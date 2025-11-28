-- Migration: Create Winthor sync logs
-- Created: 2025-11-28

DO $$
BEGIN
  IF NOT EXISTS (SELECT 1 FROM pg_type WHERE typname = 'winthor_sync_type') THEN
    CREATE TYPE winthor_sync_type AS ENUM ('export_orders', 'import_products', 'import_clients');
  END IF;
END$$;

CREATE TABLE IF NOT EXISTS winthor_sync_logs (
  id SERIAL PRIMARY KEY,
  type winthor_sync_type NOT NULL,
  status VARCHAR(20) NOT NULL,
  message TEXT,
  details JSONB,
  started_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  finished_at TIMESTAMP
);

CREATE INDEX IF NOT EXISTS idx_winthor_sync_type ON winthor_sync_logs(type);
CREATE INDEX IF NOT EXISTS idx_winthor_sync_started ON winthor_sync_logs(started_at DESC);
