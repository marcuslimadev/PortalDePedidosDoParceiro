-- SQL para executar no banco de produção via phpMyAdmin
-- https://darkred-wombat-992258.hostingersite.com:2083/cpsess0000000000/3rdparty/phpMyAdmin/

-- Alterar coluna preco para aceitar NULL
ALTER TABLE products MODIFY preco DECIMAL(12,2) NULL;

-- Verificar produtos sem preço
SELECT COUNT(*) as total_sem_preco FROM products WHERE preco IS NULL OR preco <= 0.01;

-- (Opcional) Atualizar produtos com R$ 0,01 para NULL
-- UPDATE products SET preco = NULL WHERE preco = 0.01;
