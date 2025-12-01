import { Router } from 'express';
import { authenticateToken, requireRole } from '../middleware/auth.js';
import { query } from '../config/database.js';

const router = Router();

router.use(authenticateToken, requireRole('admin', 'operador'));

/**
 * Curva ABC de produtos
 * Analisa produtos por volume de vendas (quantidade ou valor)
 * GET /api/reports/abc/products?period=30&type=value
 */
router.get('/abc/products', async (req, res) => {
  try {
    const period = parseInt(req.query.period) || 90; // dias
    const type = req.query.type || 'value'; // 'value' ou 'quantity'

    const orderByClause = type === 'quantity'
      ? 'SUM(oi.quantidade)'
      : 'SUM(oi.subtotal)';

    const result = await query(
      `WITH product_sales AS (
        SELECT 
          p.id,
          p.codigo,
          p.descricao,
          p.categoria,
          p.preco,
          SUM(oi.quantidade) as total_quantity,
          SUM(oi.subtotal) as total_value,
          COUNT(DISTINCT o.id) as order_count
        FROM products p
        LEFT JOIN order_items oi ON oi.product_id = p.id
        LEFT JOIN orders o ON o.id = oi.order_id 
          AND o.created_at >= NOW() - INTERVAL '${period} days'
          AND o.status != 'cancelado'
        GROUP BY p.id, p.codigo, p.descricao, p.categoria, p.preco
      ),
      ranked_products AS (
        SELECT 
          *,
          SUM(total_value) OVER () as grand_total_value,
          SUM(total_quantity) OVER () as grand_total_quantity,
          SUM(total_value) OVER (ORDER BY ${orderByClause} DESC) as cumulative_value,
          SUM(total_quantity) OVER (ORDER BY ${orderByClause} DESC) as cumulative_quantity
        FROM product_sales
      )
      SELECT 
        id,
        codigo,
        descricao,
        categoria,
        preco,
        total_quantity,
        total_value,
        order_count,
        ROUND((total_value / NULLIF(grand_total_value, 0) * 100)::numeric, 2) as value_percentage,
        ROUND((cumulative_value / NULLIF(grand_total_value, 0) * 100)::numeric, 2) as cumulative_percentage,
        CASE 
          WHEN ROUND((cumulative_value / NULLIF(grand_total_value, 0) * 100)::numeric, 2) <= 80 THEN 'A'
          WHEN ROUND((cumulative_value / NULLIF(grand_total_value, 0) * 100)::numeric, 2) <= 95 THEN 'B'
          ELSE 'C'
        END as abc_class
      FROM ranked_products
      ORDER BY ${orderByClause} DESC
      `,
      []
    );

    const summary = {
      A: result.rows.filter(r => r.abc_class === 'A').length,
      B: result.rows.filter(r => r.abc_class === 'B').length,
      C: result.rows.filter(r => r.abc_class === 'C').length,
      total: result.rows.length
    };

    res.json({
      products: result.rows,
      summary,
      period,
      type
    });
  } catch (error) {
    console.error('Erro ao gerar curva ABC de produtos:', error);
    res.status(500).json({ error: 'Erro ao gerar relatório' });
  }
});

/**
 * Curva ABC de clientes
 * Analisa clientes por volume de compras
 * GET /api/reports/abc/clients?period=90
 */
router.get('/abc/clients', async (req, res) => {
  try {
    const period = parseInt(req.query.period) || 90; // dias

    const result = await query(
      `WITH client_sales AS (
        SELECT 
          u.id,
          u.nome,
          u.email,
          u.cnpj,
          u.rota,
          u.segmentacao,
          u.credit_limit,
          u.credit_used,
          COUNT(o.id) as order_count,
          SUM(o.total) as total_value,
          AVG(o.total) as avg_order_value,
          MAX(o.created_at) as last_order_date
        FROM users u
        LEFT JOIN orders o ON o.loja_id = u.id 
          AND o.created_at >= NOW() - INTERVAL '${period} days'
          AND o.status != 'cancelado'
        WHERE u.role = 'loja' AND u.ativo = true
        GROUP BY u.id, u.nome, u.email, u.cnpj, u.rota, u.segmentacao, u.credit_limit, u.credit_used
      ),
      ranked_clients AS (
        SELECT 
          *,
          SUM(total_value) OVER () as grand_total,
          SUM(total_value) OVER (ORDER BY total_value DESC NULLS LAST) as cumulative_value
        FROM client_sales
      )
      SELECT 
        id,
        nome,
        email,
        cnpj,
        rota,
        segmentacao,
        credit_limit,
        credit_used,
        order_count,
        COALESCE(total_value, 0) as total_value,
        COALESCE(avg_order_value, 0) as avg_order_value,
        last_order_date,
        ROUND((COALESCE(total_value, 0) / NULLIF(grand_total, 0) * 100)::numeric, 2) as value_percentage,
        ROUND((cumulative_value / NULLIF(grand_total, 0) * 100)::numeric, 2) as cumulative_percentage,
        CASE 
          WHEN ROUND((cumulative_value / NULLIF(grand_total, 0) * 100)::numeric, 2) <= 80 THEN 'A'
          WHEN ROUND((cumulative_value / NULLIF(grand_total, 0) * 100)::numeric, 2) <= 95 THEN 'B'
          ELSE 'C'
        END as abc_class
      FROM ranked_clients
      ORDER BY total_value DESC NULLS LAST
      `,
      []
    );

    const summary = {
      A: result.rows.filter(r => r.abc_class === 'A').length,
      B: result.rows.filter(r => r.abc_class === 'B').length,
      C: result.rows.filter(r => r.abc_class === 'C').length,
      total: result.rows.length,
      totalRevenue: result.rows.reduce((sum, r) => sum + parseFloat(r.total_value || 0), 0)
    };

    res.json({
      clients: result.rows,
      summary,
      period
    });
  } catch (error) {
    console.error('Erro ao gerar curva ABC de clientes:', error);
    res.status(500).json({ error: 'Erro ao gerar relatório' });
  }
});

/**
 * Relatório de vendas por período
 * GET /api/reports/sales?startDate=2025-01-01&endDate=2025-01-31&groupBy=day
 */
router.get('/sales', async (req, res) => {
  try {
    const { startDate, endDate, groupBy = 'day' } = req.query;

    const dateFormat = {
      day: 'YYYY-MM-DD',
      week: 'YYYY-"W"IW',
      month: 'YYYY-MM'
    }[groupBy] || 'YYYY-MM-DD';

    const result = await query(
      `SELECT 
        TO_CHAR(created_at, $1) as period,
        COUNT(*) as order_count,
        SUM(total) as total_value,
        AVG(total) as avg_order_value,
        SUM(discount) as total_discount
      FROM orders
      WHERE created_at >= $2 
        AND created_at < $3 + INTERVAL '1 day'
        AND status != 'cancelado'
      GROUP BY period
      ORDER BY period
      `,
      [dateFormat, startDate, endDate]
    );

    res.json({
      sales: result.rows,
      groupBy,
      startDate,
      endDate
    });
  } catch (error) {
    console.error('Erro ao gerar relatório de vendas:', error);
    res.status(500).json({ error: 'Erro ao gerar relatório' });
  }
});

/**
 * Dashboard com KPIs gerais
 * GET /api/reports/dashboard
 */
router.get('/dashboard', async (req, res) => {
  try {
    const [ordersStats, revenueStats, topProducts, topClients] = await Promise.all([
      // Estatísticas de pedidos
      query(`
        SELECT 
          COUNT(*) FILTER (WHERE status = 'pendente') as pending_orders,
          COUNT(*) FILTER (WHERE status = 'aprovado') as approved_orders,
          COUNT(*) FILTER (WHERE status = 'cancelado') as cancelled_orders,
          COUNT(*) as total_orders
        FROM orders
        WHERE created_at >= NOW() - INTERVAL '30 days'
      `),

      // Estatísticas de receita
      query(`
        SELECT 
          SUM(total) FILTER (WHERE created_at >= NOW() - INTERVAL '30 days' AND status != 'cancelado') as revenue_30d,
          SUM(total) FILTER (WHERE created_at >= NOW() - INTERVAL '7 days' AND status != 'cancelado') as revenue_7d,
          AVG(total) FILTER (WHERE status != 'cancelado') as avg_order_value
        FROM orders
      `),

      // Top 5 produtos
      query(`
        SELECT 
          p.codigo,
          p.descricao,
          SUM(oi.quantidade) as quantity,
          SUM(oi.subtotal) as value
        FROM order_items oi
        JOIN products p ON p.id = oi.product_id
        JOIN orders o ON o.id = oi.order_id
        WHERE o.created_at >= NOW() - INTERVAL '30 days' AND o.status != 'cancelado'
        GROUP BY p.id, p.codigo, p.descricao
        ORDER BY value DESC
        LIMIT 5
      `),

      // Top 5 clientes
      query(`
        SELECT 
          u.nome,
          COUNT(o.id) as order_count,
          SUM(o.total) as total_value
        FROM users u
        JOIN orders o ON o.loja_id = u.id
        WHERE o.created_at >= NOW() - INTERVAL '30 days' AND o.status != 'cancelado'
        GROUP BY u.id, u.nome
        ORDER BY total_value DESC
        LIMIT 5
      `)
    ]);

    res.json({
      orders: ordersStats.rows[0],
      revenue: revenueStats.rows[0],
      topProducts: topProducts.rows,
      topClients: topClients.rows
    });
  } catch (error) {
    console.error('Erro ao gerar dashboard:', error);
    res.status(500).json({ error: 'Erro ao gerar dashboard' });
  }
});

export default router;
// Vendas por loja
router.get('/sales-by-store', async (req, res) => {
  try {
    const { startDate, endDate } = req.query;

    const result = await query(
      `SELECT 
         u.id as loja_id,
         u.nome as loja_nome,
         COUNT(o.id) as order_count,
         COALESCE(SUM(o.total), 0) as total_value,
         COALESCE(SUM(o.discount), 0) as total_discount
       FROM users u
       LEFT JOIN orders o ON o.loja_id = u.id
         AND o.status != 'cancelado'
         AND o.created_at >= $1
         AND o.created_at < $2 + INTERVAL '1 day'
       WHERE u.role = 'loja' AND u.ativo = true
       GROUP BY u.id, u.nome
       ORDER BY total_value DESC`,
      [startDate, endDate]
    );

    res.json({ stores: result.rows, startDate, endDate });
  } catch (error) {
    console.error('Erro ao gerar relatório de vendas por loja:', error);
    res.status(500).json({ error: 'Erro ao gerar relatório' });
  }
});
