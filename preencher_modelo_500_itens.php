<?php

use Spatie\SimpleExcel\SimpleExcelReader;
use Spatie\SimpleExcel\SimpleExcelWriter;

require __DIR__ . '/vendor/autoload.php';

echo "Lendo arquivo de 500 itens...\n";

$sourceFile = __DIR__ . '/old/CADASTRO 500 ITENS.xlsx';
$targetFile = __DIR__ . '/storage/app/public/modelo_importacao_produtos_500_itens.xlsx';

if (!file_exists($sourceFile)) {
    die("Erro: Arquivo {$sourceFile} não encontrado!\n");
}

$rows = SimpleExcelReader::create($sourceFile)->getRows();
$writer = SimpleExcelWriter::create($targetFile);

$count = 0;
$produtos = [];

foreach ($rows as $row) {
    // Pega valores do arquivo Winthor
    $codprod = $row['CODPROD'] ?? '';
    $descricao = $row['DESCRICAO'] ?? '';
    $categoria = $row['J11_CATEGORIA'] ?? $row['J8_DESCRICAO'] ?? '';
    $marca = $row['J9_MARCA'] ?? '';
    $pesoLiq = $row['PESOLIQ'] ?? 0;
    $pesoBruto = $row['PESOBRUTO'] ?? 0;
    $embalagem = $row['EMBALAGEMMASTER'] ?? '';
    $unidade = $row['UNIDADEDEMEDIDA'] ?? 'UN';
    
    // Pula se não tiver código ou descrição
    if (empty($codprod) || empty($descricao)) {
        continue;
    }
    
    // Gera código único no formato PROD + código do produto
    $codigo = 'PROD' . str_pad($codprod, 6, '0', STR_PAD_LEFT);
    
    // Gera preço aleatório entre R$ 5,00 e R$ 500,00
    $preco = round(rand(500, 50000) / 100, 2);
    
    // Gera EAN-13 fictício (7891 + 9 dígitos aleatórios)
    $eanProduto = '7891' . str_pad(rand(0, 999999999), 9, '0', STR_PAD_LEFT);
    
    // Gera EAN-13 da embalagem (mesma lógica, prefixo 17891)
    $eanEmbalagem = '17891' . str_pad(rand(0, 99999999), 8, '0', STR_PAD_LEFT);
    
    // NBM genérico para bebidas (22021000 para refrigerantes)
    $nbm = '22021000';
    
    // Determina tributação baseada no preço
    $tributacao = $preco > 100 ? 'T01' : 'T02';
    
    // Estoque aleatório entre 0 e 1000
    $estoque = rand(0, 1000);
    
    $produtos[] = [
        'codigo' => $codigo,
        'descricao' => trim($descricao),
        'preco' => $preco,
        'unidade' => $unidade,
        'tributacao' => $tributacao,
        'estoque' => $estoque,
        'categoria' => trim($categoria),
        'marca' => trim($marca),
        'embalagem' => trim($embalagem),
        'peso_liquido' => (float) $pesoLiq,
        'peso_bruto' => (float) $pesoBruto,
        'nbm' => $nbm,
        'ean_produto' => $eanProduto,
        'ean_embalagem' => $eanEmbalagem,
    ];
    
    $count++;
    
    if ($count % 50 == 0) {
        echo "Processados {$count} produtos...\n";
    }
}

echo "Escrevendo arquivo Excel com {$count} produtos...\n";
$writer->addRows($produtos);

echo "✓ Arquivo criado: {$targetFile}\n";
echo "✓ Total de produtos: {$count}\n";
echo "✓ Tamanho: " . number_format(filesize($targetFile)) . " bytes\n";
