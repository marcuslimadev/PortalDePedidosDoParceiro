import { Router } from 'express';
import { authenticateToken, requireRole } from '../middleware/auth.js';
import { createOrder, listOrders } from '../controllers/orderController.js';

const router = Router();

router.use(authenticateToken);

router.get('/', listOrders);
router.post('/', requireRole('loja'), createOrder);

export default router;
