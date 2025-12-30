<?php

use Spatie\SimpleExcel\SimpleExcelWriter;

require __DIR__ . '/vendor/autoload.php';

// Criar arquivo Excel
$writer = SimpleExcelWriter::create(__DIR__ . '/storage/app/public/modelo_importacao_produtos.xlsx');

// Adicionar cabeçalhos e exemplos
$writer->addRows([
    [
        'codigo' => 'PROD001',
        'descricao' => 'Produto Exemplo 1',
        'preco' => 150.00,
        'unidade' => 'UN',
        'tributacao' => 'T01',
        'estoque' => 100,
        'categoria' => 'Premium',
        'marca' => 'Marca A',
        'embalagem' => '1UNX490ML',
        'peso_liquido' => 0.49,
        'peso_bruto' => 0.494,
        'nbm' => '22021000',
        'ean_produto' => '7891234567890',
        'ean_embalagem' => '17891234567897',
    ],
    [
        'codigo' => 'PROD002',
        'descricao' => 'Produto Exemplo 2',
        'preco' => 85.50,
        'unidade' => 'UN',
        'tributacao' => 'T01',
        'estoque' => 250,
        'categoria' => 'Standard',
        'marca' => 'Marca B',
        'embalagem' => '12X490ML',
        'peso_liquido' => 0.49,
        'peso_bruto' => 0.494,
        'nbm' => '22021000',
        'ean_produto' => '7899876543210',
        'ean_embalagem' => '17899876543217',
    ],
    [
        'codigo' => 'PROD003',
        'descricao' => 'Produto Exemplo 3',
        'preco' => 45.00,
        'unidade' => 'CX',
        'tributacao' => 'T02',
        'estoque' => 500,
        'categoria' => 'Economy',
        'marca' => 'Marca C',
        'embalagem' => '24X250ML',
        'peso_liquido' => 0.25,
        'peso_bruto' => 0.260,
        'nbm' => '22030000',
        'ean_produto' => '7895551112222',
        'ean_embalagem' => '17895551112229',
    ],
]);

echo "✓ Arquivo Excel criado: storage/app/public/modelo_importacao_produtos.xlsx\n";
echo "✓ Arquivo CSV criado: storage/app/public/modelo_importacao_produtos.csv\n";
