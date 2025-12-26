import { Router } from 'express';
import { authenticateToken, requireRole } from '../middleware/auth.js';
import {
  listProducts,
  listPublicCatalog,
  createProduct,
  updateProduct,
  deleteProduct,
  deleteManyProducts,
  deleteAllProducts,
  getProductPriceHistory,
  exportProductsCsv,
  importProductsCsv
} from '../controllers/productController.js';

const router = Router();

// Catálogo público (limitado)
router.get('/public', listPublicCatalog);

router.use(authenticateToken);

router.get('/', listProducts);
router.get('/export/csv', requireRole('admin', 'operador'), exportProductsCsv);
router.post('/import/csv', requireRole('admin', 'operador'), importProductsCsv);
router.post('/delete-many', requireRole('admin'), deleteManyProducts);
router.delete('/all', requireRole('admin'), deleteAllProducts);
router.post('/', requireRole('admin', 'operador'), createProduct);
router.get('/:id/history', requireRole('admin', 'operador'), getProductPriceHistory);
router.put('/:id', requireRole('admin', 'operador'), updateProduct);
router.delete('/:id', requireRole('admin'), deleteProduct);

export default router;
