<?php

/**
 * Script para limpar produtos importados com preço incorreto (R$ 0,01)
 * Execute: php cleanup_invalid_products.php
 */

require __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\DB;

// Bootstrap Laravel
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=======================================================\n";
echo "  LIMPEZA DE PRODUTOS COM PREÇOS INVÁLIDOS\n";
echo "=======================================================\n\n";

// Contar produtos com preço R$ 0,01
$count001 = DB::table('products')->where('preco', 0.01)->count();
// Contar produtos sem preço (NULL)
$countNull = DB::table('products')->whereNull('preco')->count();
// Contar produtos com preço zero
$countZero = DB::table('products')->where('preco', 0)->count();

echo "Produtos encontrados:\n";
echo "  - Com preço R\$ 0,01: $count001\n";
echo "  - Com preço NULL: $countNull\n";
echo "  - Com preço R\$ 0,00: $countZero\n\n";

if ($count001 > 0) {
    echo "Deseja deletar os $count001 produtos com preço R\$ 0,01? (s/n): ";
    $handle = fopen("php://stdin", "r");
    $line = fgets($handle);
    fclose($handle);
    
    if (trim(strtolower($line)) === 's') {
        $deleted = DB::table('products')->where('preco', 0.01)->delete();
        echo "✓ $deleted produtos deletados com sucesso!\n\n";
    } else {
        echo "✗ Operação cancelada.\n\n";
    }
} else {
    echo "✓ Nenhum produto com preço R\$ 0,01 encontrado.\n\n";
}

echo "=======================================================\n";
echo "Status atual do banco:\n";
$total = DB::table('products')->count();
$withPrice = DB::table('products')->whereNotNull('preco')->where('preco', '>', 0)->count();
$noPrice = DB::table('products')->whereNull('preco')->orWhere('preco', '<=', 0)->count();

echo "  Total de produtos: $total\n";
echo "  Com preço válido: $withPrice\n";
echo "  Sem preço: $noPrice\n";
echo "=======================================================\n";
