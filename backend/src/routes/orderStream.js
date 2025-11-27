import { Router } from 'express';
import { authenticateToken } from '../middleware/auth.js';
import { eventBus } from '../events/eventBus.js';

const router = Router();

router.get('/stream', authenticateToken, (req, res) => {
  res.setHeader('Content-Type', 'text/event-stream');
  res.setHeader('Cache-Control', 'no-cache');
  res.setHeader('Connection', 'keep-alive');
  res.flushHeaders?.();

  const sendEvent = (event) => {
    const isLoja = req.user.role === 'loja';
    const isSameLoja = !event.lojaId || event.lojaId === req.user.id;
    const canReceive = !isLoja || (isLoja && isSameLoja);

    if (!canReceive) {
      return;
    }

    res.write(`event: ${event.type}\n`);
    res.write(`data: ${JSON.stringify(event.payload)}\n\n`);
  };

  res.write('event: connected\n');
  res.write('data: {"message":"stream aberto"}\n\n');

  eventBus.on('order-event', sendEvent);

  req.on('close', () => {
    eventBus.off('order-event', sendEvent);
    res.end();
  });
});

export default router;
