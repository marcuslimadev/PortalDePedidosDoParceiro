import { Router } from 'express';
import { authenticateToken, requireRole } from '../middleware/auth.js';
import { createOrder, listOrders, updateOrderStatus, listOpenOrders, getOrderStats } from '../controllers/orderController.js';

const router = Router();

router.use(authenticateToken);

router.get('/', listOrders);
router.get('/open', requireRole('operador', 'admin'), listOpenOrders);
router.get('/stats', requireRole('operador', 'admin'), getOrderStats);
router.post('/', requireRole('loja'), createOrder);
router.patch('/:id/status', requireRole('operador', 'admin'), updateOrderStatus);

export default router;
