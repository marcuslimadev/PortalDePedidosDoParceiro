import request from 'supertest';
import express from 'express';
import jwt from 'jsonwebtoken';
import authRoutes from '../routes/auth.js';
import { cleanDatabase, createTestUser } from './testHelpers.js';

// Create test app
const app = express();
app.use(express.json());
app.use('/api/auth', authRoutes);

describe('Authentication API', () => {
  beforeEach(async () => {
    await cleanDatabase();
  });

  describe('POST /api/auth/login', () => {
    it('should login with valid credentials', async () => {
      // Create test user with known password
      const bcrypt = await import('bcryptjs');
      const passwordHash = await bcrypt.hash('password123', 10);
      
      await createTestUser({
        email: 'user@test.com',
        password_hash: passwordHash,
        nome: 'Test User',
        role: 'loja'
      });

      const response = await request(app)
        .post('/api/auth/login')
        .send({
          email: 'user@test.com',
          password: 'password123'
        });

      expect(response.status).toBe(200);
      expect(response.body).toHaveProperty('token');
      expect(response.body).toHaveProperty('user');
      expect(response.body.user.email).toBe('user@test.com');
      expect(response.body.user.role).toBe('loja');
    });

    it('should reject invalid credentials', async () => {
      const bcrypt = await import('bcryptjs');
      const passwordHash = await bcrypt.hash('password123', 10);
      
      await createTestUser({
        email: 'user@test.com',
        password_hash: passwordHash
      });

      const response = await request(app)
        .post('/api/auth/login')
        .send({
          email: 'user@test.com',
          password: 'wrongpassword'
        });

      expect(response.status).toBe(401);
      expect(response.body).toHaveProperty('error');
    });

    it('should reject non-existent user', async () => {
      const response = await request(app)
        .post('/api/auth/login')
        .send({
          email: 'nonexistent@test.com',
          password: 'password123'
        });

      expect(response.status).toBe(401);
    });

    it('should require email and password', async () => {
      const response = await request(app)
        .post('/api/auth/login')
        .send({
          email: 'user@test.com'
        });

      expect(response.status).toBe(400);
    });
  });

  describe('POST /api/auth/register', () => {
    it('should register new user as loja', async () => {
      const response = await request(app)
        .post('/api/auth/register')
        .send({
          email: 'newuser@test.com',
          password: 'password123',
          nome: 'New User',
          razao_social: 'Test Company',
          cnpj: '12345678901234'
        });

      expect(response.status).toBe(201);
      expect(response.body).toHaveProperty('token');
      expect(response.body.user.email).toBe('newuser@test.com');
      expect(response.body.user.role).toBe('loja');
    });

    it('should reject duplicate email', async () => {
      await createTestUser({ email: 'existing@test.com' });

      const response = await request(app)
        .post('/api/auth/register')
        .send({
          email: 'existing@test.com',
          password: 'password123',
          nome: 'Duplicate User'
        });

      expect(response.status).toBe(400);
      expect(response.body.error).toContain('já cadastrado');
    });

    it('should require valid email format', async () => {
      const response = await request(app)
        .post('/api/auth/register')
        .send({
          email: 'invalid-email',
          password: 'password123',
          nome: 'Test User'
        });

      expect(response.status).toBe(400);
    });

    it('should require minimum password length', async () => {
      const response = await request(app)
        .post('/api/auth/register')
        .send({
          email: 'user@test.com',
          password: '123',
          nome: 'Test User'
        });

      expect(response.status).toBe(400);
      expect(response.body.error).toContain('6 caracteres');
    });
  });

  describe('JWT Token Validation', () => {
    it('should generate valid JWT token', async () => {
      const bcrypt = await import('bcryptjs');
      const passwordHash = await bcrypt.hash('password123', 10);
      
      await createTestUser({
        email: 'user@test.com',
        password_hash: passwordHash
      });

      const response = await request(app)
        .post('/api/auth/login')
        .send({
          email: 'user@test.com',
          password: 'password123'
        });

      const token = response.body.token;
      expect(token).toBeDefined();

      // Verify token structure
      const decoded = jwt.verify(token, process.env.JWT_SECRET || 'test-secret');
      expect(decoded).toHaveProperty('id');
      expect(decoded).toHaveProperty('email');
      expect(decoded).toHaveProperty('role');
      expect(decoded.email).toBe('user@test.com');
    });

    it('should include user data in token payload', async () => {
      const bcrypt = await import('bcryptjs');
      const passwordHash = await bcrypt.hash('password123', 10);
      
      const user = await createTestUser({
        email: 'user@test.com',
        password_hash: passwordHash,
        nome: 'Test User',
        role: 'admin'
      });

      const response = await request(app)
        .post('/api/auth/login')
        .send({
          email: 'user@test.com',
          password: 'password123'
        });

      const decoded = jwt.verify(response.body.token, process.env.JWT_SECRET || 'test-secret');
      expect(decoded.id).toBe(user.id);
      expect(decoded.email).toBe('user@test.com');
      expect(decoded.nome).toBe('Test User');
      expect(decoded.role).toBe('admin');
    });
  });
});
