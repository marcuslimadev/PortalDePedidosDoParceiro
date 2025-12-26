import { query } from '../config/database.js';

export const listNotifications = async (req, res) => {
  try {
    const limitParam = Number(req.query.limit) || 20;
    const limit = Math.min(Math.max(limitParam, 1), 200);

    const result = await query(
      `SELECT id, type, title, body, read_at, created_at
         FROM notifications
        WHERE user_id = $1
        ORDER BY created_at DESC
        LIMIT $2`,
      [req.user.id, limit]
    );

    const unreadCount = await query(
      'SELECT COUNT(*) AS total FROM notifications WHERE user_id = $1 AND read_at IS NULL',
      [req.user.id]
    );

    res.json({
      notifications: result.rows,
      meta: { unread: Number(unreadCount.rows[0]?.total || 0) }
    });
  } catch (error) {
    console.error('Erro ao listar notificações:', error);
    res.status(500).json({ error: 'Erro ao buscar notificações' });
  }
};

export const markAsRead = async (req, res) => {
  const { id } = req.params;

  try {
    const result = await query(
      `UPDATE notifications
          SET read_at = COALESCE(read_at, NOW())
        WHERE id = $1 AND user_id = $2
        RETURNING id, type, title, body, read_at, created_at`,
      [id, req.user.id]
    );

    if (result.rows.length === 0) {
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
    await query(
      `UPDATE notifications
          SET read_at = NOW()
        WHERE user_id = $1 AND read_at IS NULL`,
      [req.user.id]
    );

    res.json({ message: 'Notificações marcadas como lidas' });
  } catch (error) {
    console.error('Erro ao marcar notificações como lidas:', error);
    res.status(500).json({ error: 'Erro ao atualizar notificações' });
  }
};
