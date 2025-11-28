import { Router } from 'express';
import { authenticateToken, requireRole } from '../middleware/auth.js';
import { getClient, listClientHistory, listClients, setClientAccess, updateClient } from '../controllers/clientController.js';
import { auditMiddleware } from '../middleware/audit.js';

const router = Router();

router.use(authenticateToken, requireRole('admin', 'operador'));

router.get('/', listClients);
router.get('/:id/history', listClientHistory);
router.put('/:id/access', auditMiddleware('update', 'client'), setClientAccess);
router.get('/:id', getClient);
router.put('/:id', auditMiddleware('credit_update', 'client'), updateClient);

export default router;
