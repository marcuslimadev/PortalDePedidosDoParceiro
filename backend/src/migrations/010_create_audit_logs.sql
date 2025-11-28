-- Migration: Create audit_logs table
-- Created: 2025-11-28

-- Create enum for audit action types
DO $$
BEGIN
  IF NOT EXISTS (SELECT 1 FROM pg_type WHERE typname = 'audit_action') THEN
    CREATE TYPE audit_action AS ENUM (
      'create', 'update', 'delete', 'approve', 'reject', 'cancel',
      'login', 'logout', 'credit_update', 'export', 'import'
    );
  END IF;
END$$;

-- Create audit_logs table
CREATE TABLE IF NOT EXISTS audit_logs (
  id SERIAL PRIMARY KEY,
  user_id INTEGER REFERENCES users(id) ON DELETE SET NULL,
  user_email VARCHAR(255) NOT NULL,
  user_role VARCHAR(50),
  action audit_action NOT NULL,
  resource_type VARCHAR(100) NOT NULL,
  resource_id INTEGER,
  description TEXT,
  ip_address VARCHAR(45),
  user_agent TEXT,
  old_values JSONB,
  new_values JSONB,
  metadata JSONB,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create indexes for efficient querying
CREATE INDEX IF NOT EXISTS idx_audit_user_id ON audit_logs(user_id);
CREATE INDEX IF NOT EXISTS idx_audit_action ON audit_logs(action);
CREATE INDEX IF NOT EXISTS idx_audit_resource ON audit_logs(resource_type, resource_id);
CREATE INDEX IF NOT EXISTS idx_audit_created_at ON audit_logs(created_at DESC);
CREATE INDEX IF NOT EXISTS idx_audit_user_email ON audit_logs(user_email);

-- Add comment
COMMENT ON TABLE audit_logs IS 'Registro de auditoria de todas as operações críticas do sistema';
COMMENT ON COLUMN audit_logs.old_values IS 'Valores antes da operação (para updates)';
COMMENT ON COLUMN audit_logs.new_values IS 'Valores depois da operação';
COMMENT ON COLUMN audit_logs.metadata IS 'Informações adicionais sobre a operação';
