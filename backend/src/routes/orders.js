import { Router } from 'express';
import { authenticateToken, requireRole } from '../middleware/auth.js';
import { createOrder, exportOrdersCsv, listOrders, openOrdersSummary, repeatOrder, updateOrderStatus } from '../controllers/orderController.js';
import { eventBus } from '../events/eventBus.js';

const router = Router();

router.use(authenticateToken);

router.get('/', listOrders);
router.get('/open-summary', requireRole('operador', 'admin'), openOrdersSummary);
router.get('/export/csv', requireRole('operador', 'admin'), exportOrdersCsv);
router.get('/stream', (req, res) => {
	res.setHeader('Content-Type', 'text/event-stream');
	res.setHeader('Cache-Control', 'no-cache');
	res.setHeader('Connection', 'keep-alive');
	res.flushHeaders?.();

	const sendEvent = (event) => {
		const isLoja = req.user.role === 'loja';
		const isSameLoja = !event.lojaId || event.lojaId === req.user.id;
		if (isLoja && !isSameLoja) {
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
router.post('/', requireRole('loja'), createOrder);
router.post('/:id/repeat', requireRole('loja'), repeatOrder);
router.patch('/:id/status', requireRole('operador', 'admin'), updateOrderStatus);

export default router;
