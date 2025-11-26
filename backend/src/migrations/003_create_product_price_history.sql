-- Migration: Create product price history table
-- Created: 2025-11-21

CREATE TABLE product_price_history (
  id SERIAL PRIMARY KEY,
  product_id INTEGER NOT NULL REFERENCES products(id) ON DELETE CASCADE,
  preco_anterior NUMERIC(12,2) NOT NULL,
  preco_novo NUMERIC(12,2) NOT NULL,
  changed_by INTEGER REFERENCES users(id),
  changed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_price_history_product ON product_price_history (product_id);
