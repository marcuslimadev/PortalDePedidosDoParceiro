#!/bin/bash

# Script para enviar arquivos modelo para produção via Git
# Execute no servidor após pull
# Servidor: us-phx-web1005 - usar comando 'php' direto

echo "📦 Configurando modelos de importação em produção..."

# Garantir que o diretório existe
mkdir -p storage/app/public

# Executar migrations pendentes
echo "🔧 Executando migrations..."
php artisan migrate --force

# Executar script de criação dos modelos
echo "📝 Criando modelos..."
php create_excel_template.php

echo "✅ Modelos criados com sucesso!"
echo "📍 Localização: storage/app/public/"
ls -lh storage/app/public/modelo_*

echo ""
echo "🔗 URLs de acesso:"
echo "Excel: https://darkred-wombat-992258.hostingersite.com/products-import/download-modelo-excel"
echo "CSV: https://darkred-wombat-992258.hostingersite.com/products-import/download-modelo-csv"
