-- Migration: Add client management fields to users table
-- Created: 2025-11-27

ALTER TABLE users
  ADD COLUMN IF NOT EXISTS cnpj VARCHAR(20),
  ADD COLUMN IF NOT EXISTS inscricao_estadual VARCHAR(20),
  ADD COLUMN IF NOT EXISTS rota VARCHAR(100),
  ADD COLUMN IF NOT EXISTS segmentacao VARCHAR(100),
  ADD COLUMN IF NOT EXISTS credit_limit NUMERIC(14,2),
  ADD COLUMN IF NOT EXISTS credit_used NUMERIC(14,2) DEFAULT 0,
  ADD COLUMN IF NOT EXISTS payment_terms VARCHAR(50),
  ADD COLUMN IF NOT EXISTS cliente_status VARCHAR(20) DEFAULT 'ativo';

ALTER TABLE users
  ALTER COLUMN credit_used SET DEFAULT 0;

ALTER TABLE users
  ADD CONSTRAINT IF NOT EXISTS credit_used_non_negative CHECK (credit_used >= 0);

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
