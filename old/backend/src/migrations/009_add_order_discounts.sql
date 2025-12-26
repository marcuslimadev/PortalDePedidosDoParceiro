-- Migration: Add discount fields to orders table
-- Created: 2025-11-28

-- Add discount-related columns to orders table
ALTER TABLE orders 
  ADD COLUMN IF NOT EXISTS subtotal DECIMAL(10, 2),
  ADD COLUMN IF NOT EXISTS discount DECIMAL(10, 2) DEFAULT 0,
  ADD COLUMN IF NOT EXISTS discount_percentage DECIMAL(5, 2) DEFAULT 0;

-- Update existing orders to have subtotal = total (for backwards compatibility)
UPDATE orders 
SET subtotal = total, 
    discount = 0, 
    discount_percentage = 0 
WHERE subtotal IS NULL;

-- Add index for discount queries
CREATE INDEX IF NOT EXISTS idx_orders_discount ON orders(discount) WHERE discount > 0;

-- Add comment
COMMENT ON COLUMN orders.subtotal IS 'Valor total antes de descontos';
COMMENT ON COLUMN orders.discount IS 'Valor do desconto aplicado';
COMMENT ON COLUMN orders.discount_percentage IS 'Percentual de desconto aplicado';
