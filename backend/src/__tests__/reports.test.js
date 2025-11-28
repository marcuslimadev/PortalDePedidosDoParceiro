import request from 'supertest';
import express from 'express';
import reportRoutes from '../routes/reports.js';
import { cleanDatabase, createTestUser, createTestProduct, createTestOrder, generateTestToken } from './testHelpers.js';
import { authenticateToken } from '../middleware/auth.js';

// Create test app
const app = express();
app.use(express.json());
app.use('/api/reports', authenticateToken, reportRoutes);

describe('Reports API', () => {
  let adminUser, adminToken;
  let operadorUser, operadorToken;
  let lojaUser, lojaToken;
  let product1, product2, product3;

  beforeEach(async () => {
    await cleanDatabase();
    
    // Create users
    adminUser = await createTestUser({
      email: 'admin@test.com',
      nome: 'Admin User',
      role: 'admin'
    });
    adminToken = generateTestToken(adminUser);

    operadorUser = await createTestUser({
      email: 'operador@test.com',
      nome: 'Operador User',
      role: 'operador'
    });
    operadorToken = generateTestToken(operadorUser);

    lojaUser = await createTestUser({
      email: 'loja@test.com',
      nome: 'Loja User',
      role: 'loja',
      credit_limit: 50000
    });
    lojaToken = generateTestToken(lojaUser);

    // Create products
    product1 = await createTestProduct({ codigo: 1001, nome: 'Product A', preco: 100 });
    product2 = await createTestProduct({ codigo: 1002, nome: 'Product B', preco: 200 });
    product3 = await createTestProduct({ codigo: 1003, nome: 'Product C', preco: 50 });
  });

  describe('GET /api/reports/abc/products', () => {
    it('should return ABC analysis for products', async () => {
      // Create orders with different product volumes
      await createTestOrder({
        loja_id: lojaUser.id,
        status: 'aprovado',
        total: 5000,
        items: [
          { product_id: product1.id, quantidade: 50, preco_unitario: 100 }
        ]
      });

      await createTestOrder({
        loja_id: lojaUser.id,
        status: 'aprovado',
        total: 2000,
        items: [
          { product_id: product2.id, quantidade: 10, preco_unitario: 200 }
        ]
      });

      await createTestOrder({
        loja_id: lojaUser.id,
        status: 'aprovado',
        total: 250,
        items: [
          { product_id: product3.id, quantidade: 5, preco_unitario: 50 }
        ]
      });

      const response = await request(app)
        .get('/api/reports/abc/products')
        .set('Authorization', `Bearer ${adminToken}`);

      expect(response.status).toBe(200);
      expect(response.body.products).toHaveLength(3);
      expect(response.body.products[0]).toHaveProperty('classification');
      expect(response.body.products[0]).toHaveProperty('revenue_percentage');
      expect(response.body.products[0].classification).toBe('A');
    });

    it('should reject access by loja user', async () => {
      const response = await request(app)
        .get('/api/reports/abc/products')
        .set('Authorization', `Bearer ${lojaToken}`);

      expect(response.status).toBe(403);
    });

    it('should allow access by operador', async () => {
      const response = await request(app)
        .get('/api/reports/abc/products')
        .set('Authorization', `Bearer ${operadorToken}`);

      expect(response.status).toBe(200);
    });
  });

  describe('GET /api/reports/abc/clients', () => {
    it('should return ABC analysis for clients', async () => {
      // Create multiple lojas
      const loja1 = await createTestUser({
        email: 'loja1@test.com',
        role: 'loja',
        credit_limit: 50000
      });

      const loja2 = await createTestUser({
        email: 'loja2@test.com',
        role: 'loja',
        credit_limit: 30000
      });

      // Create orders with different totals
      await createTestOrder({
        loja_id: loja1.id,
        status: 'aprovado',
        total: 10000
      });

      await createTestOrder({
        loja_id: loja2.id,
        status: 'aprovado',
        total: 2000
      });

      const response = await request(app)
        .get('/api/reports/abc/clients')
        .set('Authorization', `Bearer ${adminToken}`);

      expect(response.status).toBe(200);
      expect(response.body.clients.length).toBeGreaterThanOrEqual(2);
      expect(response.body.clients[0]).toHaveProperty('classification');
      expect(response.body.clients[0]).toHaveProperty('total_revenue');
    });
  });

  describe('GET /api/reports/dashboard', () => {
    it('should return dashboard KPIs', async () => {
      // Create test data
      await createTestOrder({
        loja_id: lojaUser.id,
        status: 'pendente',
        total: 1000
      });

      await createTestOrder({
        loja_id: lojaUser.id,
        status: 'aprovado',
        total: 2000
      });

      await createTestOrder({
        loja_id: lojaUser.id,
        status: 'entregue',
        total: 3000
      });

      const response = await request(app)
        .get('/api/reports/dashboard')
        .set('Authorization', `Bearer ${adminToken}`);

      expect(response.status).toBe(200);
      expect(response.body).toHaveProperty('total_orders');
      expect(response.body).toHaveProperty('pending_orders');
      expect(response.body).toHaveProperty('total_revenue');
      expect(response.body).toHaveProperty('average_ticket');
      expect(response.body.total_orders).toBeGreaterThanOrEqual(3);
    });

    it('should filter dashboard by date range', async () => {
      const today = new Date();
      const yesterday = new Date(today);
      yesterday.setDate(yesterday.getDate() - 1);

      const response = await request(app)
        .get('/api/reports/dashboard')
        .query({
          start_date: yesterday.toISOString().split('T')[0],
          end_date: today.toISOString().split('T')[0]
        })
        .set('Authorization', `Bearer ${adminToken}`);

      expect(response.status).toBe(200);
      expect(response.body).toHaveProperty('total_orders');
    });
  });

  describe('GET /api/reports/sales', () => {
    it('should return sales by period', async () => {
      await createTestOrder({
        loja_id: lojaUser.id,
        status: 'aprovado',
        total: 1000
      });

      await createTestOrder({
        loja_id: lojaUser.id,
        status: 'entregue',
        total: 2000
      });

      const response = await request(app)
        .get('/api/reports/sales')
        .set('Authorization', `Bearer ${adminToken}`);

      expect(response.status).toBe(200);
      expect(response.body.sales).toBeDefined();
      expect(Array.isArray(response.body.sales)).toBe(true);
    });

    it('should group sales by month', async () => {
      const response = await request(app)
        .get('/api/reports/sales?period=month')
        .set('Authorization', `Bearer ${adminToken}`);

      expect(response.status).toBe(200);
      expect(response.body.sales).toBeDefined();
    });

    it('should group sales by week', async () => {
      const response = await request(app)
        .get('/api/reports/sales?period=week')
        .set('Authorization', `Bearer ${adminToken}`);

      expect(response.status).toBe(200);
      expect(response.body.sales).toBeDefined();
    });
  });

  describe('GET /api/reports/sales-by-store', () => {
    it('should return sales grouped by store', async () => {
      const loja1 = await createTestUser({
        email: 'store1@test.com',
        role: 'loja',
        nome: 'Store 1',
        credit_limit: 50000
      });

      const loja2 = await createTestUser({
        email: 'store2@test.com',
        role: 'loja',
        nome: 'Store 2',
        credit_limit: 30000
      });

      await createTestOrder({
        loja_id: loja1.id,
        status: 'aprovado',
        total: 5000
      });

      await createTestOrder({
        loja_id: loja2.id,
        status: 'entregue',
        total: 3000
      });

      const response = await request(app)
        .get('/api/reports/sales-by-store')
        .set('Authorization', `Bearer ${adminToken}`);

      expect(response.status).toBe(200);
      expect(response.body.stores).toBeDefined();
      expect(Array.isArray(response.body.stores)).toBe(true);
      expect(response.body.stores.length).toBeGreaterThanOrEqual(2);
      expect(response.body.stores[0]).toHaveProperty('store_name');
      expect(response.body.stores[0]).toHaveProperty('total_sales');
      expect(response.body.stores[0]).toHaveProperty('order_count');
    });

    it('should sort stores by total sales descending', async () => {
      const loja1 = await createTestUser({
        email: 'highsales@test.com',
        role: 'loja',
        nome: 'High Sales Store',
        credit_limit: 50000
      });

      const loja2 = await createTestUser({
        email: 'lowsales@test.com',
        role: 'loja',
        nome: 'Low Sales Store',
        credit_limit: 30000
      });

      await createTestOrder({
        loja_id: loja1.id,
        status: 'aprovado',
        total: 10000
      });

      await createTestOrder({
        loja_id: loja2.id,
        status: 'aprovado',
        total: 1000
      });

      const response = await request(app)
        .get('/api/reports/sales-by-store')
        .set('Authorization', `Bearer ${adminToken}`);

      expect(response.status).toBe(200);
      expect(response.body.stores[0].total_sales).toBeGreaterThan(response.body.stores[1].total_sales);
    });
  });

  describe('Access Control', () => {
    it('should allow admin to access all reports', async () => {
      const endpoints = [
        '/api/reports/abc/products',
        '/api/reports/abc/clients',
        '/api/reports/dashboard',
        '/api/reports/sales',
        '/api/reports/sales-by-store'
      ];

      for (const endpoint of endpoints) {
        const response = await request(app)
          .get(endpoint)
          .set('Authorization', `Bearer ${adminToken}`);

        expect(response.status).toBe(200);
      }
    });

    it('should allow operador to access all reports', async () => {
      const endpoints = [
        '/api/reports/abc/products',
        '/api/reports/abc/clients',
        '/api/reports/dashboard',
        '/api/reports/sales'
      ];

      for (const endpoint of endpoints) {
        const response = await request(app)
          .get(endpoint)
          .set('Authorization', `Bearer ${operadorToken}`);

        expect(response.status).toBe(200);
      }
    });

    it('should reject loja access to reports', async () => {
      const response = await request(app)
        .get('/api/reports/dashboard')
        .set('Authorization', `Bearer ${lojaToken}`);

      expect(response.status).toBe(403);
    });
  });
});
