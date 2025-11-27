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
    let sql = 'SELECT id, codigo, descricao, preco, unidade, tributacao, estoque, categoria, winthor_data, created_at, updated_at FROM products';

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

export const listPublicCatalog = async (req, res) => {
  try {
    const { q } = req.query;
    const params = [];
    let sql = `
      SELECT id, codigo, descricao, preco, unidade, tributacao, estoque, categoria, winthor_data
      FROM products`;

    if (q) {
      params.push(`%${q.toLowerCase()}%`);
      sql += ' WHERE LOWER(codigo) LIKE $1 OR LOWER(descricao) LIKE $1';
    }

    sql += ' ORDER BY descricao ASC LIMIT 200';

    const result = await query(sql, params);
    res.json({ products: result.rows });
  } catch (error) {
    console.error('Erro ao listar catálogo público:', error);
    res.status(500).json({ error: 'Erro ao buscar catálogo' });
  }
};

export const createProduct = async (req, res) => {
  try {
    const validationError = validateProductPayload(req.body);
    if (validationError) {
      return res.status(400).json({ error: validationError });
    }

    const { codigo, descricao, preco, unidade, tributacao, estoque = 0, categoria = null, winthor_data: winthorData = {} } = req.body;
    const safeWinthor = typeof winthorData === 'object' && winthorData !== null ? winthorData : {};

    const duplicate = await query('SELECT id FROM products WHERE codigo = $1', [codigo]);
    if (duplicate.rows.length > 0) {
      return res.status(409).json({ error: 'Já existe um produto com este código' });
    }

    const result = await query(
      `INSERT INTO products (codigo, descricao, preco, unidade, tributacao, estoque, categoria, winthor_data)
       VALUES ($1, $2, $3, $4, $5, $6, $7, $8)
       RETURNING id, codigo, descricao, preco, unidade, tributacao, estoque, categoria, winthor_data, created_at, updated_at`,
      [codigo, descricao, preco, unidade, tributacao, estoque, categoria, safeWinthor]
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
    const { codigo, descricao, preco, unidade, tributacao, estoque = 0, categoria = null, winthor_data: winthorData = {} } = req.body;
    const safeWinthor = typeof winthorData === 'object' && winthorData !== null ? winthorData : {};

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
        updated_at = NOW(),
        winthor_data = $8
      WHERE id = $9
      RETURNING id, codigo, descricao, preco, unidade, tributacao, estoque, categoria, winthor_data, created_at, updated_at`,
      [codigo, descricao, preco, unidade, tributacao, estoque, categoria, safeWinthor, id]
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

const baseCsvFields = ['codigo', 'descricao', 'preco', 'unidade', 'tributacao', 'estoque', 'categoria'];

const toCsvValue = (value) => {
  if (value === null || value === undefined) return '';
  const str = String(value).replace(/"/g, '""');
  if (str.includes(';') || str.includes('"') || str.includes('\n')) {
    return `"${str}"`;
  }
  return str;
};

export const exportProductsCsv = async (req, res) => {
  try {
    const result = await query('SELECT codigo, descricao, preco, unidade, tributacao, estoque, categoria, winthor_data FROM products ORDER BY updated_at DESC');
    const rows = result.rows;
    if (rows.length === 0) {
      res.setHeader('Content-Type', 'text/csv; charset=utf-8');
      res.setHeader('Content-Disposition', 'attachment; filename=produtos.csv');
      return res.send(baseCsvFields.join(';'));
    }

    const extraKeys = new Set();
    rows.forEach(row => {
      const data = row.winthor_data || {};
      Object.keys(data).forEach(k => extraKeys.add(k));
    });
    const header = [...baseCsvFields, ...Array.from(extraKeys)];

    const csv = [
      header.join(';'),
      ...rows.map(row => {
        const data = row.winthor_data || {};
        return header.map(key => {
          if (baseCsvFields.includes(key)) {
            return toCsvValue(row[key]);
          }
          return toCsvValue(data[key]);
        }).join(';');
      })
    ].join('\n');

    res.setHeader('Content-Type', 'text/csv; charset=utf-8');
    res.setHeader('Content-Disposition', `attachment; filename=produtos-${Date.now()}.csv`);
    res.send('\ufeff' + csv);
  } catch (error) {
    console.error('Erro ao exportar produtos:', error);
    res.status(500).json({ error: 'Não foi possível exportar produtos' });
  }
};

export const importProductsCsv = async (req, res) => {
  try {
    const { csv } = req.body;
    if (!csv) {
      return res.status(400).json({ error: 'CSV não enviado' });
    }

    const lines = csv.split(/\r?\n/).filter(line => line.trim() !== '');
    if (lines.length === 0) {
      return res.status(400).json({ error: 'CSV vazio' });
    }

    const header = lines[0].split(';').map(h => h.trim());
    const created = [];
    const updated = [];
    let errors = 0;

    for (let i = 1; i < lines.length; i++) {
      const line = lines[i];
      if (!line.trim()) continue;
      const cols = line.split(';');
      const record = {};
      header.forEach((key, idx) => {
        record[key] = cols[idx] !== undefined ? cols[idx].replace(/^"|"$/g, '').replace(/""/g, '"') : '';
      });

      const payload = {
        codigo: record.codigo,
        descricao: record.descricao,
        preco: record.preco ? Number(record.preco) : 0,
        unidade: record.unidade || 'UN',
        tributacao: record.tributacao || 'ICMS',
        estoque: record.estoque ? Number(record.estoque) : 0,
        categoria: record.categoria || null,
        winthor_data: {}
      };

      header.forEach(key => {
        if (!baseCsvFields.includes(key)) {
          payload.winthor_data[key] = record[key] || '';
        }
      });

      try {
        const existing = await query('SELECT id FROM products WHERE codigo = $1', [payload.codigo]);
        if (existing.rows.length > 0) {
          await query(
            `UPDATE products
               SET descricao = $1, preco = $2, unidade = $3, tributacao = $4,
                   estoque = $5, categoria = $6, winthor_data = $7, updated_at = NOW()
             WHERE codigo = $8`,
            [
              payload.descricao,
              payload.preco,
              payload.unidade,
              payload.tributacao,
              payload.estoque,
              payload.categoria,
              payload.winthor_data,
              payload.codigo
            ]
          );
          updated.push(payload.codigo);
        } else {
          await query(
            `INSERT INTO products (codigo, descricao, preco, unidade, tributacao, estoque, categoria, winthor_data)
             VALUES ($1, $2, $3, $4, $5, $6, $7, $8)`,
            [
              payload.codigo,
              payload.descricao,
              payload.preco,
              payload.unidade,
              payload.tributacao,
              payload.estoque,
              payload.categoria,
              payload.winthor_data
            ]
          );
          created.push(payload.codigo);
        }
      } catch (err) {
        console.error(`Erro ao importar linha ${i + 1} (${payload.codigo}):`, err);
        errors += 1;
      }
    }

    res.json({ message: 'Importação concluída', created, updated, errors });
  } catch (error) {
    console.error('Erro ao importar produtos:', error);
    res.status(500).json({ error: 'Não foi possível importar produtos' });
  }
};
