import bcrypt from 'bcryptjs';
import { query } from '../config/database.js';

const allowedStatuses = ['ativo', 'inativo', 'bloqueado'];

const sanitizeDocument = (value, currentValue) => {
  if (value === undefined) return currentValue;
  if (value === null || value === '') return null;
  return value.replace(/\D/g, '').slice(0, 20);
};

const parseCreditLimit = (value, currentValue) => {
  if (value === undefined) {
    return currentValue;
  }

  if (value === null || value === '') {
    return null;
  }

  const numeric = Number(value);
  if (Number.isNaN(numeric) || numeric < 0) {
    throw new Error('Limite de crédito inválido');
  }

  return numeric;
};

const hasChanged = (previous, next) => {
  if (previous === null || previous === undefined) {
    return next !== null && next !== undefined;
  }
  if (next === null || next === undefined) {
    return previous !== null && previous !== undefined;
  }
  if (typeof previous === 'number' || typeof next === 'number') {
    return Number(previous) !== Number(next);
  }
  return previous !== next;
};

export const listClients = async (req, res) => {
  try {
    const result = await query(
      `SELECT id, nome, email, cnpj, inscricao_estadual, rota, segmentacao,
              credit_limit, credit_used, payment_terms, cliente_status
         FROM users
        WHERE role = 'loja'
        ORDER BY nome`
    );

    res.json({ clients: result.rows });
  } catch (error) {
    console.error('Erro ao listar clientes:', error);
    res.status(500).json({ error: 'Falha ao buscar clientes' });
  }
};

export const getClient = async (req, res) => {
  const { id } = req.params;

  try {
    const result = await query(
      `SELECT id, nome, email, cnpj, inscricao_estadual, rota, segmentacao,
              credit_limit, credit_used, payment_terms, cliente_status
         FROM users
        WHERE id = $1 AND role = 'loja'`,
      [id]
    );

    if (result.rows.length === 0) {
      return res.status(404).json({ error: 'Cliente não encontrado' });
    }

    res.json({ client: result.rows[0] });
  } catch (error) {
    console.error('Erro ao buscar cliente:', error);
    res.status(500).json({ error: 'Falha ao buscar cliente' });
  }
};

export const updateClient = async (req, res) => {
  const { id } = req.params;
  const {
    cnpj,
    inscricao_estadual: ie,
    rota,
    segmentacao,
    credit_limit: creditLimitValue,
    payment_terms: paymentTerms,
    cliente_status: status
  } = req.body;

  try {
    const existing = await query(
      `SELECT id, nome, email, cnpj, inscricao_estadual, rota, segmentacao,
              credit_limit, credit_used, payment_terms, cliente_status
         FROM users
        WHERE id = $1 AND role = 'loja'`,
      [id]
    );

    if (existing.rows.length === 0) {
      return res.status(404).json({ error: 'Cliente não encontrado' });
    }

    if (status && !allowedStatuses.includes(status)) {
      return res.status(400).json({ error: 'Status inválido' });
    }

    const current = existing.rows[0];
    const updatedFields = {
      cnpj: sanitizeDocument(cnpj, current.cnpj),
      inscricao_estadual: sanitizeDocument(ie, current.inscricao_estadual),
      rota: rota === undefined ? current.rota : (rota || null),
      segmentacao: segmentacao === undefined ? current.segmentacao : (segmentacao || null),
      credit_limit: parseCreditLimit(creditLimitValue, current.credit_limit),
      payment_terms: paymentTerms === undefined ? current.payment_terms : (paymentTerms || null),
      cliente_status: status === undefined ? current.cliente_status : status
    };

    const result = await query(
      `UPDATE users
          SET cnpj = $1,
              inscricao_estadual = $2,
              rota = $3,
              segmentacao = $4,
              credit_limit = $5,
              payment_terms = $6,
              cliente_status = $7,
              updated_at = NOW()
        WHERE id = $8 AND role = 'loja'
        RETURNING id, nome, email, cnpj, inscricao_estadual, rota, segmentacao,
                  credit_limit, credit_used, payment_terms, cliente_status`,
      [
        updatedFields.cnpj,
        updatedFields.inscricao_estadual,
        updatedFields.rota,
        updatedFields.segmentacao,
        updatedFields.credit_limit,
        updatedFields.payment_terms,
        updatedFields.cliente_status,
        id
      ]
    );

    const updated = result.rows[0];

    const creditChanged = hasChanged(current.credit_limit, updated.credit_limit);
    const termsChanged = hasChanged(current.payment_terms, updated.payment_terms);
    const statusChanged = hasChanged(current.cliente_status, updated.cliente_status);

    if (creditChanged || termsChanged || statusChanged) {
      await query(
        `INSERT INTO client_credit_history (
            client_id,
            changed_by,
            previous_credit_limit,
            new_credit_limit,
            previous_payment_terms,
            new_payment_terms,
            previous_status,
            new_status
          ) VALUES ($1, $2, $3, $4, $5, $6, $7, $8)`
        , [
          updated.id,
          req.user?.id || null,
          current.credit_limit,
          updated.credit_limit,
          current.payment_terms,
          updated.payment_terms,
          current.cliente_status,
          updated.cliente_status
        ]
      );
    }

    res.json({ client: updated });
  } catch (error) {
    if (error.message === 'Limite de crédito inválido') {
      return res.status(400).json({ error: error.message });
    }

    console.error('Erro ao atualizar cliente:', error);
    res.status(500).json({ error: 'Falha ao atualizar cliente' });
  }
};

export const listClientHistory = async (req, res) => {
  const { id } = req.params;

  try {
    const result = await query(
      `SELECT h.id,
              h.previous_credit_limit,
              h.new_credit_limit,
              h.previous_payment_terms,
              h.new_payment_terms,
              h.previous_status,
              h.new_status,
              h.created_at,
              u.nome AS autor
         FROM client_credit_history h
    LEFT JOIN users u ON u.id = h.changed_by
        WHERE h.client_id = $1
        ORDER BY h.created_at DESC`,
      [id]
    );

    res.json({ history: result.rows });
  } catch (error) {
    console.error('Erro ao buscar histórico do cliente:', error);
    res.status(500).json({ error: 'Falha ao buscar histórico' });
  }
};

export const setClientAccess = async (req, res) => {
  const { id } = req.params;
  const { email, password, nome } = req.body;

  const normalizedEmail = email?.trim().toLowerCase();
  const normalizedPassword = password?.trim();

  if (!normalizedEmail || !normalizedPassword) {
    return res.status(400).json({ error: 'Email e senha do portal são obrigatórios' });
  }

  if (normalizedPassword.length < 6) {
    return res.status(400).json({ error: 'Senha precisa ter ao menos 6 caracteres' });
  }

  try {
    const existing = await query(
      `SELECT id, role FROM users WHERE id = $1 AND role = 'loja'`,
      [id]
    );

    if (existing.rows.length === 0) {
      return res.status(404).json({ error: 'Cliente não encontrado para acesso' });
    }

    const emailInUse = await query(
      'SELECT id FROM users WHERE email = $1 AND id <> $2',
      [normalizedEmail, id]
    );

    if (emailInUse.rows.length > 0) {
      return res.status(409).json({ error: 'Email já utilizado por outro usuário' });
    }

    const passwordHash = await bcrypt.hash(normalizedPassword, 10);

    const result = await query(
      `UPDATE users
          SET email = $1,
              password_hash = $2,
              nome = COALESCE($3, nome),
              ativo = true,
              updated_at = NOW()
        WHERE id = $4 AND role = 'loja'
        RETURNING id, nome, email, cnpj, inscricao_estadual, rota, segmentacao,
                  credit_limit, credit_used, payment_terms, cliente_status`,
      [normalizedEmail, passwordHash, nome, id]
    );

    if (result.rows.length === 0) {
      return res.status(404).json({ error: 'Cliente não encontrado' });
    }

    res.json({
      client: result.rows[0],
      temporary_password: normalizedPassword
    });
  } catch (error) {
    console.error('Erro ao configurar acesso do cliente:', error);
    res.status(500).json({ error: 'Não foi possível configurar o acesso do cliente' });
  }
};
