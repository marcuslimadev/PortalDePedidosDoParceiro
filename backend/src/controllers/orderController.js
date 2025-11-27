import { getClient, query } from '../config/database.js';

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
        return res.status(400).json({ error: `Quantidade solicitada excede o estoque para ${product.codigo}` });
      }
    }

    let total = 0;
    for (const item of items) {
      const product = productsMap.get(item.productId);
      const subtotal = Number(product.preco) * Number(item.quantidade);
      total += subtotal;
    }

    const orderResult = await client.query(
      `INSERT INTO orders (loja_id, payment_terms, observations, total)
       VALUES ($1, $2, $3, $4)
       RETURNING id, status, payment_terms, observations, total, created_at, updated_at`,
      [req.user.id, paymentTerms, observations, total]
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

    res.status(201).json({
      order: {
        ...order,
        loja_id: req.user.id,
        items: orderItems
      }
    });
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
    let baseQuery = `
      SELECT o.id, o.loja_id, o.status, o.payment_terms, o.observations, o.total, o.created_at, o.updated_at,
             u.nome AS loja_nome
      FROM orders o
      JOIN users u ON u.id = o.loja_id`;

    if (isLoja) {
      params.push(req.user.id);
      baseQuery += ' WHERE o.loja_id = $1';
    }

    baseQuery += ' ORDER BY o.created_at DESC LIMIT 50';

    const ordersResult = await query(baseQuery, params);
    const orders = ordersResult.rows;

    if (orders.length === 0) {
      return res.json({ orders: [] });
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

    const response = orders.map(order => ({
      ...order,
      items: itemsByOrder[order.id] || []
    }));

    res.json({ orders: response });
  } catch (error) {
    console.error('Erro ao listar pedidos:', error);
    res.status(500).json({ error: 'Erro ao buscar pedidos' });
  }
};
