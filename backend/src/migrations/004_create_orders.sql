-- Migration: Create orders and order_items tables
-- Created: 2025-11-19

CREATE TYPE order_status AS ENUM ('pendente', 'aprovado', 'cancelado');

CREATE TABLE orders (
  id SERIAL PRIMARY KEY,
  loja_id INTEGER NOT NULL REFERENCES users(id),
  status order_status NOT NULL DEFAULT 'pendente',
  payment_terms VARCHAR(255),
  observations TEXT,
  total NUMERIC(12, 2) NOT NULL DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE order_items (
  id SERIAL PRIMARY KEY,
  order_id INTEGER NOT NULL REFERENCES orders(id) ON DELETE CASCADE,
  product_id INTEGER NOT NULL REFERENCES products(id),
  quantidade INTEGER NOT NULL,
  preco_unitario NUMERIC(12, 2) NOT NULL,
  subtotal NUMERIC(12, 2) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_orders_loja_status ON orders(loja_id, status);
CREATE INDEX idx_order_items_order ON order_items(order_id);
