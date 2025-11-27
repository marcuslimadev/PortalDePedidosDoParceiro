-- Migration: Create client credit change history
-- Created: 2025-11-27

CREATE TABLE IF NOT EXISTS client_credit_history (
  id SERIAL PRIMARY KEY,
  client_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
  changed_by INTEGER REFERENCES users(id) ON DELETE SET NULL,
  previous_credit_limit NUMERIC(14,2),
  new_credit_limit NUMERIC(14,2),
  previous_payment_terms VARCHAR(50),
  new_payment_terms VARCHAR(50),
  previous_status VARCHAR(20),
  new_status VARCHAR(20),
  created_at TIMESTAMP WITHOUT TIME ZONE DEFAULT NOW()
);

CREATE INDEX IF NOT EXISTS idx_credit_history_client ON client_credit_history(client_id);
CREATE INDEX IF NOT EXISTS idx_credit_history_changed_at ON client_credit_history(created_at DESC);
