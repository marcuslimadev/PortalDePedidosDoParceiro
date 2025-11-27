import { Router } from 'express';
import { authenticateToken } from '../middleware/auth.js';
import { listNotifications, markAllAsRead, markAsRead } from '../controllers/notificationController.js';

const router = Router();

router.use(authenticateToken);

router.get('/', listNotifications);
router.post('/read-all', markAllAsRead);
router.patch('/:id/read', markAsRead);

export default router;
