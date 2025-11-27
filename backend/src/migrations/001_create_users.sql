-- Migration: Create users and roles tables
-- Created: 2025-11-19

-- Create enum for user roles (idempotent)
DO $$
BEGIN
  IF NOT EXISTS (SELECT 1 FROM pg_type WHERE typname = 'user_role') THEN
    CREATE TYPE user_role AS ENUM ('admin', 'operador', 'loja');
  END IF;
END$$;

-- Create users table
CREATE TABLE IF NOT EXISTS users (
  id SERIAL PRIMARY KEY,
  email VARCHAR(255) UNIQUE NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  nome VARCHAR(255) NOT NULL,
  role user_role NOT NULL DEFAULT 'loja',
  ativo BOOLEAN DEFAULT true,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create index on email for faster lookups
CREATE INDEX IF NOT EXISTS idx_users_email ON users(email);
CREATE INDEX IF NOT EXISTS idx_users_role ON users(role);

-- Insert default admin user (password: admin123)
-- Hash generated with bcrypt rounds=10
INSERT INTO users (email, password_hash, nome, role) VALUES 
('admin@portalpedidos.com', '$2b$10$rZ8kLhqJ5K3XxN0Y9w8GceF.QW0.YqxVZ0kPzJ9vHqB3qT4FJ8YGy', 'Administrador do Sistema', 'admin')
ON CONFLICT (email) DO NOTHING;
