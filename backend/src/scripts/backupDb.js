import { spawn } from 'child_process';
import fs from 'fs';
import path from 'path';
import dotenv from 'dotenv';

dotenv.config();

const DATABASE_URL = process.env.DATABASE_URL;
if (!DATABASE_URL) {
  console.error('DATABASE_URL não definido. Configure no .env.');
  process.exit(1);
}

const outDir = path.resolve(process.cwd(), 'backups');
if (!fs.existsSync(outDir)) fs.mkdirSync(outDir);

const timestamp = new Date().toISOString().replace(/[:.]/g, '-');
const outFile = path.join(outDir, `backup-${timestamp}.sql`);

console.log('Iniciando backup para', outFile);

// Requer pg_dump instalado no PATH
const child = spawn('pg_dump', ['--format=plain', '--no-owner', DATABASE_URL], { stdio: ['ignore', 'pipe', 'inherit'] });

const writeStream = fs.createWriteStream(outFile);
child.stdout.pipe(writeStream);

child.on('error', (err) => {
  console.error('Erro ao executar pg_dump:', err.message);
  process.exit(1);
});

child.on('close', (code) => {
  if (code === 0) {
    console.log('Backup concluído com sucesso:', outFile);
  } else {
    console.error('pg_dump finalizou com código', code);
    process.exit(code);
  }
});
