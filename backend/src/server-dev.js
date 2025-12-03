import express from 'express';
import cors from 'cors';
import dotenv from 'dotenv';

import healthRouter from './routes/health.js';
import authRouter from './routes/auth.js';
import productsRouter from './routes/products.js';
import ordersRouter from './routes/orders.js';
import catalogRouter from './routes/catalog.js';
import clientsRouter from './routes/clients.js';
import { generalLimiter } from './middleware/rateLimiter.js';
import { securityHeaders } from './middleware/security.js';

dotenv.config();

const app = express();

app.use(cors());
app.use(express.json());

// Rate limiting
app.use('/api', generalLimiter);

// Segurança HTTP (sem enforceHttps em dev)
app.use(securityHeaders);

app.get('/', (req, res) => {
  res.json({ message: 'Portal de Pedidos API - DEV MODE' });
});

app.get('/api', (req, res) => {
  res.json({
    message: 'Portal de Pedidos API - DEV',
    status: 'ok',
    time: new Date().toISOString()
  });
});

app.use('/api', healthRouter);
app.use('/api/catalog', catalogRouter);
app.use('/api/auth', authRouter);
app.use('/api/products', productsRouter);
app.use('/api/orders', ordersRouter);
app.use('/api/clients', clientsRouter);

// Global error handler
app.use((err, req, res, next) => {
  console.error('Error:', err);
  res.status(err.status || 500).json({
    error: err.message || 'Erro interno do servidor',
    stack: err.stack
  });
});

const port = process.env.PORT || 3000;

console.log('🚀 Iniciando servidor DEV (sem migrations/seed)...');

const server = app.listen(port, '0.0.0.0', () => {
  console.log(`✅ API DEV rodando na porta ${port}`);
  console.log(`🌐 http://localhost:${port}/api`);
  console.log(`📊 Health: http://localhost:${port}/api/health`);
  console.log('\n💡 Pressione Ctrl+C para parar\n');
});

server.on('error', (error) => {
  console.error('❌ Erro ao iniciar servidor:', error);
  process.exit(1);
});

// Manter processo vivo
process.on('SIGINT', () => {
  console.log('\n👋 Encerrando servidor...');
  server.close(() => {
    console.log('✅ Servidor encerrado');
    process.exit(0);
  });
});

process.on('SIGTERM', () => {
  console.log('\n👋 Encerrando servidor...');
  server.close(() => {
    console.log('✅ Servidor encerrado');
    process.exit(0);
  });
});
