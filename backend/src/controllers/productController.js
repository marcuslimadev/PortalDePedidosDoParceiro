import { query } from '../config/database.js';

const requiredFields = ['codigo', 'descricao', 'preco', 'unidade', 'tributacao'];

const validateProductPayload = (body) => {
  const missing = requiredFields.filter(field => !body[field]);
  if (missing.length > 0) {
    return `Campos obrigatórios ausentes: ${missing.join(', ')}`;
  }

  if (isNaN(body.preco) || Number(body.preco) < 0) {
    return 'Preço deve ser um número maior ou igual a zero';
  }

  return null;
};

export const listProducts = async (req, res) => {
  try {
    const { q } = req.query;
    const params = [];
    let sql = 'SELECT id, codigo, descricao, preco, unidade, tributacao, estoque, categoria, created_at, updated_at FROM products';

    if (q) {
      params.push(`%${q.toLowerCase()}%`);
      sql += ' WHERE LOWER(codigo) LIKE $1 OR LOWER(descricao) LIKE $1';
    }

    sql += ' ORDER BY updated_at DESC';

    const result = await query(sql, params);
    res.json({ products: result.rows });
  } catch (error) {
    console.error('Erro ao listar produtos:', error);
    res.status(500).json({ error: 'Erro ao buscar produtos' });
  }
};

export const createProduct = async (req, res) => {
  try {
    const validationError = validateProductPayload(req.body);
    if (validationError) {
      return res.status(400).json({ error: validationError });
    }

    const { codigo, descricao, preco, unidade, tributacao, estoque = 0, categoria = null } = req.body;

    const duplicate = await query('SELECT id FROM products WHERE codigo = $1', [codigo]);
    if (duplicate.rows.length > 0) {
      return res.status(409).json({ error: 'Já existe um produto com este código' });
    }

    const result = await query(
      `INSERT INTO products (codigo, descricao, preco, unidade, tributacao, estoque, categoria)
       VALUES ($1, $2, $3, $4, $5, $6, $7)
       RETURNING id, codigo, descricao, preco, unidade, tributacao, estoque, categoria, created_at, updated_at`,
      [codigo, descricao, preco, unidade, tributacao, estoque, categoria]
    );

    res.status(201).json({ product: result.rows[0] });
  } catch (error) {
    console.error('Erro ao criar produto:', error);
    res.status(500).json({ error: 'Erro ao criar produto' });
  }
};

export const updateProduct = async (req, res) => {
  try {
    const validationError = validateProductPayload(req.body);
    if (validationError) {
      return res.status(400).json({ error: validationError });
    }

    const { id } = req.params;
    const { codigo, descricao, preco, unidade, tributacao, estoque = 0, categoria = null } = req.body;

    const existing = await query('SELECT id, preco FROM products WHERE id = $1', [id]);
    if (existing.rows.length === 0) {
      return res.status(404).json({ error: 'Produto não encontrado' });
    }

    const duplicate = await query('SELECT id FROM products WHERE codigo = $1 AND id != $2', [codigo, id]);
    if (duplicate.rows.length > 0) {
      return res.status(409).json({ error: 'Já existe outro produto com este código' });
    }

    const result = await query(
      `UPDATE products SET
        codigo = $1,
        descricao = $2,
        preco = $3,
        unidade = $4,
        tributacao = $5,
        estoque = $6,
        categoria = $7,
        updated_at = NOW()
      WHERE id = $8
      RETURNING id, codigo, descricao, preco, unidade, tributacao, estoque, categoria, created_at, updated_at`,
      [codigo, descricao, preco, unidade, tributacao, estoque, categoria, id]
    );

    const previousPrice = existing.rows[0].preco;
    if (Number(previousPrice) !== Number(preco)) {
      await query(
        `INSERT INTO product_price_history (product_id, preco_anterior, preco_novo, changed_by)
         VALUES ($1, $2, $3, $4)`,
        [id, previousPrice, preco, req.user?.id || null]
      );
    }

    res.json({ product: result.rows[0] });
  } catch (error) {
    console.error('Erro ao atualizar produto:', error);
    res.status(500).json({ error: 'Erro ao atualizar produto' });
  }
};

export const getProductPriceHistory = async (req, res) => {
  try {
    const { id } = req.params;

    const history = await query(
      `SELECT h.id, h.preco_anterior, h.preco_novo, h.changed_at, u.nome AS usuario
       FROM product_price_history h
       LEFT JOIN users u ON u.id = h.changed_by
       WHERE h.product_id = $1
       ORDER BY h.changed_at DESC`,
      [id]
    );

    res.json({ history: history.rows });
  } catch (error) {
    console.error('Erro ao buscar histórico de preços:', error);
    res.status(500).json({ error: 'Erro ao buscar histórico de preços' });
  }
};

export const deleteProduct = async (req, res) => {
  try {
    const { id } = req.params;
    const result = await query('DELETE FROM products WHERE id = $1 RETURNING id', [id]);

    if (result.rows.length === 0) {
      return res.status(404).json({ error: 'Produto não encontrado' });
    }

    res.json({ message: 'Produto removido com sucesso' });
  } catch (error) {
    console.error('Erro ao remover produto:', error);
    res.status(500).json({ error: 'Erro ao remover produto' });
  }
};
