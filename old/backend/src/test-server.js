import express from 'express';
import cors from 'cors';

const app = express();

app.use(cors());
app.use(express.json());

app.get('/api', (req, res) => {
  res.json({ message: 'Test API', status: 'ok' });
});

app.get('/api/health', (req, res) => {
  res.json({ status: 'healthy' });
});

const port = 3002;
app.listen(port, '0.0.0.0', () => {
  console.log(`✅ Test API rodando na porta ${port}`);
});

// Manter processo vivo
setInterval(() => {
  console.log('Still alive:', new Date().toISOString());
}, 5000);
