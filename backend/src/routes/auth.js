import { Router } from 'express';
import { login, register, verify } from '../controllers/authController.js';
import { authenticateToken } from '../middleware/auth.js';

const router = Router();

router.post('/login', login);
router.post('/register', register);
router.get('/verify', authenticateToken, verify);

export default router;
