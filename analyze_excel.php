<?php

require __DIR__ . '/vendor/autoload.php';

use Spatie\SimpleExcel\SimpleExcelReader;

$filePath = __DIR__ . '/old/CADASTRO 500 ITENS.xlsx';

if (!file_exists($filePath)) {
    die("Arquivo não encontrado: $filePath\n");
}

echo "Analisando arquivo: $filePath\n";
echo str_repeat("=", 80) . "\n\n";

$reader = SimpleExcelReader::create($filePath);
$rows = $reader->getRows();

// Pegar primeira linha para ver os cabeçalhos
$firstRow = $rows->first();

if (!$firstRow) {
    die("Arquivo vazio!\n");
}

echo "COLUNAS ENCONTRADAS NO ARQUIVO:\n";
echo str_repeat("-", 80) . "\n";

$columnIndex = 1;
foreach ($firstRow as $key => $value) {
    echo sprintf("%2d. %-30s => Valor exemplo: %s\n", $columnIndex++, $key, substr($value ?? 'NULL', 0, 40));
}

echo "\n" . str_repeat("=", 80) . "\n\n";

// Pegar algumas linhas de exemplo
echo "PRIMEIRAS 3 LINHAS DE DADOS:\n";
echo str_repeat("-", 80) . "\n";

$count = 0;
foreach ($rows as $index => $row) {
    if ($count >= 3) break;
    
    echo "\nLinha " . ($index + 2) . ":\n";
    foreach ($row as $key => $value) {
        if ($value !== null && $value !== '') {
            echo "  $key: $value\n";
        }
    }
    $count++;
}

echo "\n" . str_repeat("=", 80) . "\n\n";

// Análise de mapeamento para o sistema
echo "MAPEAMENTO PARA O SISTEMA:\n";
echo str_repeat("-", 80) . "\n";

$mappings = [
    'codigo' => ['codigo', 'cod_prod', 'codprod', 'codigo_produto'],
    'descricao' => ['descricao', 'descrição', 'produto', 'descricao_produto'],
    'preco' => ['preco', 'preço', 'valor', 'preco_venda', 'pvenda'],
    'unidade' => ['unidade', 'un', 'unidadedemedida'],
    'tributacao' => ['tributacao', 'tributação'],
    'estoque' => ['estoque', 'qtd', 'quantidade'],
    'categoria' => ['categoria', 'grupo', 'linha'],
    'codprod_winthor' => ['codprod_winthor', 'winthor', 'cod_winthor'],
    'embalagem' => ['embalagem', 'pack', 'emb'],
    'marca' => ['marca', 'fabricante'],
    'peso_liquido' => ['peso_liquido', 'peso_liq', 'pesoliq'],
    'peso_bruto' => ['peso_bruto', 'peso_brt', 'pesobrt'],
];

$availableColumns = array_keys($firstRow);
$normalizedColumns = array_map('strtolower', array_map(function($col) {
    return str_replace([' ', '-', '_'], '', $col);
}, $availableColumns));

foreach ($mappings as $field => $possibleNames) {
    $found = false;
    foreach ($possibleNames as $name) {
        $normalized = str_replace([' ', '-', '_'], '', strtolower($name));
        $index = array_search($normalized, $normalizedColumns);
        
        if ($index !== false) {
            echo "✓ $field => '{$availableColumns[$index]}'\n";
            $found = true;
            break;
        }
    }
    
    if (!$found) {
        echo "✗ $field => NÃO ENCONTRADO\n";
    }
}

echo "\n" . str_repeat("=", 80) . "\n";
echo "\nTotal de linhas no arquivo: " . $rows->count() . "\n";
