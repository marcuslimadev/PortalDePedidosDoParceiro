import request from 'supertest';
import express from 'express';
import orderRoutes from '../routes/orders.js';
import { cleanDatabase, createTestUser, createTestProduct, createTestOrder, generateTestToken } from './testHelpers.js';
import { authenticateToken } from '../middleware/auth.js';
import { getClient } from '../config/database.js';

// Create test app
const app = express();
app.use(express.json());
app.use('/api/orders', authenticateToken, orderRoutes);

describe('Orders API', () => {
  let adminUser, adminToken;
  let lojaUser, lojaToken;
  let product1, product2;

  beforeEach(async () => {
    await cleanDatabase();

    // Create users
    adminUser = await createTestUser({
      email: 'admin@test.com',
      nome: 'Admin User',
      role: 'admin'
    });
    adminToken = generateTestToken(adminUser);

    lojaUser = await createTestUser({
      email: 'loja@test.com',
      nome: 'Loja User',
      role: 'loja',
      credit_limit: 10000,
      credit_used: 0
    });
    lojaToken = generateTestToken(lojaUser);

    // Create products
    product1 = await createTestProduct({ codigo: 1001, nome: 'Product 1', preco: 100 });
    product2 = await createTestProduct({ codigo: 1002, nome: 'Product 2', preco: 200 });
  });

  describe('POST /api/orders', () => {
    it('should create order with valid data', async () => {
      const response = await request(app)
        .post('/api/orders')
        .set('Authorization', `Bearer ${lojaToken}`)
        .send({
          payment_terms: 'Antecipado',
          items: [
            { product_id: product1.id, quantidade: 10, preco_unitario: 100 },
            { product_id: product2.id, quantidade: 5, preco_unitario: 200 }
          ]
        });

      expect(response.status).toBe(201);
      expect(response.body.order).toHaveProperty('id');
      expect(response.body.order.loja_id).toBe(lojaUser.id);
      expect(response.body.order.total).toBe(1900); // (10*100 + 5*200) - 5% Antecipado = 2000 - 100 = 1900
      expect(response.body.order.discount_percentage).toBe(5);
      expect(response.body.order.status).toBe('pendente');
    });

    it('should apply 5% discount for Antecipado', async () => {
      const response = await request(app)
        .post('/api/orders')
        .set('Authorization', `Bearer ${lojaToken}`)
        .send({
          payment_terms: 'Antecipado',
          items: [
            { product_id: product1.id, quantidade: 10, preco_unitario: 100 }
          ]
        });

      expect(response.status).toBe(201);
      expect(response.body.order.subtotal).toBe(1000);
      expect(response.body.order.discount).toBe(50); // 5% of 1000
      expect(response.body.order.total).toBe(950);
    });

    it('should apply 2% discount for 30 dias', async () => {
      const response = await request(app)
        .post('/api/orders')
        .set('Authorization', `Bearer ${lojaToken}`)
        .send({
          payment_terms: '30 dias',
          items: [
            { product_id: product1.id, quantidade: 10, preco_unitario: 100 }
          ]
        });

      expect(response.status).toBe(201);
      expect(response.body.order.subtotal).toBe(1000);
      expect(response.body.order.discount).toBe(20); // 2% of 1000
      expect(response.body.order.total).toBe(980);
    });

    it('should reject order exceeding credit limit', async () => {
      // Create loja with low credit limit
      const lowCreditLoja = await createTestUser({
        email: 'lowcredit@test.com',
        role: 'loja',
        credit_limit: 500,
        credit_used: 0
      });
      const lowCreditToken = generateTestToken(lowCreditLoja);

      const response = await request(app)
        .post('/api/orders')
        .set('Authorization', `Bearer ${lowCreditToken}`)
        .send({
          payment_terms: 'Antecipado',
          items: [
            { product_id: product1.id, quantidade: 10, preco_unitario: 100 }
          ]
        });

      expect(response.status).toBe(400);
      expect(response.body.error).toContain('crédito');
    });

    it('should update credit_used after order creation', async () => {
      await request(app)
        .post('/api/orders')
        .set('Authorization', `Bearer ${lojaToken}`)
        .send({
          payment_terms: 'Antecipado',
          items: [
            { product_id: product1.id, quantidade: 10, preco_unitario: 100 }
          ]
        });

      // Check credit_used in database
      const client = await getClient();
      try {
        const result = await client.query(
          'SELECT credit_used FROM users WHERE id = $1',
          [lojaUser.id]
        );
        expect(result.rows[0].credit_used).toBe(950); // Order total
      } finally {
        client.release();
      }
    });

    it('should require at least one item', async () => {
      const response = await request(app)
        .post('/api/orders')
        .set('Authorization', `Bearer ${lojaToken}`)
        .send({
          payment_terms: 'Antecipado',
          items: []
        });

      expect(response.status).toBe(400);
    });

    it('should validate payment terms', async () => {
      const response = await request(app)
        .post('/api/orders')
        .set('Authorization', `Bearer ${lojaToken}`)
        .send({
          payment_terms: 'Invalid Payment',
          items: [
            { product_id: product1.id, quantidade: 10, preco_unitario: 100 }
          ]
        });

      expect(response.status).toBe(400);
    });
  });

  describe('PATCH /api/orders/:id/status', () => {
    it('should update order status as admin', async () => {
      const order = await createTestOrder({
        loja_id: lojaUser.id,
        status: 'pendente'
      });

      const response = await request(app)
        .patch(`/api/orders/${order.id}/status`)
        .set('Authorization', `Bearer ${adminToken}`)
        .send({
          status: 'aprovado'
        });

      expect(response.status).toBe(200);
      expect(response.body.order.status).toBe('aprovado');
    });

    it('should reject status update by loja user', async () => {
      const order = await createTestOrder({
        loja_id: lojaUser.id,
        status: 'pendente'
      });

      const response = await request(app)
        .patch(`/api/orders/${order.id}/status`)
        .set('Authorization', `Bearer ${lojaToken}`)
        .send({
          status: 'aprovado'
        });

      expect(response.status).toBe(403);
    });

    it('should decrease credit_used when order is cancelled', async () => {
      const order = await createTestOrder({
        loja_id: lojaUser.id,
        status: 'aprovado',
        total: 950
      });

      // Update loja credit_used
      const client = await getClient();
      try {
        await client.query(
          'UPDATE users SET credit_used = 950 WHERE id = $1',
          [lojaUser.id]
        );
      } finally {
        client.release();
      }

      await request(app)
        .patch(`/api/orders/${order.id}/status`)
        .set('Authorization', `Bearer ${adminToken}`)
        .send({
          status: 'cancelado',
          motivo_cancelamento: 'Test cancellation'
        });

      // Check credit_used decreased
      const client2 = await getClient();
      try {
        const result = await client2.query(
          'SELECT credit_used FROM users WHERE id = $1',
          [lojaUser.id]
        );
        expect(result.rows[0].credit_used).toBe(0);
      } finally {
        client2.release();
      }
    });
  });

  describe('GET /api/orders', () => {
    it('should list orders for loja user (own orders only)', async () => {
      await createTestOrder({ loja_id: lojaUser.id });
      await createTestOrder({ loja_id: lojaUser.id });

      // Create another loja and order
      const otherLoja = await createTestUser({
        email: 'other@test.com',
        role: 'loja'
      });
      await createTestOrder({ loja_id: otherLoja.id });

      const response = await request(app)
        .get('/api/orders')
        .set('Authorization', `Bearer ${lojaToken}`);

      expect(response.status).toBe(200);
      expect(response.body.orders).toHaveLength(2);
      expect(response.body.orders[0].loja_id).toBe(lojaUser.id);
    });

    it('should list all orders for admin', async () => {
      await createTestOrder({ loja_id: lojaUser.id });

      const otherLoja = await createTestUser({
        email: 'other@test.com',
        role: 'loja'
      });
      await createTestOrder({ loja_id: otherLoja.id });

      const response = await request(app)
        .get('/api/orders')
        .set('Authorization', `Bearer ${adminToken}`);

      expect(response.status).toBe(200);
      expect(response.body.orders.length).toBeGreaterThanOrEqual(2);
    });

    it('should filter orders by status', async () => {
      await createTestOrder({ loja_id: lojaUser.id, status: 'pendente' });
      await createTestOrder({ loja_id: lojaUser.id, status: 'aprovado' });

      const response = await request(app)
        .get('/api/orders?status=pendente')
        .set('Authorization', `Bearer ${lojaToken}`);

      expect(response.status).toBe(200);
      expect(response.body.orders).toHaveLength(1);
      expect(response.body.orders[0].status).toBe('pendente');
    });
  });

  describe('Credit Limit Validation', () => {
    it('should allow order within available credit', async () => {
      const loja = await createTestUser({
        email: 'creditloja@test.com',
        role: 'loja',
        credit_limit: 5000,
        credit_used: 3000
      });
      const token = generateTestToken(loja);

      const response = await request(app)
        .post('/api/orders')
        .set('Authorization', `Bearer ${token}`)
        .send({
          payment_terms: 'Antecipado',
          items: [
            { product_id: product1.id, quantidade: 10, preco_unitario: 100 }
          ]
        });

      expect(response.status).toBe(201);
    });

    it('should reject order exceeding available credit', async () => {
      const loja = await createTestUser({
        email: 'limitloja@test.com',
        role: 'loja',
        credit_limit: 5000,
        credit_used: 4900
      });
      const token = generateTestToken(loja);

      const response = await request(app)
        .post('/api/orders')
        .set('Authorization', `Bearer ${token}`)
        .send({
          payment_terms: 'Antecipado',
          items: [
            { product_id: product1.id, quantidade: 10, preco_unitario: 100 }
          ]
        });

      expect(response.status).toBe(400);
      expect(response.body.error).toContain('crédito');
    });
  });
});
