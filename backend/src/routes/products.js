import { Router } from 'express';
import { authenticateToken, requireRole } from '../middleware/auth.js';
import {
  listProducts,
  createProduct,
  updateProduct,
  deleteProduct
} from '../controllers/productController.js';

const router = Router();

router.use(authenticateToken);

router.get('/', listProducts);
router.post('/', requireRole('admin', 'operador'), createProduct);
router.put('/:id', requireRole('admin', 'operador'), updateProduct);
router.delete('/:id', requireRole('admin'), deleteProduct);

export default router;
