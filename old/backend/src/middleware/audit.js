import { logAudit } from '../services/auditService.js';

/**
 * Middleware para auditoria automática de operações
 * Deve ser aplicado após authenticateToken
 */
export const auditMiddleware = (action, resourceType) => {
  return async (req, res, next) => {
    // Armazena a função original res.json
    const originalJson = res.json.bind(res);

    // Sobrescreve res.json para capturar a resposta
    res.json = function (data) {
      // Só registra se a operação foi bem-sucedida (status 2xx)
      if (res.statusCode >= 200 && res.statusCode < 300) {
        const auditData = {
          userId: req.user?.id,
          userEmail: req.user?.email || 'unknown',
          userRole: req.user?.role,
          action,
          resourceType,
          resourceId: data?.id || data?.order?.id || data?.client?.id || req.params?.id,
          description: getDescription(action, resourceType, req, data),
          ipAddress: req.ip || req.connection?.remoteAddress,
          userAgent: req.get('user-agent'),
          newValues: action === 'create' ? sanitizeData(data) : null,
          oldValues: req.auditOldValues || null,
          metadata: {
            method: req.method,
            path: req.path,
            query: req.query,
            params: req.params
          }
        };

        // Executa auditoria de forma assíncrona sem bloquear resposta
        logAudit(auditData).catch(err => {
          console.error('Erro ao registrar auditoria:', err);
        });
      }

      // Chama a função original
      return originalJson(data);
    };

    next();
  };
};

/**
 * Gera descrição automática baseada na ação
 */
const getDescription = (action, resourceType, req, data) => {
  const descriptions = {
    create: {
      order: `Pedido #${data?.order?.id} criado`,
      product: `Produto ${data?.product?.codigo} criado`,
      client: `Cliente ${data?.client?.nome} criado`
    },
    update: {
      order: 'Pedido #' + (req.params?.id || '') + ' atualizado',
      product: 'Produto atualizado',
      client: 'Cliente ' + (req.params?.id || '') + ' atualizado'
    },
    delete: {
      order: 'Pedido #' + (req.params?.id || '') + ' excluído',
      product: 'Produto excluído',
      client: 'Cliente ' + (req.params?.id || '') + ' excluído'
    },
    approve: {
      order: `Pedido #${req.params?.id} aprovado`
    },
    cancel: {
      order: `Pedido #${req.params?.id} cancelado`
    },
    credit_update: {
      client: `Limite de crédito do cliente ${req.params?.id} atualizado`
    }
  };

  return descriptions[action]?.[resourceType] || `${action} em ${resourceType}`;
};

/**
 * Remove dados sensíveis antes de armazenar
 */
const sanitizeData = (data) => {
  if (!data) return null;

  const sanitized = { ...data };

  // Remove campos sensíveis
  delete sanitized.password;
  delete sanitized.password_hash;
  delete sanitized.token;

  return sanitized;
};

/**
 * Helper para capturar valores antigos antes de update
 * Deve ser chamado no controller antes da atualização
 */
export const captureOldValues = (oldValues) => {
  return (req, res, next) => {
    req.auditOldValues = sanitizeData(oldValues);
    next();
  };
};
