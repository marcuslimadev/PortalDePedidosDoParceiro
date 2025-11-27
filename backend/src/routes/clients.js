import { Router } from 'express';
import { authenticateToken, requireRole } from '../middleware/auth.js';
import { getClient, listClientHistory, listClients, setClientAccess, updateClient } from '../controllers/clientController.js';

const router = Router();

router.use(authenticateToken, requireRole('admin', 'operador'));

router.get('/', listClients);
router.get('/:id/history', listClientHistory);
router.put('/:id/access', setClientAccess);
router.get('/:id', getClient);
router.put('/:id', updateClient);

export default router;
