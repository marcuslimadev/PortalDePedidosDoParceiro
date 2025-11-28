import { Router } from 'express';
import { authenticateToken, requireRole } from '../middleware/auth.js';
import { exportApprovedOrders, importClients, importProducts, listSyncLogs } from '../services/winthorService.js';

const router = Router();

router.use(authenticateToken, requireRole('admin', 'operador'));

router.post('/export-orders', async (req, res) => {
  try {
    const payload = await exportApprovedOrders({
      fromDate: req.body?.fromDate || null,
      toDate: req.body?.toDate || null
    });
    res.json({ orders: payload });
  } catch (error) {
    console.error('Export orders error:', error);
    res.status(500).json({ error: 'Falha ao exportar pedidos' });
  }
});

router.post('/import-products', async (req, res) => {
  try {
    const result = await importProducts(req.body?.rows || []);
    res.json(result);
  } catch (error) {
    console.error('Import products error:', error);
    res.status(400).json({ error: error.message || 'Falha ao importar produtos' });
  }
});

router.post('/import-clients', async (req, res) => {
  try {
    const result = await importClients(req.body?.rows || []);
    res.json(result);
  } catch (error) {
    console.error('Import clients error:', error);
    res.status(400).json({ error: error.message || 'Falha ao importar clientes' });
  }
});

router.get('/logs', async (req, res) => {
  try {
    const logs = await listSyncLogs({ limit: Number(req.query.limit) || 50 });
    res.json({ logs });
  } catch (error) {
    console.error('List logs error:', error);
    res.status(500).json({ error: 'Falha ao listar logs de sincronização' });
  }
});

export default router;
