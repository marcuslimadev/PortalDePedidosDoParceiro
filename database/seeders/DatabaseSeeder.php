<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Criar usuários padrão
        $admin = User::create([
            'name' => 'Administrador do Sistema',
            'email' => 'admin@portalpedidos.com',
            'password' => bcrypt('admin123'),
            'role' => 'admin',
            'ativo' => true,
        ]);

        $operador = User::create([
            'name' => 'Operador do Sistema',
            'email' => 'operador@portalpedidos.com',
            'password' => bcrypt('operador123'),
            'role' => 'operador',
            'ativo' => true,
        ]);

        // Criar 3 lojas de exemplo
        $loja1 = User::create([
            'name' => 'Loja Exemplo 1',
            'email' => 'loja1@cliente.com',
            'password' => bcrypt('cliente123'),
            'role' => 'loja',
            'ativo' => true,
            'cnpj' => '12.345.678/0001-90',
            'credit_limit' => 50000.00,
            'credit_used' => 15000.00,
            'payment_terms' => '30 dias',
            'cliente_status' => 'ativo',
        ]);

        $loja2 = User::create([
            'name' => 'Loja Exemplo 2',
            'email' => 'loja2@cliente.com',
            'password' => bcrypt('cliente123'),
            'role' => 'loja',
            'ativo' => true,
            'cnpj' => '98.765.432/0001-10',
            'credit_limit' => 75000.00,
            'credit_used' => 30000.00,
            'payment_terms' => 'Antecipado',
            'cliente_status' => 'ativo',
        ]);

        $loja3 = User::create([
            'name' => 'Loja Exemplo 3',
            'email' => 'loja3@cliente.com',
            'password' => bcrypt('cliente123'),
            'role' => 'loja',
            'ativo' => true,
            'cnpj' => '11.222.333/0001-44',
            'credit_limit' => 100000.00,
            'credit_used' => 45000.00,
            'payment_terms' => '60 dias',
            'cliente_status' => 'ativo',
        ]);

        // Criar produtos de exemplo
        $produtos = [
            ['codigo' => 'PROD001', 'descricao' => 'Produto A - Categoria Premium', 'preco' => 150.00, 'unidade' => 'UN', 'tributacao' => 'T01', 'estoque' => 100, 'categoria' => 'Premium'],
            ['codigo' => 'PROD002', 'descricao' => 'Produto B - Categoria Standard', 'preco' => 85.50, 'unidade' => 'UN', 'tributacao' => 'T01', 'estoque' => 250, 'categoria' => 'Standard'],
            ['codigo' => 'PROD003', 'descricao' => 'Produto C - Categoria Economy', 'preco' => 45.00, 'unidade' => 'UN', 'tributacao' => 'T01', 'estoque' => 500, 'categoria' => 'Economy'],
            ['codigo' => 'PROD004', 'descricao' => 'Produto D - Categoria Premium', 'preco' => 220.00, 'unidade' => 'CX', 'tributacao' => 'T02', 'estoque' => 75, 'categoria' => 'Premium'],
            ['codigo' => 'PROD005', 'descricao' => 'Produto E - Categoria Standard', 'preco' => 95.00, 'unidade' => 'UN', 'tributacao' => 'T01', 'estoque' => 180, 'categoria' => 'Standard'],
        ];

        foreach ($produtos as $prod) {
            \App\Models\Product::create($prod);
        }

        echo "✓ Seeder completo: 3 usuários (admin, operador, 3 lojas) + 5 produtos criados!\n";
    }
}
