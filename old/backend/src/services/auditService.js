import { query } from '../config/database.js';

/**
 * Registra uma ação de auditoria no sistema
 * @param {Object} params - Parâmetros do log de auditoria
 * @param {number} params.userId - ID do usuário que executou a ação
 * @param {string} params.userEmail - Email do usuário
 * @param {string} params.userRole - Role do usuário
 * @param {string} params.action - Tipo de ação (create, update, delete, etc)
 * @param {string} params.resourceType - Tipo de recurso afetado (order, product, client, etc)
 * @param {number} params.resourceId - ID do recurso afetado
 * @param {string} params.description - Descrição da ação
 * @param {string} params.ipAddress - Endereço IP do usuário
 * @param {string} params.userAgent - User agent do navegador
 * @param {Object} params.oldValues - Valores anteriores (para updates)
 * @param {Object} params.newValues - Novos valores
 * @param {Object} params.metadata - Informações adicionais
 */
export const logAudit = async (params) => {
  const {
    userId = null,
    userEmail,
    userRole = null,
    action,
    resourceType,
    resourceId = null,
    description = null,
    ipAddress = null,
    userAgent = null,
    oldValues = null,
    newValues = null,
    metadata = null
  } = params;

  try {
    await query(
      `INSERT INTO audit_logs (
        user_id, user_email, user_role, action, resource_type, resource_id,
        description, ip_address, user_agent, old_values, new_values, metadata
      ) VALUES ($1, $2, $3, $4, $5, $6, $7, $8, $9, $10, $11, $12)`,
      [
        userId,
        userEmail,
        userRole,
        action,
        resourceType,
        resourceId,
        description,
        ipAddress,
        userAgent,
        oldValues ? JSON.stringify(oldValues) : null,
        newValues ? JSON.stringify(newValues) : null,
        metadata ? JSON.stringify(metadata) : null
      ]
    );
  } catch (error) {
    console.error('Erro ao registrar log de auditoria:', error);
    // Não propagar erro para não quebrar a operação principal
  }
};

/**
 * Busca logs de auditoria com filtros
 * @param {Object} filters - Filtros de busca
 * @returns {Promise<Array>} Lista de logs de auditoria
 */
export const getAuditLogs = async (filters = {}) => {
  const {
    userId = null,
    action = null,
    resourceType = null,
    resourceId = null,
    startDate = null,
    endDate = null,
    limit = 100,
    offset = 0
  } = filters;

  const conditions = [];
  const params = [];
  let paramIndex = 1;

  if (userId) {
    conditions.push(`user_id = $${paramIndex++}`);
    params.push(userId);
  }

  if (action) {
    conditions.push(`action = $${paramIndex++}`);
    params.push(action);
  }

  if (resourceType) {
    conditions.push(`resource_type = $${paramIndex++}`);
    params.push(resourceType);
  }

  if (resourceId) {
    conditions.push(`resource_id = $${paramIndex++}`);
    params.push(resourceId);
  }

  if (startDate) {
    conditions.push(`created_at >= $${paramIndex++}`);
    params.push(startDate);
  }

  if (endDate) {
    conditions.push(`created_at <= $${paramIndex++}`);
    params.push(endDate);
  }

  const whereClause = conditions.length > 0 ? `WHERE ${conditions.join(' AND ')}` : '';

  params.push(limit, offset);

  const result = await query(
    `SELECT 
      id, user_id, user_email, user_role, action, resource_type, resource_id,
      description, ip_address, old_values, new_values, metadata, created_at
    FROM audit_logs
    ${whereClause}
    ORDER BY created_at DESC
    LIMIT $${paramIndex++} OFFSET $${paramIndex++}`,
    params
  );

  return result.rows;
};

/**
 * Obtém estatísticas de auditoria
 */
export const getAuditStats = async () => {
  const result = await query(`
    SELECT 
      action,
      resource_type,
      COUNT(*) as count,
      MAX(created_at) as last_action
    FROM audit_logs
    WHERE created_at > NOW() - INTERVAL '30 days'
    GROUP BY action, resource_type
    ORDER BY count DESC
  `);

  return result.rows;
};
