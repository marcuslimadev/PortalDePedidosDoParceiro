-- Migration: Add winthor_data JSONB column to products
-- Created: 2025-11-27

ALTER TABLE products
  ADD COLUMN IF NOT EXISTS winthor_data JSONB DEFAULT '{}'::jsonb;

CREATE INDEX IF NOT EXISTS idx_products_winthor_data ON products USING GIN (winthor_data);
