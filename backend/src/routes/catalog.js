import { Router } from 'express';
import { listPublicCatalog } from '../controllers/productController.js';

const router = Router();

router.get('/', listPublicCatalog);

export default router;
