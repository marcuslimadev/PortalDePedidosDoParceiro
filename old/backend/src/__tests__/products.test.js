import request from 'supertest';
import express from 'express';
import productRoutes from '../routes/products.js';
import { cleanDatabase, createTestUser, createTestProduct, generateTestToken } from './testHelpers.js';
import { authenticateToken } from '../middleware/auth.js';

// Create test app
const app = express();
app.use(express.json());
app.use('/api/products', authenticateToken, productRoutes);

describe('Products API', () => {
  let adminUser, adminToken;
  let lojaUser, lojaToken;

  beforeEach(async () => {
    await cleanDatabase();

    // Create admin user
    adminUser = await createTestUser({
      email: 'admin@test.com',
      nome: 'Admin User',
      role: 'admin'
    });
    adminToken = generateTestToken(adminUser);

    // Create loja user
    lojaUser = await createTestUser({
      email: 'loja@test.com',
      nome: 'Loja User',
      role: 'loja'
    });
    lojaToken = generateTestToken(lojaUser);
  });

  describe('GET /api/products', () => {
    it('should list all products for authenticated user', async () => {
      await createTestProduct({ nome: 'Product 1', preco: 100 });
      await createTestProduct({ nome: 'Product 2', preco: 200 });

      const response = await request(app)
        .get('/api/products')
        .set('Authorization', `Bearer ${lojaToken}`);

      expect(response.status).toBe(200);
      expect(response.body.products).toHaveLength(2);
      expect(response.body.products[0]).toHaveProperty('nome');
      expect(response.body.products[0]).toHaveProperty('preco');
    });

    it('should search products by name', async () => {
      await createTestProduct({ nome: 'Coca Cola', preco: 5 });
      await createTestProduct({ nome: 'Pepsi', preco: 4.5 });
      await createTestProduct({ nome: 'Fanta', preco: 4 });

      const response = await request(app)
        .get('/api/products?q=cola')
        .set('Authorization', `Bearer ${lojaToken}`);

      expect(response.status).toBe(200);
      expect(response.body.products).toHaveLength(1);
      expect(response.body.products[0].nome).toContain('Cola');
    });

    it('should reject unauthenticated requests', async () => {
      const response = await request(app)
        .get('/api/products');

      expect(response.status).toBe(401);
    });
  });

  describe('POST /api/products', () => {
    it('should create product as admin', async () => {
      const response = await request(app)
        .post('/api/products')
        .set('Authorization', `Bearer ${adminToken}`)
        .send({
          codigo: 12345,
          nome: 'New Product',
          preco: 150.50,
          categoria: 'Bebidas',
          estoque: 100,
          unidade: 'UN'
        });

      expect(response.status).toBe(201);
      expect(response.body.product).toHaveProperty('id');
      expect(response.body.product.nome).toBe('New Product');
      expect(response.body.product.preco).toBe(150.50);
    });

    it('should reject product creation by loja user', async () => {
      const response = await request(app)
        .post('/api/products')
        .set('Authorization', `Bearer ${lojaToken}`)
        .send({
          codigo: 12345,
          nome: 'New Product',
          preco: 150.50
        });

      expect(response.status).toBe(403);
    });

    it('should reject duplicate product codigo', async () => {
      await createTestProduct({ codigo: 12345 });

      const response = await request(app)
        .post('/api/products')
        .set('Authorization', `Bearer ${adminToken}`)
        .send({
          codigo: 12345,
          nome: 'Duplicate Product',
          preco: 100
        });

      expect(response.status).toBe(400);
    });

    it('should require mandatory fields', async () => {
      const response = await request(app)
        .post('/api/products')
        .set('Authorization', `Bearer ${adminToken}`)
        .send({
          nome: 'Incomplete Product'
        });

      expect(response.status).toBe(400);
    });
  });

  describe('PUT /api/products/:id', () => {
    it('should update product as admin', async () => {
      const product = await createTestProduct({ nome: 'Old Name', preco: 100 });

      const response = await request(app)
        .put(`/api/products/${product.id}`)
        .set('Authorization', `Bearer ${adminToken}`)
        .send({
          nome: 'New Name',
          preco: 150
        });

      expect(response.status).toBe(200);
      expect(response.body.product.nome).toBe('New Name');
      expect(response.body.product.preco).toBe(150);
    });

    it('should reject update by non-admin', async () => {
      const product = await createTestProduct();

      const response = await request(app)
        .put(`/api/products/${product.id}`)
        .set('Authorization', `Bearer ${lojaToken}`)
        .send({
          nome: 'New Name'
        });

      expect(response.status).toBe(403);
    });

    it('should return 404 for non-existent product', async () => {
      const response = await request(app)
        .put('/api/products/99999')
        .set('Authorization', `Bearer ${adminToken}`)
        .send({
          nome: 'New Name'
        });

      expect(response.status).toBe(404);
    });
  });

  describe('DELETE /api/products/:id', () => {
    it('should delete product as admin', async () => {
      const product = await createTestProduct();

      const response = await request(app)
        .delete(`/api/products/${product.id}`)
        .set('Authorization', `Bearer ${adminToken}`);

      expect(response.status).toBe(200);
      expect(response.body.message).toContain('removido');
    });

    it('should reject deletion by non-admin', async () => {
      const product = await createTestProduct();

      const response = await request(app)
        .delete(`/api/products/${product.id}`)
        .set('Authorization', `Bearer ${lojaToken}`);

      expect(response.status).toBe(403);
    });
  });

  describe('Product Price Validation', () => {
    it('should reject negative prices', async () => {
      const response = await request(app)
        .post('/api/products')
        .set('Authorization', `Bearer ${adminToken}`)
        .send({
          codigo: 12345,
          nome: 'Test Product',
          preco: -10,
          categoria: 'Teste'
        });

      expect(response.status).toBe(400);
    });

    it('should reject zero prices', async () => {
      const response = await request(app)
        .post('/api/products')
        .set('Authorization', `Bearer ${adminToken}`)
        .send({
          codigo: 12345,
          nome: 'Test Product',
          preco: 0,
          categoria: 'Teste'
        });

      expect(response.status).toBe(400);
    });
  });
});
