import { query } from '../config/database.js';

export const notifyUser = async (userId, payload) => {
  if (!userId) return;
  await query(
    `INSERT INTO notifications (user_id, type, title, body)
     VALUES ($1, $2, $3, $4)`,
    [userId, payload.type, payload.title, payload.body || null]
  );
};

export const notifyUsers = async (roles, payload) => {
  const roleList = Array.isArray(roles) ? roles : [roles];
  const result = await query(
    `SELECT id FROM users WHERE role = ANY($1::text[]) AND ativo = true`,
    [roleList]
  );

  for (const row of result.rows) {
    await notifyUser(row.id, payload);
  }
};
