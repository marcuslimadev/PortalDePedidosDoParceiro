import { Router } from 'express';
import { authenticateToken, requireRole } from '../middleware/auth.js';
import { createOrder, listOrders, updateOrderStatus } from '../controllers/orderController.js';

const router = Router();

router.use(authenticateToken);

router.get('/', listOrders);
router.post('/', requireRole('loja'), createOrder);
router.patch('/:id/status', requireRole('operador', 'admin'), updateOrderStatus);

export default router;
