-- Migration: Create products table
-- Created: 2025-11-20

CREATE TABLE IF NOT EXISTS products (
  id SERIAL PRIMARY KEY,
  codigo VARCHAR(50) NOT NULL UNIQUE,
  descricao TEXT NOT NULL,
  preco NUMERIC(12,2) NOT NULL CHECK (preco >= 0),
  unidade VARCHAR(10) NOT NULL,
  tributacao VARCHAR(50) NOT NULL,
  estoque INTEGER NOT NULL DEFAULT 0,
  categoria VARCHAR(100),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX IF NOT EXISTS idx_products_descricao ON products USING gin (to_tsvector('portuguese', descricao));
CREATE INDEX IF NOT EXISTS idx_products_categoria ON products (categoria);

-- Trigger to update updated_at timestamp
CREATE OR REPLACE FUNCTION update_products_updated_at()
RETURNS TRIGGER AS $$
BEGIN
  NEW.updated_at = NOW();
  RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER trg_products_updated_at
BEFORE UPDATE ON products
FOR EACH ROW
EXECUTE FUNCTION update_products_updated_at();
