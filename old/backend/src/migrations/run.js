import { getClient } from '../config/database.js';
import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

export async function runMigrations () {
  console.log('📊 Iniciando sistema de migrations...');
  const client = await getClient();

  try {
    // Create migrations tracking table if it doesn't exist
    console.log('🔧 Verificando tabela de controle schema_migrations...');
    await client.query(`
      CREATE TABLE IF NOT EXISTS schema_migrations (
        id SERIAL PRIMARY KEY,
        filename VARCHAR(255) NOT NULL UNIQUE,
        applied_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
      )
    `);
    console.log('✅ Tabela de controle pronta');

    const migrationFiles = fs
      .readdirSync(__dirname)
      .filter(file => file.endsWith('.sql'))
      .sort();

    console.log(`📋 Encontradas ${migrationFiles.length} migrations`);

    for (const file of migrationFiles) {
      // Check if migration already applied
      const { rows } = await client.query(
        'SELECT filename FROM schema_migrations WHERE filename = $1',
        [file]
      );

      if (rows.length > 0) {
        console.log(`⏭️  Skipping ${file} (already applied)`);
        continue;
      }

      // Run migration
      const migrationSQL = fs.readFileSync(path.join(__dirname, file), 'utf8');
      console.log(`🔄 Running migration: ${file}`);
      await client.query(migrationSQL);

      // Record migration as applied
      await client.query(
        'INSERT INTO schema_migrations (filename) VALUES ($1)',
        [file]
      );

      console.log(`✅ ${file} aplicada com sucesso`);
    }

    console.log('✅ Todas as migrations foram executadas.');
  } catch (error) {
    console.error('❌ Migration failed:', error);
    throw error;
  } finally {
    client.release();
  }
}

// Apenas executa se rodado diretamente via CLI
const isMainModule = process.argv[1] && process.argv[1].includes('migrations/run');
if (isMainModule) {
  runMigrations()
    .then(() => process.exit(0))
    .catch(() => process.exit(1));
}
