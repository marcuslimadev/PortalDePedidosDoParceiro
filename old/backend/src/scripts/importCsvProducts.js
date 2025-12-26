import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';
import { query } from '../config/database.js';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

const CSV_PATH = fs.existsSync('/app/MODELO.csv') 
  ? '/app/MODELO.csv' 
  : path.resolve(__dirname, '../../../MODELO.csv');

function parseCSVContent(content) {
  const records = [];
  let current = '';
  let inQuotes = false;
  
  for (let i = 0; i < content.length; i++) {
    const char = content[i];
    if (char === '"') {
      inQuotes = !inQuotes;
      current += char;
    } else if ((char === '\n' || char === '\r') && !inQuotes) {
      if (current.trim()) records.push(current);
      current = '';
      if (char === '\r' && content[i + 1] === '\n') i++;
    } else {
      current += char;
    }
  }
  if (current.trim()) records.push(current);
  return records;
}

function parseCSVLine(line) {
  const result = [];
  let current = '';
  let inQuotes = false;
  
  for (const char of line) {
    if (char === '"') inQuotes = !inQuotes;
    else if (char === ';' && !inQuotes) {
      result.push(current.trim().replace(/^"|"$/g, ''));
      current = '';
    } else current += char;
  }
  result.push(current.trim().replace(/^"|"$/g, ''));
  return result;
}

function parseNumber(value) {
  if (!value || value === '') return 0;
  const cleaned = value.toString().replace(/\./g, '').replace(',', '.');
  const num = parseFloat(cleaned);
  return isNaN(num) ? 0 : num;
}

function readCSV() {
  console.log(`📂 Lendo arquivo: ${CSV_PATH}`);
  const content = fs.readFileSync(CSV_PATH, 'utf-8');
  const lines = parseCSVContent(content);
  console.log(`📊 Total de linhas: ${lines.length}`);
  
  const headers = parseCSVLine(lines[0]);
  const col = {};
  headers.forEach((h, i) => col[h] = i);
  
  const products = [];
  
  for (let i = 1; i < lines.length; i++) {
    const f = parseCSVLine(lines[i]);
    const codprod = f[col.CODPROD];
    const descricao = f[col.DESCRICAO];
    
    if (!codprod || descricao === 'ZZ' || descricao?.startsWith('ZZ')) continue;
    
    let preco = parseNumber(f[col.CUSTOREPTAB]) || parseNumber(f[col.CUSTOREP]);
    if (preco === 0) preco = Math.round((Math.random() * 48 + 2) * 100) / 100;
    
    products.push({
      codigo: codprod,
      descricao: (descricao || `Produto ${codprod}`).substring(0, 200).replace(/[\n\r]+/g, ' ').trim(),
      preco,
      unidade: f[col.UNIDADE] || 'UN',
      categoria: (f[col.J11_CATEGORIA] || 'GERAL').substring(0, 100).trim(),
      estoque: Math.floor(Math.random() * 500) + 50,
      winthor_data: {
        codprod,
        codfornec: f[col.CODFORNEC] || null,
        fornecedor: f[col.J5_FORNECEDOR] || null,
        departamento: f[col.J6_DESCRICAO] || null,
        secao: f[col.J8_DESCRICAO] || null,
        ncm: f[col.CODNCMEX]?.replace('.', '') || null,
        ean: f[col.GTINCODAUXILIAR]?.replace(',00', '') || null,
      }
    });
  }
  
  console.log(`✅ Produtos válidos: ${products.length}`);
  return products;
}

async function importProducts(products) {
  console.log('🗑️ Limpando dados existentes...');
  await query('DELETE FROM order_items');
  await query('DELETE FROM product_price_history');
  const del = await query('DELETE FROM products');
  console.log(`   Removidos: ${del.rowCount} produtos`);
  await query('ALTER SEQUENCE products_id_seq RESTART WITH 1');
  
  console.log(`📥 Inserindo ${products.length} produtos...`);
  let ok = 0, err = 0;
  
  for (const p of products) {
    try {
      await query(
        `INSERT INTO products (codigo, descricao, preco, unidade, tributacao, estoque, categoria, winthor_data)
         VALUES ($1, $2, $3, $4, 'ICMS', $5, $6, $7)`,
        [p.codigo, p.descricao, p.preco, p.unidade, p.estoque, p.categoria, JSON.stringify(p.winthor_data)]
      );
      ok++;
      if (ok % 100 === 0) console.log(`   Progresso: ${ok}/${products.length}`);
    } catch (e) {
      err++;
      if (err <= 5) console.log(`   ❌ Erro produto ${p.codigo}: ${e.message}`);
    }
  }
  
  console.log(`\n✅ Importação concluída! Inseridos: ${ok}, Erros: ${err}`);
}

async function main() {
  console.log('\n🚀 Importando produtos do CSV\n');
  const products = readCSV();
  await importProducts(products);
  console.log('\n✅ Finalizado!\n');
  process.exit(0);
}

main().catch(e => { console.error('❌ Erro:', e); process.exit(1); });
