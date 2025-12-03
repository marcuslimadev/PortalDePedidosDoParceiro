import { getClient, query } from '../config/database.js';
import { eventBus } from '../events/eventBus.js';
import {
  allowedPaymentTerms,
  getPaymentTerms,
  calculateOrderTotals
} from '../services/paymentService.js';

const validateItems = (items) => {
  if (!Array.isArray(items) || items.length === 0) {
    return 'Inclua ao menos um item no pedido';
  }

  const invalid = items.find(item => !item.productId || !item.quantidade || Number(item.quantidade) <= 0);
  if (invalid) {
    return 'Itens precisam de productId e quantidade maior que zero';
  }

  return null;
};

export const createOrder = async (req, res) => {
  const validationError = validateItems(req.body.items);
  if (validationError) {
    return res.status(400).json({ error: validationError });
  }

  const { items, paymentTerms = null, observations = null } = req.body;
  const client = await getClient();

  try {
    await client.query('BEGIN');

    const fail = async (status, message) => {
      await client.query('ROLLBACK');
      return res.status(status).json({ error: message });
    };

    const lojaResult = await client.query(
      `SELECT nome, cliente_status, credit_limit, credit_used, payment_terms
         FROM users
        WHERE id = $1
        FOR UPDATE`,
      [req.user.id]
    );

    if (lojaResult.rows.length === 0) {
      return fail(404, 'Perfil da loja não encontrado');
    }

    const lojaPerfil = lojaResult.rows[0];

    if (lojaPerfil.cliente_status && lojaPerfil.cliente_status !== 'ativo') {
      return fail(403, 'Cliente sem permissão para registrar pedidos');
    }

    const productIds = items.map(item => item.productId);
    const placeholders = productIds.map((_, index) => `$${index + 1}`).join(',');
    const productsResult = await client.query(
      `SELECT id, codigo, descricao, preco, estoque FROM products WHERE id IN (${placeholders})`,
      productIds
    );

    const productsMap = new Map(productsResult.rows.map(product => [product.id, product]));

    for (const item of items) {
      const product = productsMap.get(item.productId);
      if (!product) {
        throw new Error(`Produto ${item.productId} não encontrado`);
      }

      if (product.estoque !== null && Number(item.quantidade) > Number(product.estoque)) {
        return fail(400, `Quantidade solicitada excede o estoque para ${product.codigo}`);
      }
    }

    let subtotalValue = 0;
    for (const item of items) {
      const product = productsMap.get(item.productId);
      const subtotal = Number(product.preco) * Number(item.quantidade);
      subtotalValue += subtotal;
    }

    // Aplicar condições de pagamento e calcular descontos
    const finalPaymentTerms = getPaymentTerms(paymentTerms, lojaPerfil.payment_terms);

    if (!allowedPaymentTerms.includes(finalPaymentTerms)) {
      return fail(400, 'Condição de pagamento inválida');
    }

    const orderTotals = calculateOrderTotals(subtotalValue, finalPaymentTerms);

    // Validar limite de crédito com valor final (após desconto)
    if (lojaPerfil.credit_limit !== null) {
      const atual = Number(lojaPerfil.credit_used || 0);
      const limite = Number(lojaPerfil.credit_limit);
      if (atual + orderTotals.total > limite) {
        return fail(400, 'Limite de crédito excedido para a loja');
      }
    }

    const orderResult = await client.query(
      `INSERT INTO orders (loja_id, payment_terms, observations, subtotal, discount, discount_percentage, total)
       VALUES ($1, $2, $3, $4, $5, $6, $7)
       RETURNING id, status, payment_terms, observations, subtotal, discount, discount_percentage, total, created_at, updated_at`,
      [req.user.id, finalPaymentTerms, observations, orderTotals.subtotal, orderTotals.discount, orderTotals.discountPercentage, orderTotals.total]
    );

    const order = orderResult.rows[0];

    for (const item of items) {
      const product = productsMap.get(item.productId);
      const subtotal = Number(product.preco) * Number(item.quantidade);

      await client.query(
        `INSERT INTO order_items (order_id, product_id, quantidade, preco_unitario, subtotal)
         VALUES ($1, $2, $3, $4, $5)`,
        [order.id, product.id, item.quantidade, product.preco, subtotal]
      );

      await client.query(
        'UPDATE products SET estoque = estoque - $1, updated_at = NOW() WHERE id = $2',
        [item.quantidade, product.id]
      );
    }

    await client.query(
      `UPDATE users
          SET credit_used = COALESCE(credit_used, 0) + $1,
              updated_at = NOW()
        WHERE id = $2`,
      [orderTotals.total, req.user.id]
    );

    await client.query('COMMIT');

    const orderItems = items.map(item => {
      const product = productsMap.get(item.productId);
      const subtotal = Number(product.preco) * Number(item.quantidade);
      return {
        product_id: product.id,
        codigo: product.codigo,
        descricao: product.descricao,
        quantidade: item.quantidade,
        preco_unitario: product.preco,
        subtotal
      };
    });

    const response = {
      ...order,
      loja_id: req.user.id,
      loja_nome: lojaPerfil.nome,
      items: orderItems
    };

    eventBus.emit('order-event', {
      type: 'order.created',
      lojaId: req.user.id,
      payload: response
    });

    res.status(201).json({ order: response });
  } catch (error) {
    await client.query('ROLLBACK');
    console.error('Erro ao criar pedido:', error);
    res.status(500).json({ error: 'Erro ao registrar o pedido' });
  } finally {
    client.release();
  }
};

export const listOrders = async (req, res) => {
  try {
    const isLoja = req.user.role === 'loja';
    const params = [];
    const filters = [];

    const limitParam = Number(req.query.limit) || 50;
    const limit = Math.min(Math.max(limitParam, 1), 500);
    const pageParam = Number(req.query.page) || 1;
    const page = Math.max(pageParam, 1);
    const offset = (page - 1) * limit;
    const statusFilter = req.query.status;
    const fromDate = req.query.from;
    const toDate = req.query.to;

    if (isLoja) {
      params.push(req.user.id);
      filters.push(`o.loja_id = $${params.length}`);
    }

    if (statusFilter) {
      params.push(statusFilter);
      filters.push(`o.status = $${params.length}`);
    }

    if (fromDate) {
      params.push(fromDate);
      filters.push(`o.created_at >= $${params.length}`);
    }

    if (toDate) {
      params.push(toDate);
      filters.push(`o.created_at <= $${params.length}`);
    }

    const baseQuery = `
      WITH filtered AS (
        SELECT o.id, o.loja_id, o.status, o.payment_terms, o.observations, o.total, o.created_at, o.updated_at,
               u.nome AS loja_nome
          FROM orders o
          JOIN users u ON u.id = o.loja_id
         ${filters.length ? 'WHERE ' + filters.join(' AND ') : ''}
         ORDER BY o.created_at DESC
      )
      SELECT *, COUNT(*) OVER() AS total_count
        FROM filtered
       LIMIT $${params.length + 1}
      OFFSET $${params.length + 2}`;

    params.push(limit, offset);

    const ordersResult = await query(baseQuery, params);
    const orders = ordersResult.rows;

    if (orders.length === 0) {
      return res.json({ orders: [], meta: { total: 0, page, pages: 0, limit } });
    }

    const orderIds = orders.map(order => order.id);
    const placeholders = orderIds.map((_, index) => `$${index + 1}`).join(',');
    const itemsResult = await query(
      `SELECT oi.order_id, oi.product_id, oi.quantidade, oi.preco_unitario, oi.subtotal, p.codigo, p.descricao
       FROM order_items oi
       JOIN products p ON p.id = oi.product_id
       WHERE oi.order_id IN (${placeholders})`,
      orderIds
    );

    const itemsByOrder = itemsResult.rows.reduce((acc, item) => {
      acc[item.order_id] = acc[item.order_id] || [];
      acc[item.order_id].push(item);
      return acc;
    }, {});

    const total = Number(orders[0]?.total_count) || 0;
    const pages = total === 0 ? 0 : Math.max(1, Math.ceil(total / limit));

    const response = orders.map(order => ({
      ...order,
      items: itemsByOrder[order.id] || []
    }));

    res.json({
      orders: response,
      meta: {
        total,
        page,
        pages,
        limit
      }
    });
  } catch (error) {
    console.error('Erro ao listar pedidos:', error);
    res.status(500).json({ error: 'Erro ao buscar pedidos' });
  }
};

export const updateOrderStatus = async (req, res) => {
  const { id } = req.params;
  const { status } = req.body;

  const allowedStatus = ['pendente', 'aprovado', 'em_separacao', 'faturado', 'cancelado'];
  if (!allowedStatus.includes(status)) {
    return res.status(400).json({ error: 'Status inválido' });
  }

  try {
    const updatedOrder = await query(
      `UPDATE orders
       SET status = $1, updated_at = NOW()
       WHERE id = $2
       RETURNING id, loja_id, status, payment_terms, observations, total, created_at, updated_at`,
      [status, id]
    );

    if (updatedOrder.rowCount === 0) {
      return res.status(404).json({ error: 'Pedido não encontrado' });
    }

    const order = updatedOrder.rows[0];
    const lojaResult = await query('SELECT nome FROM users WHERE id = $1', [order.loja_id]);
    const itemsResult = await query(
      `SELECT oi.product_id, oi.quantidade, oi.preco_unitario, oi.subtotal, p.codigo, p.descricao
       FROM order_items oi
       JOIN products p ON p.id = oi.product_id
       WHERE oi.order_id = $1`,
      [id]
    );

    const response = {
      ...order,
      loja_nome: lojaResult.rows[0]?.nome || null,
      items: itemsResult.rows
    };

    eventBus.emit('order-event', {
      type: 'order.status_updated',
      lojaId: order.loja_id,
      payload: response
    });

    res.json({ order: response });
  } catch (error) {
    console.error('Erro ao atualizar status do pedido:', error);
    res.status(500).json({ error: 'Falha ao atualizar status do pedido' });
  }
};

const minutesSince = (date) => {
  if (!date) return null;
  const diff = Date.now() - new Date(date).getTime();
  if (Number.isNaN(diff)) return null;
  return Math.max(0, Math.floor(diff / 60000));
};

export const openOrdersSummary = async (req, res) => {
  try {
    const [summaryResult, byStatusResult, queueResult, agingResult] = await Promise.all([
      query(
        `SELECT COUNT(*) AS total_open,
                COALESCE(SUM(total), 0) AS total_value,
                MIN(created_at) AS oldest_created_at
           FROM orders
          WHERE status = 'pendente'`
      ),
      query(
        `SELECT status,
                COUNT(*) AS count,
                COALESCE(SUM(total), 0) AS total_value,
                MIN(created_at) AS oldest_created_at
           FROM orders
          WHERE status != 'cancelado'
       GROUP BY status`
      ),
      query(
        `SELECT o.id,
                o.status,
                o.total,
                o.created_at,
                u.nome AS loja_nome
           FROM orders o
           JOIN users u ON u.id = o.loja_id
          WHERE o.status = 'pendente'
       ORDER BY o.created_at ASC
          LIMIT 20`
      ),
      query(
        `SELECT
            COUNT(*) FILTER (WHERE NOW() - created_at <= INTERVAL '2 hours') AS up_to_2h,
            COALESCE(SUM(total) FILTER (WHERE NOW() - created_at <= INTERVAL '2 hours'), 0) AS value_up_to_2h,
            COUNT(*) FILTER (WHERE NOW() - created_at > INTERVAL '2 hours' AND NOW() - created_at <= INTERVAL '6 hours') AS between_2_6h,
            COALESCE(SUM(total) FILTER (WHERE NOW() - created_at > INTERVAL '2 hours' AND NOW() - created_at <= INTERVAL '6 hours'), 0) AS value_between_2_6h,
            COUNT(*) FILTER (WHERE NOW() - created_at > INTERVAL '6 hours' AND NOW() - created_at <= INTERVAL '24 hours') AS between_6_24h,
            COALESCE(SUM(total) FILTER (WHERE NOW() - created_at > INTERVAL '6 hours' AND NOW() - created_at <= INTERVAL '24 hours'), 0) AS value_between_6_24h,
            COUNT(*) FILTER (WHERE NOW() - created_at > INTERVAL '24 hours') AS over_24h,
            COALESCE(SUM(total) FILTER (WHERE NOW() - created_at > INTERVAL '24 hours'), 0) AS value_over_24h
          FROM orders
         WHERE status = 'pendente'`
      )
    ]);

    const summaryRow = summaryResult.rows[0] || {};
    const agingRow = agingResult.rows[0] || {};

    const summary = {
      totalOpen: Number(summaryRow.total_open) || 0,
      totalValue: Number(summaryRow.total_value) || 0,
      oldestMinutes: minutesSince(summaryRow.oldest_created_at)
    };

    const byStatus = byStatusResult.rows.map(row => ({
      status: row.status,
      count: Number(row.count) || 0,
      totalValue: Number(row.total_value) || 0,
      oldestMinutes: minutesSince(row.oldest_created_at)
    }));

    const aging = [
      { label: '0-2h', count: Number(agingRow.up_to_2h) || 0, totalValue: Number(agingRow.value_up_to_2h) || 0 },
      { label: '2-6h', count: Number(agingRow.between_2_6h) || 0, totalValue: Number(agingRow.value_between_2_6h) || 0 },
      { label: '6-24h', count: Number(agingRow.between_6_24h) || 0, totalValue: Number(agingRow.value_between_6_24h) || 0 },
      { label: '24h+', count: Number(agingRow.over_24h) || 0, totalValue: Number(agingRow.value_over_24h) || 0 }
    ];

    const queue = queueResult.rows.map(row => ({
      id: row.id,
      loja_nome: row.loja_nome,
      status: row.status,
      total: Number(row.total) || 0,
      created_at: row.created_at,
      waitingMinutes: minutesSince(row.created_at)
    }));

    res.json({
      summary,
      byStatus,
      aging,
      queue,
      updatedAt: new Date().toISOString()
    });
  } catch (error) {
    console.error('Erro ao gerar resumo de pedidos em aberto:', error);
    res.status(500).json({ error: 'Não foi possível carregar o dashboard de pedidos' });
  }
};

export const repeatOrder = async (req, res) => {
  const { id } = req.params;

  try {
    const orderResult = await query(
      `SELECT id, loja_id, payment_terms
         FROM orders
        WHERE id = $1`,
      [id]
    );

    if (orderResult.rows.length === 0) {
      return res.status(404).json({ error: 'Pedido não encontrado' });
    }

    const order = orderResult.rows[0];

    if (req.user.role === 'loja' && order.loja_id !== req.user.id) {
      return res.status(403).json({ error: 'Você não pode repetir pedidos de outra loja' });
    }

    const itemsResult = await query(
      `SELECT product_id, quantidade
         FROM order_items
        WHERE order_id = $1`,
      [id]
    );

    if (itemsResult.rows.length === 0) {
      return res.status(400).json({ error: 'Pedido selecionado não possui itens para repetir' });
    }

    req.body = {
      items: itemsResult.rows.map(item => ({
        productId: item.product_id,
        quantidade: item.quantidade
      })),
      paymentTerms: req.body?.paymentTerms || order.payment_terms,
      observations: req.body?.observations || `Repetição automática do pedido #${order.id}`
    };

    return createOrder(req, res);
  } catch (error) {
    console.error('Erro ao repetir pedido:', error);
    return res.status(500).json({ error: 'Não foi possível repetir o pedido' });
  }
};

export const exportOrdersCsv = async (req, res) => {
  try {
    const limitParam = Number(req.query.limit) || 200;
    const limit = Math.min(Math.max(limitParam, 1), 1000);

    const ordersResult = await query(
      `SELECT o.id, o.loja_id, o.status, o.payment_terms, o.observations, o.total, o.created_at, o.updated_at,
              u.nome AS loja_nome, u.email AS loja_email
         FROM orders o
         JOIN users u ON u.id = o.loja_id
        ORDER BY o.created_at DESC
        LIMIT $1`,
      [limit]
    );

    const orders = ordersResult.rows;

    if (orders.length === 0) {
      res.setHeader('Content-Type', 'text/csv; charset=utf-8');
      res.setHeader('Content-Disposition', 'attachment; filename=pedidos.csv');
      return res.send('id;loja;email;status;total;pagamento;criado_em;atualizado_em;itens');
    }

    const orderIds = orders.map(order => order.id);
    const placeholders = orderIds.map((_, index) => `$${index + 1}`).join(',');
    const itemsResult = await query(
      `SELECT oi.order_id, oi.product_id, oi.quantidade, p.codigo, p.descricao
         FROM order_items oi
         JOIN products p ON p.id = oi.product_id
        WHERE oi.order_id IN (${placeholders})`,
      orderIds
    );

    const itemsByOrder = itemsResult.rows.reduce((acc, item) => {
      acc[item.order_id] = acc[item.order_id] || [];
      acc[item.order_id].push(item);
      return acc;
    }, {});

    const header = ['id', 'loja', 'email', 'status', 'total', 'pagamento', 'criado_em', 'atualizado_em', 'itens'];
    const rows = orders.map(order => {
      const items = (itemsByOrder[order.id] || []).map(item => `${item.quantidade}x ${item.codigo}`).join(' | ');
      return [
        order.id,
        order.loja_nome,
        order.loja_email,
        order.status,
        Number(order.total).toFixed(2),
        order.payment_terms || '',
        order.created_at.toISOString(),
        order.updated_at.toISOString(),
        items
      ];
    });

    const csv = [header, ...rows]
      .map(columns => columns
        .map(column => {
          if (column === null || column === undefined) return '';
          const value = String(column).replace(/"/g, '""');
          if (value.includes(';') || value.includes('\n') || value.includes('"')) {
            return `"${value}"`;
          }
          return value;
        })
        .join(';'))
      .join('\n');

    res.setHeader('Content-Type', 'text/csv; charset=utf-8');
    res.setHeader('Content-Disposition', `attachment; filename=pedidos-${Date.now()}.csv`);
    res.send('\ufeff' + csv);
  } catch (error) {
    console.error('Erro ao exportar pedidos:', error);
    res.status(500).json({ error: 'Não foi possível exportar os pedidos' });
  }
};

export const getOrderById = async (req, res) => {
  const { id } = req.params;
  const isLoja = req.user.role === 'loja';

  try {
    let orderQuery = `
      SELECT o.id, o.loja_id, o.status, o.payment_terms, o.observations, 
             o.subtotal, o.discount, o.discount_percentage, o.total, 
             o.created_at, o.updated_at,
             u.nome AS loja_nome, u.email AS loja_email
        FROM orders o
        JOIN users u ON u.id = o.loja_id
       WHERE o.id = $1
    `;
    const params = [id];

    if (isLoja) {
      orderQuery += ` AND o.loja_id = $2`;
      params.push(req.user.id);
    }

    const orderResult = await query(orderQuery, params);

    if (orderResult.rows.length === 0) {
      return res.status(404).json({ error: 'Pedido não encontrado' });
    }

    const order = orderResult.rows[0];

    const itemsResult = await query(
      `SELECT oi.id, oi.product_id, oi.quantidade, oi.preco_unitario, oi.subtotal,
              p.codigo AS produto_codigo, p.descricao AS produto_descricao
         FROM order_items oi
         JOIN products p ON p.id = oi.product_id
        WHERE oi.order_id = $1`,
      [id]
    );

    res.json({
      ...order,
      items: itemsResult.rows
    });
  } catch (error) {
    console.error('Erro ao buscar pedido:', error);
    res.status(500).json({ error: 'Erro ao buscar pedido' });
  }
};

export const cancelOrder = async (req, res) => {
  const { id } = req.params;
  const { motivo } = req.body;

  if (!motivo || motivo.trim().length < 3) {
    return res.status(400).json({ error: 'Informe um motivo válido para o cancelamento' });
  }

  const client = await getClient();

  try {
    await client.query('BEGIN');

    const orderResult = await client.query(
      `SELECT o.id, o.loja_id, o.status, o.total, u.nome AS loja_nome
         FROM orders o
         JOIN users u ON u.id = o.loja_id
        WHERE o.id = $1
        FOR UPDATE`,
      [id]
    );

    if (orderResult.rows.length === 0) {
      await client.query('ROLLBACK');
      return res.status(404).json({ error: 'Pedido não encontrado' });
    }

    const order = orderResult.rows[0];

    if (order.status === 'cancelado') {
      await client.query('ROLLBACK');
      return res.status(400).json({ error: 'Pedido já está cancelado' });
    }

    if (order.status === 'faturado') {
      await client.query('ROLLBACK');
      return res.status(400).json({ error: 'Não é possível cancelar pedido já faturado' });
    }

    // Restaurar estoque dos itens
    const itemsResult = await client.query(
      `SELECT product_id, quantidade FROM order_items WHERE order_id = $1`,
      [id]
    );

    for (const item of itemsResult.rows) {
      await client.query(
        `UPDATE products SET estoque = estoque + $1, updated_at = NOW() WHERE id = $2`,
        [item.quantidade, item.product_id]
      );
    }

    // Restaurar crédito do cliente
    await client.query(
      `UPDATE users SET credit_used = GREATEST(0, COALESCE(credit_used, 0) - $1), updated_at = NOW() WHERE id = $2`,
      [order.total, order.loja_id]
    );

    // Atualizar status do pedido
    await client.query(
      `UPDATE orders SET status = 'cancelado', observations = COALESCE(observations, '') || E'\n[CANCELADO] ' || $1, updated_at = NOW() WHERE id = $2`,
      [motivo, id]
    );

    await client.query('COMMIT');

    eventBus.emit('order-event', {
      type: 'order.cancelled',
      lojaId: order.loja_id,
      payload: { orderId: id, motivo, cancelledBy: req.user.email },
      motivo
    });

    res.json({ message: 'Pedido cancelado com sucesso', orderId: id, motivo });
  } catch (error) {
    await client.query('ROLLBACK');
    console.error('Erro ao cancelar pedido:', error);
    res.status(500).json({ error: 'Erro ao cancelar pedido' });
  } finally {
    client.release();
  }
};
