-- Migration: Add client management fields to users table
-- Created: 2025-11-27

DO $$
BEGIN
  IF NOT EXISTS (SELECT 1 FROM information_schema.columns WHERE table_name = 'users' AND column_name = 'cnpj') THEN
    ALTER TABLE users ADD COLUMN cnpj VARCHAR(20);
  END IF;
  IF NOT EXISTS (SELECT 1 FROM information_schema.columns WHERE table_name = 'users' AND column_name = 'inscricao_estadual') THEN
    ALTER TABLE users ADD COLUMN inscricao_estadual VARCHAR(20);
  END IF;
  IF NOT EXISTS (SELECT 1 FROM information_schema.columns WHERE table_name = 'users' AND column_name = 'rota') THEN
    ALTER TABLE users ADD COLUMN rota VARCHAR(100);
  END IF;
  IF NOT EXISTS (SELECT 1 FROM information_schema.columns WHERE table_name = 'users' AND column_name = 'segmentacao') THEN
    ALTER TABLE users ADD COLUMN segmentacao VARCHAR(100);
  END IF;
  IF NOT EXISTS (SELECT 1 FROM information_schema.columns WHERE table_name = 'users' AND column_name = 'credit_limit') THEN
    ALTER TABLE users ADD COLUMN credit_limit NUMERIC(14,2);
  END IF;
  IF NOT EXISTS (SELECT 1 FROM information_schema.columns WHERE table_name = 'users' AND column_name = 'credit_used') THEN
    ALTER TABLE users ADD COLUMN credit_used NUMERIC(14,2) DEFAULT 0;
  END IF;
  IF NOT EXISTS (SELECT 1 FROM information_schema.columns WHERE table_name = 'users' AND column_name = 'payment_terms') THEN
    ALTER TABLE users ADD COLUMN payment_terms VARCHAR(50);
  END IF;
  IF NOT EXISTS (SELECT 1 FROM information_schema.columns WHERE table_name = 'users' AND column_name = 'cliente_status') THEN
    ALTER TABLE users ADD COLUMN cliente_status VARCHAR(20) DEFAULT 'ativo';
  END IF;
END $$;

ALTER TABLE users ALTER COLUMN credit_used SET DEFAULT 0;

DO $$
BEGIN
  IF NOT EXISTS (
    SELECT 1 FROM pg_constraint WHERE conname = 'credit_used_non_negative'
  ) THEN
    ALTER TABLE users
      ADD CONSTRAINT credit_used_non_negative CHECK (credit_used >= 0);
  END IF;
END $$;

UPDATE users
   SET cliente_status = CASE
       WHEN cliente_status IS NULL AND ativo = true THEN 'ativo'
       WHEN cliente_status IS NULL AND (ativo = false OR ativo IS NULL) THEN 'inativo'
       ELSE cliente_status
     END;

UPDATE users
   SET credit_used = 0
 WHERE credit_used IS NULL;

CREATE INDEX IF NOT EXISTS idx_users_cliente_status ON users(cliente_status);
