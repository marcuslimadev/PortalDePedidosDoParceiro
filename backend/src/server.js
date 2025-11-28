import express from 'express';
import cors from 'cors';
import dotenv from 'dotenv';
import bcrypt from 'bcryptjs';

import healthRouter from './routes/health.js';
import authRouter from './routes/auth.js';
import productsRouter from './routes/products.js';
import ordersRouter from './routes/orders.js';
import catalogRouter from './routes/catalog.js';
import clientsRouter from './routes/clients.js';
import auditRouter from './routes/audit.js';
import reportsRouter from './routes/reports.js';
import { runMigrations } from './migrations/run.js';
import { runSeed } from './scripts/seedMockData.js';
import { query } from './config/database.js';
import notificationsRouter from './routes/notifications.js';
import { registerEventListeners } from './services/eventListeners.js';
import { generalLimiter } from './middleware/rateLimiter.js';

dotenv.config();

const app = express();
app.use(cors());
app.use(express.json());

// Rate limiting geral para todas as rotas da API
app.use('/api', generalLimiter);

async function ensureDefaultAdminUser () {
  const email = 'admin@portalpedidos.com';
  const defaultPassword = 'admin123';
  const nome = 'Administrador do Sistema';

  const existingUser = await query(
    'SELECT id, password_hash, role, ativo FROM users WHERE email = $1',
    [email]
  );

  if (existingUser.rows.length === 0) {
    const passwordHash = await bcrypt.hash(defaultPassword, 10);
    await query(
      'INSERT INTO users (email, password_hash, nome, role, ativo) VALUES ($1, $2, $3, $4, true)',
      [email, passwordHash, nome, 'admin']
    );
    console.log('Usuário admin criado para produção.');
    return;
  }

  const user = existingUser.rows[0];
  const updates = [];
  const params = [user.id];

  const passwordValid = await bcrypt.compare(defaultPassword, user.password_hash);
  if (!passwordValid) {
    const passwordHash = await bcrypt.hash(defaultPassword, 10);
    updates.push('password_hash = $' + (params.length + 1));
    params.push(passwordHash);
  }

  if (!user.ativo) {
    updates.push('ativo = true');
  }

  if (user.role !== 'admin') {
    updates.push('role = $' + (params.length + 1));
    params.push('admin');
  }

  if (updates.length > 0) {
    const setClause = updates.join(', ');
    await query(
      `UPDATE users SET ${setClause} WHERE id = $1`,
      params
    );
    console.log('Usuário admin sincronizado para credenciais padrão.');
  }
}

app.get('/', (req, res) => {
  res.json({ message: 'Portal de Pedidos API online' });
});

app.use('/api', healthRouter);
app.use('/api/catalog', catalogRouter);
app.use('/api/auth', authRouter);
app.use('/api/products', productsRouter);
app.use('/api/orders', ordersRouter);
app.use('/api/clients', clientsRouter);
app.use('/api/notifications', notificationsRouter);
app.use('/api/audit', auditRouter);
app.use('/api/reports', reportsRouter);

const port = process.env.PORT || 3000;

async function startServer () {
  try {
    // Garantir que a base esteja sempre atualizada em qualquer ambiente (Render/Docker/local)
    await runMigrations();
    // Sincroniza dados base sempre (idempotente: upsert de admin, operadores, clientes e catálogo mock)
    await runSeed();
    await ensureDefaultAdminUser();
    
    // Registrar listeners de eventos
    registerEventListeners();
    
    const existingProducts = await query('SELECT COUNT(*) AS total FROM products');
    const existingClients = await query("SELECT COUNT(*) AS total FROM users WHERE role = 'loja'");
    const productsCount = Number(existingProducts.rows[0]?.total || 0);
    const clientsCount = Number(existingClients.rows[0]?.total || 0);
    console.log(`Seed sincronizado. Produtos: ${productsCount}, clientes: ${clientsCount}`);
  } catch (error) {
    console.error('Falha ao garantir usuário admin padrão:', error);
  }

  app.listen(port, () => {
    console.log(`API rodando na porta ${port}`);
  });
}

startServer();

