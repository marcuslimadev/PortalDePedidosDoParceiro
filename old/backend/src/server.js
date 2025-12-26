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
import winthorRouter from './routes/winthor.js';
import reportsRouter from './routes/reports.js';
import { runMigrations } from './migrations/run.js';
import { runSeed } from './scripts/seedMockData.js';
import { query } from './config/database.js';
import notificationsRouter from './routes/notifications.js';
import { registerEventListeners } from './services/eventListeners.js';
import { generalLimiter } from './middleware/rateLimiter.js';
import { securityHeaders, enforceHttps } from './middleware/security.js';
import {
  initSentry,
  sentryRequestHandler,
  sentryTracingHandler,
  sentryErrorHandler
} from './config/sentry.js';

dotenv.config();

const app = express();

// Initialize Sentry BEFORE other middleware
initSentry(app);

// Sentry request handler must be the first middleware
app.use(sentryRequestHandler());

// Sentry tracing middleware
app.use(sentryTracingHandler());

app.use(cors());
app.use(express.json());

// Rate limiting geral para todas as rotas da API
app.use('/api', generalLimiter);

// Segurança HTTP (trust proxy desabilitado para compatibilidade com rate limiter v8+)
// app.enable('trust proxy'); // REMOVIDO: causa erro ERR_ERL_PERMISSIVE_TRUST_PROXY
if (process.env.NODE_ENV === 'production') {
  app.use(enforceHttps);
}
app.use(securityHeaders);

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

// Rota base para /api evitando "Cannot GET /api" em ambientes que testam raiz da API
app.get('/api', (req, res) => {
  res.json({
    message: 'Portal de Pedidos API',
    status: 'ok',
    version: process.env.RENDER_GIT_COMMIT || 'dev',
    time: new Date().toISOString()
  });
});

app.use('/api', healthRouter);
app.use('/api/catalog', catalogRouter);
app.use('/api/auth', authRouter);
app.use('/api/products', productsRouter);
app.use('/api/orders', ordersRouter);
app.use('/api/clients', clientsRouter);
app.use('/api/notifications', notificationsRouter);
app.use('/api/audit', auditRouter);
app.use('/api/winthor', winthorRouter);
app.use('/api/reports', reportsRouter);

// Sentry error handler must be AFTER all routes but BEFORE other error handlers
app.use(sentryErrorHandler());

// Global error handler
app.use((err, req, res, next) => {
  console.error('Error:', err);

  const status = err.status || 500;
  const message = err.message || 'Erro interno do servidor';

  res.status(status).json({
    error: message,
    ...(process.env.NODE_ENV === 'development' && { stack: err.stack })
  });
});

const port = process.env.PORT || 3000;

async function startServer () {
  try {
    console.log('🚀 startServer() iniciou');
    // Garantir que a base esteja sempre atualizada em qualquer ambiente (Render/Docker/local)
    console.log('📋 Chamando runMigrations()...');
    await runMigrations();
    console.log('✅ runMigrations() completou');
    
    // Sincroniza dados base sempre (idempotente: upsert de admin, operadores, clientes e catálogo mock)
    console.log('📋 Chamando runSeed()...');
    await runSeed();
    console.log('✅ runSeed() completou');
    
    await ensureDefaultAdminUser();

    // Registrar listeners de eventos
    // registerEventListeners(); // TEMPORARIAMENTE DESABILITADO PARA DEBUG

    const existingProducts = await query('SELECT COUNT(*) AS total FROM products');
    const existingClients = await query("SELECT COUNT(*) AS total FROM users WHERE role = 'loja'");
    const productsCount = Number(existingProducts.rows[0]?.total || 0);
    const clientsCount = Number(existingClients.rows[0]?.total || 0);
    console.log(`Seed sincronizado. Produtos: ${productsCount}, clientes: ${clientsCount}`);

    // Start server DENTRO do try
    app.listen(port, '0.0.0.0', () => {
      console.log(`✅ API rodando na porta ${port}`);
      console.log(`🌐 Acesse: http://localhost:${port}/api`);
    });

  } catch (error) {
    console.error('❌ Falha ao iniciar servidor:', error);
    console.error(error.stack);
    process.exit(1);
  }
}

startServer();
