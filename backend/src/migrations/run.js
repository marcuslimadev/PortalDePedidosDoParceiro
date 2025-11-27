import { getClient } from '../config/database.js';
import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

export async function runMigrations () {
  const client = await getClient();

  try {
    const migrationFiles = fs
      .readdirSync(__dirname)
      .filter(file => file.endsWith('.sql'))
      .sort();

    for (const file of migrationFiles) {
      const migrationSQL = fs.readFileSync(path.join(__dirname, file), 'utf8');
      console.log(`Running migration: ${file}`);
      await client.query(migrationSQL);
      console.log(`✅ ${file} aplicada com sucesso`);
    }

    console.log('Todas as migrations foram executadas.');
  } catch (error) {
    console.error('❌ Migration failed:', error);
    throw error;
  } finally {
    client.release();
  }
}

if (import.meta.url === `file://${__filename}`) {
  runMigrations()
    .then(() => process.exit(0))
    .catch(() => process.exit(1));
}
