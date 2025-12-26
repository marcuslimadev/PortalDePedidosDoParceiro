import { Router } from 'express';
import { authenticateToken, requireRole } from '../middleware/auth.js';
import { getAuditLogs, getAuditStats } from '../services/auditService.js';

const router = Router();

router.use(authenticateToken, requireRole('admin'));

/**
 * Lista logs de auditoria com filtros
 * GET /api/audit?userId=1&action=create&limit=50
 */
router.get('/', async (req, res) => {
  try {
    const filters = {
      userId: req.query.userId ? parseInt(req.query.userId) : null,
      action: req.query.action || null,
      resourceType: req.query.resourceType || null,
      resourceId: req.query.resourceId ? parseInt(req.query.resourceId) : null,
      startDate: req.query.startDate || null,
      endDate: req.query.endDate || null,
      limit: req.query.limit ? parseInt(req.query.limit) : 100,
      offset: req.query.offset ? parseInt(req.query.offset) : 0
    };

    const logs = await getAuditLogs(filters);

    res.json({
      logs,
      total: logs.length,
      filters
    });
  } catch (error) {
    console.error('Erro ao buscar logs de auditoria:', error);
    res.status(500).json({ error: 'Erro ao buscar logs de auditoria' });
  }
});

/**
 * Obtém estatísticas de auditoria
 * GET /api/audit/stats
 */
router.get('/stats', async (req, res) => {
  try {
    const stats = await getAuditStats();
    res.json({ stats });
  } catch (error) {
    console.error('Erro ao buscar estatísticas:', error);
    res.status(500).json({ error: 'Erro ao buscar estatísticas' });
  }
});

export default router;
