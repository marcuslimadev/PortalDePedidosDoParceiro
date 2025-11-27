import { query } from '../config/database.js';

export const listNotifications = async (req, res) => {
  try {
    const userId = req.user.id;
    const userRole = req.user.role;

    let notificationsQuery;
    let params;

    if (userRole === 'admin' || userRole === 'operador') {
      notificationsQuery = `
        SELECT n.id, n.type, n.title, n.message, n.order_id, n.read, n.created_at,
               u.nome AS target_user_nome
        FROM notifications n
        LEFT JOIN users u ON u.id = n.user_id
        WHERE n.user_id IS NULL OR n.user_id = $1
        ORDER BY n.created_at DESC
        LIMIT 50
      `;
      params = [userId];
    } else {
      notificationsQuery = `
        SELECT n.id, n.type, n.title, n.message, n.order_id, n.read, n.created_at
        FROM notifications n
        WHERE n.user_id = $1
        ORDER BY n.created_at DESC
        LIMIT 50
      `;
      params = [userId];
    }

    const result = await query(notificationsQuery, params);
    res.json({ notifications: result.rows });
  } catch (error) {
    console.error('Erro ao listar notificações:', error);
    res.status(500).json({ error: 'Erro ao buscar notificações' });
  }
};

export const getUnreadCount = async (req, res) => {
  try {
    const userId = req.user.id;
    const userRole = req.user.role;

    let countQuery;
    let params;

    if (userRole === 'admin' || userRole === 'operador') {
      countQuery = `
        SELECT COUNT(*) as count
        FROM notifications n
        WHERE (n.user_id IS NULL OR n.user_id = $1) AND n.read = false
      `;
      params = [userId];
    } else {
      countQuery = `
        SELECT COUNT(*) as count
        FROM notifications n
        WHERE n.user_id = $1 AND n.read = false
      `;
      params = [userId];
    }

    const result = await query(countQuery, params);
    res.json({ count: parseInt(result.rows[0].count, 10) });
  } catch (error) {
    console.error('Erro ao contar notificações:', error);
    res.status(500).json({ error: 'Erro ao contar notificações' });
  }
};

export const markAsRead = async (req, res) => {
  try {
    const userId = req.user.id;
    const { id } = req.params;

    const result = await query(
      `UPDATE notifications 
       SET read = true 
       WHERE id = $1 AND (user_id = $2 OR user_id IS NULL)
       RETURNING id, read`,
      [id, userId]
    );

    if (result.rowCount === 0) {
      return res.status(404).json({ error: 'Notificação não encontrada' });
    }

    res.json({ notification: result.rows[0] });
  } catch (error) {
    console.error('Erro ao marcar notificação como lida:', error);
    res.status(500).json({ error: 'Erro ao atualizar notificação' });
  }
};

export const markAllAsRead = async (req, res) => {
  try {
    const userId = req.user.id;
    const userRole = req.user.role;

    let updateQuery;
    let params;

    if (userRole === 'admin' || userRole === 'operador') {
      updateQuery = `
        UPDATE notifications 
        SET read = true 
        WHERE (user_id IS NULL OR user_id = $1) AND read = false
      `;
      params = [userId];
    } else {
      updateQuery = `
        UPDATE notifications 
        SET read = true 
        WHERE user_id = $1 AND read = false
      `;
      params = [userId];
    }

    await query(updateQuery, params);
    res.json({ success: true });
  } catch (error) {
    console.error('Erro ao marcar todas notificações como lidas:', error);
    res.status(500).json({ error: 'Erro ao atualizar notificações' });
  }
};

export const createNotification = async (notificationData) => {
  const { userId, type, title, message, orderId } = notificationData;

  try {
    const result = await query(
      `INSERT INTO notifications (user_id, type, title, message, order_id)
       VALUES ($1, $2, $3, $4, $5)
       RETURNING id, user_id, type, title, message, order_id, read, created_at`,
      [userId || null, type, title, message, orderId || null]
    );
    return result.rows[0];
  } catch (error) {
    console.error('Erro ao criar notificação:', error);
    return null;
  }
};
