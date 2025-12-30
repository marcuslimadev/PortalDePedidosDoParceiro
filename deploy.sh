#!/bin/bash
# Script de Deploy Automatizado para Hostinger
# Execute este script VIA SSH na hospedagem após fazer upload dos arquivos

echo "🚀 Iniciando processo de deploy..."

# Cores para output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# 1. Verificar se .env existe
if [ ! -f .env ]; then
    echo -e "${RED}❌ Arquivo .env não encontrado!${NC}"
    echo "Por favor, copie .env.example para .env e configure:"
    echo "cp .env.example .env"
    echo "nano .env"
    exit 1
fi

# 2. Verificar permissões
echo -e "${YELLOW}📁 Configurando permissões...${NC}"
chmod -R 755 storage
chmod -R 755 bootstrap/cache
echo -e "${GREEN}✅ Permissões configuradas${NC}"

# 3. Composer (se disponível)
if command -v composer &> /dev/null; then
    echo -e "${YELLOW}📦 Instalando dependências do Composer...${NC}"
    composer install --no-dev --optimize-autoloader
    echo -e "${GREEN}✅ Composer instalado${NC}"
else
    echo -e "${YELLOW}⚠️  Composer não encontrado - ignorando (vendor já deve estar no upload)${NC}"
fi

# 4. Gerar APP_KEY se vazio
if ! grep -q "APP_KEY=base64:" .env; then
    echo -e "${YELLOW}🔑 Gerando APP_KEY...${NC}"
    php artisan key:generate --force
    echo -e "${GREEN}✅ APP_KEY gerada${NC}"
else
    echo -e "${GREEN}✅ APP_KEY já configurada${NC}"
fi

# 5. Executar migrations
echo -e "${YELLOW}🗄️  Executando migrations...${NC}"
php artisan migrate --force
echo -e "${GREEN}✅ Migrations executadas${NC}"

# 6. Limpar caches antigos
echo -e "${YELLOW}🧹 Limpando caches...${NC}"
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
echo -e "${GREEN}✅ Caches limpos${NC}"

# 7. Criar caches de produção
echo -e "${YELLOW}⚡ Criando caches de produção...${NC}"
php artisan config:cache
php artisan route:cache
php artisan view:cache
echo -e "${GREEN}✅ Caches criados${NC}"

# 8. Criar link de storage (se necessário)
if [ ! -L public/storage ]; then
    echo -e "${YELLOW}🔗 Criando link simbólico do storage...${NC}"
    php artisan storage:link
    echo -e "${GREEN}✅ Link criado${NC}"
fi

# 9. Verificar permissões finais
echo -e "${YELLOW}🔒 Verificando permissões finais...${NC}"
chmod -R 755 storage bootstrap/cache
echo -e "${GREEN}✅ Permissões OK${NC}"

echo ""
echo -e "${GREEN}✨ Deploy concluído com sucesso!${NC}"
echo ""
echo "📋 Próximos passos:"
echo "1. Acesse seu site e verifique se está funcionando"
echo "2. Teste o login"
echo "3. Teste a importação de produtos"
echo "4. Configure o cron job para filas (se necessário):"
echo "   * * * * * cd /caminho/do/projeto && php artisan schedule:run >> /dev/null 2>&1"
echo ""
echo "🔍 Em caso de erros, verifique os logs em:"
echo "   storage/logs/laravel.log"
echo ""
