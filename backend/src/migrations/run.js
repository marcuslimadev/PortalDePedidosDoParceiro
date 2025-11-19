import { getClient } from '../config/database.js';
import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

async function runMigration() {
  const client = await getClient();
  
  try {
    const migrationSQL = fs.readFileSync(
      path.join(__dirname, '001_create_users.sql'),
      'utf8'
    );
    
    console.log('Running migration: 001_create_users.sql');
    await client.query(migrationSQL);
    console.log('✅ Migration completed successfully!');
  } catch (error) {
    console.error('❌ Migration failed:', error);
    throw error;
  } finally {
    client.release();
  }
}

runMigration()
  .then(() => process.exit(0))
  .catch(() => process.exit(1));
