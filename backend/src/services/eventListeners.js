import { eventBus } from '../events/eventBus.js';
import { notifyUser, notifyUsers } from './notificationService.js';
import { sendEmail, emailTemplates } from './emailService.js';
import { query } from '../config/database.js';

/**
 * Obtém emails de usuários por IDs
 */
const getUserEmails = async (userIds) => {
  if (!Array.isArray(userIds) || userIds.length === 0) return [];

  const placeholders = userIds.map((_, i) => `$${i + 1}`).join(',');
  const result = await query(
    `SELECT email FROM users WHERE id IN (${placeholders}) AND ativo = true`,
    userIds
  );

  return result.rows.map(row => row.email);
};

/**
 * Obtém emails de usuários por roles
 */
const getUserEmailsByRoles = async (roles) => {
  const roleList = Array.isArray(roles) ? roles : [roles];
  const result = await query(
    'SELECT email FROM users WHERE role = ANY($1::text[]) AND ativo = true',
    [roleList]
  );

  return result.rows.map(row => row.email);
};

/**
 * Handler para evento de pedido criado
 */
const handleOrderCreated = async ({ lojaId, payload }) => {
  try {
    const order = payload;
    const lojaNome = payload.loja_nome || 'Loja';

    // Notificação no sistema para admin e operador
    await notifyUsers(['admin', 'operador'], {
      type: 'order',
      title: `Novo pedido #${order.id}`,
      body: `Pedido registrado pela loja ${lojaNome} no valor de R$ ${order.total?.toFixed(2)}`
    });

    // Email para admin e operador
    const adminEmails = await getUserEmailsByRoles(['admin', 'operador']);
    if (adminEmails.length > 0) {
      const template = emailTemplates.novoPedido(order, lojaNome);
      await sendEmail(adminEmails, template.subject, template.html);
    }

    console.log('✅ Notificações enviadas para pedido #' + order.id);
  } catch (error) {
    console.error('Erro ao processar notificação de pedido criado:', error);
  }
};

/**
 * Handler para evento de status atualizado
 */
const handleOrderStatusUpdated = async ({ lojaId, payload }) => {
  try {
    const order = payload;
    const lojaNome = payload.loja_nome || 'Loja';
    const status = order.status;

    // Notificação no sistema para a loja
    await notifyUser(lojaId, {
      type: 'order',
      title: `Pedido #${order.id} atualizado`,
      body: `Status: ${status}`
    });

    // Email para a loja
    const lojaEmails = await getUserEmails([lojaId]);
    if (lojaEmails.length > 0) {
      let template;

      if (status === 'aprovado') {
        template = emailTemplates.pedidoAprovado(order, lojaNome);
      } else if (status === 'cancelado') {
        template = emailTemplates.pedidoCancelado(order, lojaNome);
      } else {
        template = emailTemplates.statusAtualizado(order, lojaNome, status);
      }

      await sendEmail(lojaEmails, template.subject, template.html);
    }

    console.log('✅ Notificação de status enviada para pedido #' + order.id);
  } catch (error) {
    console.error('Erro ao processar notificação de status:', error);
  }
};

/**
 * Handler para evento de pedido cancelado
 */
const handleOrderCancelled = async ({ lojaId, payload, motivo }) => {
  try {
    const order = payload;
    const lojaNome = payload.loja_nome || 'Loja';

    // Notificação no sistema
    await notifyUser(lojaId, {
      type: 'order',
      title: `Pedido #${order.id} cancelado`,
      body: motivo || 'Pedido cancelado'
    });

    await notifyUsers(['admin', 'operador'], {
      type: 'order',
      title: `Pedido #${order.id} cancelado`,
      body: `Loja ${lojaNome} cancelou o pedido`
    });

    // Emails
    const lojaEmails = await getUserEmails([lojaId]);
    const adminEmails = await getUserEmailsByRoles(['admin', 'operador']);

    const template = emailTemplates.pedidoCancelado(order, lojaNome, motivo);

    if (lojaEmails.length > 0) {
      await sendEmail(lojaEmails, template.subject, template.html);
    }

    if (adminEmails.length > 0) {
      await sendEmail(adminEmails, `[Admin] ${template.subject}`, template.html);
    }

    console.log('✅ Notificações de cancelamento enviadas para pedido #' + order.id);
  } catch (error) {
    console.error('Erro ao processar notificação de cancelamento:', error);
  }
};

/**
 * Registra todos os listeners de eventos
 */
export const registerEventListeners = () => {
  eventBus.on('order-event', async (event) => {
    const { type, lojaId, payload, motivo } = event;

    switch (type) {
      case 'order.created':
        await handleOrderCreated({ lojaId, payload });
        break;

      case 'order.status_updated':
        await handleOrderStatusUpdated({ lojaId, payload });
        break;

      case 'order.cancelled':
        await handleOrderCancelled({ lojaId, payload, motivo });
        break;

      default:
        console.log('Evento desconhecido: ' + type);
    }
  });

  console.log('📡 Event listeners registrados');
};
