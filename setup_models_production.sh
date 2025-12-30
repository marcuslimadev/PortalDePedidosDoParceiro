#!/bin/bash

# Script para enviar arquivos modelo para produção via Git
# Execute no servidor após pull

echo "📦 Configurando modelos de importação em produção..."

# Garantir que o diretório existe
mkdir -p storage/app/public

# Executar script de criação dos modelos
php create_excel_template.php

echo "✅ Modelos criados com sucesso!"
echo "📍 Localização: storage/app/public/"
ls -lh storage/app/public/modelo_*

echo ""
echo "🔗 URLs de acesso:"
echo "Excel: https://darkred-wombat-992258.hostingersite.com/products-import/download-modelo-excel"
echo "CSV: https://darkred-wombat-992258.hostingersite.com/products-import/download-modelo-csv"
