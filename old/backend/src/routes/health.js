import { Router } from 'express';
import { query } from '../config/database.js';

const router = Router();

router.get('/health', async (req, res) => {
  try {
    const dbCheck = await query('SELECT NOW() as now');
    res.json({
      status: 'ok',
      databaseTime: dbCheck.rows[0].now
    });
  } catch (error) {
    res.status(500).json({ status: 'error', message: error.message });
  }
});

export default router;
