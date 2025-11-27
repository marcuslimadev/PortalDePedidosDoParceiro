import bcrypt from 'bcryptjs';
import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';
import { getClient, query } from '../config/database.js';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

const adminUser = {
  email: 'admin@portalpedidos.com',
  nome: 'Administrador',
  role: 'admin',
  password: 'admin123'
};

const operatorUser = {
  email: 'operador@portalpedidos.com',
  nome: 'Operador',
  role: 'operador',
  password: 'operador123'
};

const clientUsers = [
  {
    email: 'loja1@cliente.com',
    nome: 'Mercado Aurora',
    cnpj: '11111111000101',
    rota: 'Capital',
    segmentacao: 'Varejo',
    credit_limit: 8000,
    payment_terms: '30/60'
  },
  {
    email: 'loja2@cliente.com',
    nome: 'Super Atacado Sol',
    cnpj: '22222222000102',
    rota: 'Interior',
    segmentacao: 'Atacado',
    credit_limit: 12000,
    payment_terms: '45 dias'
  },
  {
    email: 'loja3@cliente.com',
    nome: 'Emporio Rio Doce',
    cnpj: '33333333000103',
    rota: 'Litoral',
    segmentacao: 'Food Service',
    credit_limit: 6000,
    payment_terms: '30 dias'
  }
];

const products = [
  { codigo: 'PROD-001', descricao: 'Cafe torrado 500g', preco: 18.9, unidade: 'UN', tributacao: 'ICMS', estoque: 200, categoria: 'Mercearia' },
  { codigo: 'PROD-002', descricao: 'Arroz tipo 1 5kg', preco: 26.5, unidade: 'UN', tributacao: 'ICMS', estoque: 300, categoria: 'Mercearia' },
  { codigo: 'PROD-003', descricao: 'Feijao carioca 1kg', preco: 8.7, unidade: 'UN', tributacao: 'ICMS', estoque: 500, categoria: 'Mercearia' },
  { codigo: 'PROD-004', descricao: 'Acucar refinado 1kg', preco: 4.2, unidade: 'UN', tributacao: 'ICMS', estoque: 450, categoria: 'Mercearia' },
  { codigo: 'PROD-005', descricao: 'Oleo de soja 900ml', preco: 7.8, unidade: 'UN', tributacao: 'ICMS', estoque: 380, categoria: 'Mercearia' },
  { codigo: 'PROD-006', descricao: 'Macarrao espaguete 500g', preco: 5.4, unidade: 'UN', tributacao: 'ICMS', estoque: 320, categoria: 'Massas' },
  { codigo: 'PROD-007', descricao: 'Molho de tomate 340g', preco: 3.9, unidade: 'UN', tributacao: 'ICMS', estoque: 260, categoria: 'Molhos' },
  { codigo: 'PROD-008', descricao: 'Leite integral 1L', preco: 4.9, unidade: 'UN', tributacao: 'ICMS', estoque: 220, categoria: 'Laticinios' },
  { codigo: 'PROD-009', descricao: 'Biscoito recheado 140g', preco: 2.9, unidade: 'UN', tributacao: 'ICMS', estoque: 410, categoria: 'Snacks' },
  { codigo: 'PROD-010', descricao: 'Chocolate em barra 90g', preco: 6.9, unidade: 'UN', tributacao: 'ICMS', estoque: 280, categoria: 'Snacks' },
  { codigo: 'PROD-011', descricao: 'Sabonete neutro 90g', preco: 2.5, unidade: 'UN', tributacao: 'ICMS', estoque: 350, categoria: 'Higiene' },
  { codigo: 'PROD-012', descricao: 'Detergente neutro 500ml', preco: 3.2, unidade: 'UN', tributacao: 'ICMS', estoque: 330, categoria: 'Limpeza' },
  { codigo: 'PROD-013', descricao: 'Alcool liquido 1L', preco: 9.5, unidade: 'UN', tributacao: 'ICMS', estoque: 180, categoria: 'Limpeza' },
  { codigo: 'PROD-014', descricao: 'Desinfetante 2L', preco: 12.4, unidade: 'UN', tributacao: 'ICMS', estoque: 160, categoria: 'Limpeza' },
  { codigo: 'PROD-015', descricao: 'Agua mineral 1.5L', preco: 2.6, unidade: 'UN', tributacao: 'ICMS', estoque: 500, categoria: 'Bebidas' }
];

const orderTemplates = [
  {
    lojaEmail: 'loja1@cliente.com',
    status: 'aprovado',
    payment_terms: '30/60',
    observations: 'Reposicao semanal',
    items: [
      { codigo: 'PROD-001', quantidade: 10 },
      { codigo: 'PROD-002', quantidade: 20 },
      { codigo: 'PROD-005', quantidade: 15 }
    ]
  },
  {
    lojaEmail: 'loja2@cliente.com',
    status: 'pendente',
    payment_terms: '45 dias',
    observations: 'Campanha de fim de mes',
    items: [
      { codigo: 'PROD-003', quantidade: 30 },
      { codigo: 'PROD-006', quantidade: 25 },
      { codigo: 'PROD-010', quantidade: 12 }
    ]
  },
  {
    lojaEmail: 'loja3@cliente.com',
    status: 'aprovado',
    payment_terms: '30 dias',
    observations: 'Reposicao de rota litoral',
    items: [
      { codigo: 'PROD-008', quantidade: 40 },
      { codigo: 'PROD-009', quantidade: 50 },
      { codigo: 'PROD-015', quantidade: 60 }
    ]
  }
];

const hashPassword = async (plain) => bcrypt.hash(plain, 10);

async function upsertUser (user) {
  const passwordHash = await hashPassword(user.password);
  const result = await query(
    `INSERT INTO users (email, nome, role, password_hash, ativo, cnpj, rota, segmentacao, credit_limit, payment_terms, cliente_status)
     VALUES ($1, $2, $3, $4, true, $5, $6, $7, $8, $9, 'ativo')
     ON CONFLICT (email) DO UPDATE
       SET nome = EXCLUDED.nome,
           role = EXCLUDED.role,
           password_hash = EXCLUDED.password_hash,
           ativo = true,
           cnpj = COALESCE(EXCLUDED.cnpj, users.cnpj),
           rota = COALESCE(EXCLUDED.rota, users.rota),
           segmentacao = COALESCE(EXCLUDED.segmentacao, users.segmentacao),
           credit_limit = EXCLUDED.credit_limit,
           payment_terms = EXCLUDED.payment_terms,
           cliente_status = 'ativo',
           updated_at = NOW()
     RETURNING id, email, role`,
    [
      user.email,
      user.nome,
      user.role,
      passwordHash,
      user.cnpj || null,
      user.rota || null,
      user.segmentacao || null,
      user.credit_limit || null,
      user.payment_terms || null
    ]
  );
  return result.rows[0];
}

async function upsertProduct (product) {
  const result = await query(
    `INSERT INTO products (codigo, descricao, preco, unidade, tributacao, estoque, categoria)
     VALUES ($1, $2, $3, $4, $5, $6, $7)
     ON CONFLICT (codigo) DO UPDATE
       SET descricao = EXCLUDED.descricao,
           preco = EXCLUDED.preco,
           unidade = EXCLUDED.unidade,
           tributacao = EXCLUDED.tributacao,
           estoque = EXCLUDED.estoque,
           categoria = EXCLUDED.categoria,
           updated_at = NOW()
     RETURNING id, codigo, preco`,
    [
      product.codigo,
      product.descricao,
      product.preco,
      product.unidade,
      product.tributacao,
      product.estoque,
      product.categoria
    ]
  );
  return result.rows[0];
}

async function createOrderWithItems (orderTemplate, userMap, productMap) {
  const clientUser = userMap.get(orderTemplate.lojaEmail);
  if (!clientUser) return;

  const client = await getClient();
  try {
    await client.query('BEGIN');

    const itemsData = orderTemplate.items.map(item => {
      const product = productMap.get(item.codigo);
      if (!product) throw new Error(`Produto ${item.codigo} nao encontrado`);
      const subtotal = Number(product.preco) * Number(item.quantidade);
      return { product, quantidade: item.quantidade, subtotal };
    });

    const total = itemsData.reduce((sum, item) => sum + item.subtotal, 0);

    const orderResult = await client.query(
      `INSERT INTO orders (loja_id, status, payment_terms, observations, total)
       VALUES ($1, $2, $3, $4, $5)
       RETURNING id`,
      [clientUser.id, orderTemplate.status, orderTemplate.payment_terms, orderTemplate.observations, total]
    );

    const orderId = orderResult.rows[0].id;

    for (const item of itemsData) {
      await client.query(
        `INSERT INTO order_items (order_id, product_id, quantidade, preco_unitario, subtotal)
         VALUES ($1, $2, $3, $4, $5)`,
        [orderId, item.product.id, item.quantidade, item.product.preco, item.subtotal]
      );
    }

    await client.query(
      `UPDATE users
          SET credit_used = COALESCE(credit_used, 0) + $1,
              updated_at = NOW()
        WHERE id = $2`,
      [total, clientUser.id]
    );

    await client.query('COMMIT');
  } catch (error) {
    await client.query('ROLLBACK');
    throw error;
  } finally {
    client.release?.();
  }
}

async function ensureMigrations () {
  console.log('>> Checando estrutura do banco...');
  const client = await getClient();
  try {
    const usersTable = await client.query("SELECT to_regclass('public.users') AS reg");
    const usersExists = !!usersTable.rows[0]?.reg;

    if (!usersExists) {
      console.log('Tabela users nao encontrada. Executando todas as migrations...');
      const migrationFiles = fs
        .readdirSync(path.join(__dirname, '../migrations'))
        .filter(file => file.endsWith('.sql'))
        .sort();

      for (const file of migrationFiles) {
        const sql = fs.readFileSync(path.join(__dirname, '../migrations', file), 'utf8');
        console.log(`-- Migration ${file}`);
        await client.query(sql);
      }
      console.log('Migrations basicas aplicadas.');
      return;
    }

    const hasCnpj = await client.query(`
      SELECT 1 FROM information_schema.columns
       WHERE table_name = 'users' AND column_name = 'cnpj'
       LIMIT 1
    `);

    if (hasCnpj.rowCount === 0) {
      console.log('Campos de cliente nao encontrados. Aplicando migrations 005 e 006...');
      for (const file of ['005_alter_users_add_client_fields.sql', '006_create_client_credit_history.sql']) {
        const sql = fs.readFileSync(path.join(__dirname, '../migrations', file), 'utf8');
        console.log(`-- Migration ${file}`);
        await client.query(sql);
      }
      console.log('Migrations de cliente aplicadas.');
    } else {
      console.log('Estrutura ja contem campos de cliente. Nenhuma migration aplicada.');
    }
  } catch (error) {
    console.error('Falha ao checar/aplicar migrations:', error);
    process.exit(1);
  } finally {
    client.release();
  }
}

async function runSeed () {
  await ensureMigrations();
  console.log('>> Iniciando carga de dados mock...');

  const admin = await upsertUser(adminUser);
  const operador = await upsertUser(operatorUser);
  console.log('Usuarios base:', { admin, operador });

  const clientResults = [];
  for (const client of clientUsers) {
    const created = await upsertUser({ ...client, role: 'loja', password: 'cliente123' });
    clientResults.push(created);
  }
  console.log(`Lojas criadas/atualizadas: ${clientResults.length}`);

  const productResults = [];
  for (const product of products) {
    const created = await upsertProduct(product);
    productResults.push(created);
  }
  const productMap = new Map(productResults.map(p => [p.codigo, p]));
  const userMap = new Map([...clientResults, admin, operador].map(u => [u.email, u]));

  for (const template of orderTemplates) {
    await createOrderWithItems(template, userMap, productMap);
  }
  console.log(`Pedidos criados: ${orderTemplates.length}`);

  console.log('Carga mock concluida.');
  console.log('Acessos disponiveis:');
  console.log('- Admin: admin@portalpedidos.com / admin123');
  console.log('- Operador: operador@portalpedidos.com / operador123');
  console.log('- Clientes: loja1@cliente.com, loja2@cliente.com, loja3@cliente.com (senha cliente123)');
}

runSeed()
  .then(() => {
    console.log('Seed finalizado.');
    process.exit(0);
  })
  .catch((error) => {
    console.error('Falha ao executar seed:', error);
    process.exit(1);
  });
