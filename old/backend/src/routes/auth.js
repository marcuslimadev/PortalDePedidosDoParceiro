import { Router } from 'express';
import { login, register, verify } from '../controllers/authController.js';
import { authenticateToken } from '../middleware/auth.js';
import { loginLimiter, registrationLimiter } from '../middleware/rateLimiter.js';

const router = Router();

router.post('/login', loginLimiter, login);
router.post('/register', registrationLimiter, register);
router.get('/verify', authenticateToken, verify);

export default router;
