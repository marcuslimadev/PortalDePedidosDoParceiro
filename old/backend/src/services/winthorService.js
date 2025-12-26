import { query } from '../config/database.js';

const startLog = async (type, details = null) => {
  const result = await query(
    'INSERT INTO winthor_sync_logs (type, status, details) VALUES ($1, \'running\', $2) RETURNING id, started_at',
    [type, details ? JSON.stringify(details) : null]
  );
  return result.rows[0].id;
};

const finishLog = async (id, status, message = null, details = null) => {
  await query(
    'UPDATE winthor_sync_logs SET status = $1, message = $2, details = COALESCE($3, details), finished_at = NOW() WHERE id = $4',
    [status, message, details ? JSON.stringify(details) : null, id]
  );
};

export const exportApprovedOrders = async ({ fromDate = null, toDate = null } = {}) => {
  const logId = await startLog('export_orders', { fromDate, toDate });
  try {
    const params = [];
    const conditions = ["status = 'aprovado'"];

    if (fromDate) { params.push(fromDate); conditions.push(`created_at >= $${params.length}`); }
    if (toDate) { params.push(toDate); conditions.push(`created_at < $${params.length} + INTERVAL '1 day'`); }

    const where = conditions.length ? 'WHERE ' + conditions.join(' AND ') : '';
    const orders = await query(
      `SELECT id, loja_id, payment_terms, subtotal, discount, total, created_at
         FROM orders ${where}
        ORDER BY created_at DESC`,
      params
    );

    const items = await query(
      `SELECT oi.order_id, oi.product_id, p.codigo, p.descricao, oi.quantidade, oi.preco_unitario, oi.subtotal
         FROM order_items oi
         JOIN products p ON p.id = oi.product_id
        WHERE oi.order_id = ANY($1::int[])`,
      [orders.rows.map(o => o.id)]
    );

    const itemsByOrder = items.rows.reduce((acc, row) => {
      (acc[row.order_id] = acc[row.order_id] || []).push(row);
      return acc;
    }, {});

    const payload = orders.rows.map(o => ({
      ...o,
      items: itemsByOrder[o.id] || []
    }));

    await finishLog(logId, 'success', 'Export complete', { count: payload.length });
    return payload;
  } catch (err) {
    await finishLog(logId, 'failure', err.message);
    throw err;
  }
};

export const importProducts = async (rows) => {
  const logId = await startLog('import_products', { count: rows?.length || 0 });
  try {
    // Mock: apenas validação básica
    if (!Array.isArray(rows)) throw new Error('Invalid payload');
    await finishLog(logId, 'success', 'Products validated', { count: rows.length });
    return { imported: rows.length };
  } catch (err) {
    await finishLog(logId, 'failure', err.message);
    throw err;
  }
};

export const importClients = async (rows) => {
  const logId = await startLog('import_clients', { count: rows?.length || 0 });
  try {
    if (!Array.isArray(rows)) throw new Error('Invalid payload');
    await finishLog(logId, 'success', 'Clients validated', { count: rows.length });
    return { imported: rows.length };
  } catch (err) {
    await finishLog(logId, 'failure', err.message);
    throw err;
  }
};

export const listSyncLogs = async ({ limit = 50 } = {}) => {
  const result = await query(
    `SELECT id, type, status, message, details, started_at, finished_at
       FROM winthor_sync_logs
      ORDER BY started_at DESC
      LIMIT $1`,
    [limit]
  );
  return result.rows;
};
