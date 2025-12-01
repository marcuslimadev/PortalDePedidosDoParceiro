import express from 'express';
import cors from 'cors';
import dotenv from 'dotenv';

dotenv.config();

const app = express();
const port = process.env.PORT || 3000;

app.use(cors());
app.use(express.json());

app.get('/', (req, res) => {
  res.json({ message: 'Portal de Pedidos API online' });
});

app.get('/api', (req, res) => {
  res.json({
    message: 'Portal de Pedidos API',
    status: 'ok',
    version: 'minimal-test',
    time: new Date().toISOString()
  });
});

const server = app.listen(port, '0.0.0.0', () => {
  console.log(`✅ API MÍNIMA rodando na porta ${port}`);
  console.log(`🌐 http://localhost:${port}/api`);
});

server.on('error', (error) => {
  console.error('❌ Erro ao iniciar servidor:', error);
  process.exit(1);
});
