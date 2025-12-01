/**
 * Test utilities and helpers
 */

import { getClient } from '../config/database.js';

/**
 * Clean up database tables for testing
 */
export async function cleanDatabase () {
  const client = await getClient();

  try {
    await client.query('BEGIN');

    // Delete in correct order to respect foreign keys
    await client.query('DELETE FROM order_items');
    await client.query('DELETE FROM orders');
    await client.query('DELETE FROM notifications');
    await client.query('DELETE FROM audit_logs');
    await client.query('DELETE FROM winthor_sync_logs');
    await client.query('DELETE FROM client_credit_history');
    await client.query('DELETE FROM product_price_history');
    await client.query('DELETE FROM products');
    await client.query('DELETE FROM users');

    await client.query('COMMIT');
  } catch (error) {
    await client.query('ROLLBACK');
    throw error;
  } finally {
    client.release();
  }
}

/**
 * Create a test user
 */
export async function createTestUser (userData = {}) {
  const client = await getClient();

  const defaultData = {
    email: 'test@example.com',
    password_hash: '$2a$10$dummyhashfortest',
    nome: 'Test User',
    role: 'loja',
    ...userData
  };

  try {
    const result = await client.query(
      `INSERT INTO users (email, password_hash, nome, role, credit_limit, credit_used)
       VALUES ($1, $2, $3, $4, $5, $6)
       RETURNING *`,
      [
        defaultData.email,
        defaultData.password_hash,
        defaultData.nome,
        defaultData.role,
        defaultData.credit_limit || 10000,
        defaultData.credit_used || 0
      ]
    );

    return result.rows[0];
  } finally {
    client.release();
  }
}

/**
 * Create a test product
 */
export async function createTestProduct (productData = {}) {
  const client = await getClient();

  const defaultData = {
    codigo: Math.floor(Math.random() * 100000),
    nome: 'Test Product',
    preco: 100.00,
    categoria: 'Teste',
    ...productData
  };

  try {
    const result = await client.query(
      `INSERT INTO products (codigo, nome, preco, categoria, estoque, unidade)
       VALUES ($1, $2, $3, $4, $5, $6)
       RETURNING *`,
      [
        defaultData.codigo,
        defaultData.nome,
        defaultData.preco,
        defaultData.categoria,
        defaultData.estoque || 100,
        defaultData.unidade || 'UN'
      ]
    );

    return result.rows[0];
  } finally {
    client.release();
  }
}

/**
 * Create a test order
 */
export async function createTestOrder (orderData = {}) {
  const client = await getClient();

  const defaultData = {
    loja_id: orderData.loja_id,
    payment_terms: 'Antecipado',
    subtotal: 1000,
    discount: 50,
    discount_percentage: 5,
    total: 950,
    items: orderData.items || [],
    ...orderData
  };

  try {
    await client.query('BEGIN');

    // Create order
    const orderResult = await client.query(
      `INSERT INTO orders (loja_id, payment_terms, subtotal, discount, discount_percentage, total, status)
       VALUES ($1, $2, $3, $4, $5, $6, $7)
       RETURNING *`,
      [
        defaultData.loja_id,
        defaultData.payment_terms,
        defaultData.subtotal,
        defaultData.discount,
        defaultData.discount_percentage,
        defaultData.total,
        defaultData.status || 'pendente'
      ]
    );

    const order = orderResult.rows[0];

    // Create order items
    if (defaultData.items.length > 0) {
      for (const item of defaultData.items) {
        await client.query(
          `INSERT INTO order_items (order_id, product_id, quantidade, preco_unitario)
           VALUES ($1, $2, $3, $4)`,
          [order.id, item.product_id, item.quantidade, item.preco_unitario]
        );
      }
    }

    await client.query('COMMIT');
    return order;
  } catch (error) {
    await client.query('ROLLBACK');
    throw error;
  } finally {
    client.release();
  }
}

/**
 * Generate JWT token for testing
 */
export function generateTestToken (user) {
  const jwt = require('jsonwebtoken');
  return jwt.sign(
    {
      id: user.id,
      email: user.email,
      nome: user.nome,
      role: user.role
    },
    process.env.JWT_SECRET || 'test-secret',
    { expiresIn: '1h' }
  );
}
