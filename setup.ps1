# Setup Automático - Portal de Pedidos Laravel
# Execute este script para configurar o projeto do zero

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "  Portal de Pedidos - Setup Laravel" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

$projectPath = "C:\xampp\htdocs\PortalDePedidosDoParceiro\laravel"
Set-Location $projectPath

# 1. Verificar PHP
Write-Host "[1/8] Verificando PHP..." -ForegroundColor Yellow
$phpVersion = & C:\xampp\php\php.exe -v
if ($?) {
    Write-Host "✓ PHP encontrado" -ForegroundColor Green
} else {
    Write-Host "✗ PHP não encontrado. Instale o XAMPP." -ForegroundColor Red
    exit 1
}

# 2. Verificar MySQL
Write-Host "[2/8] Verificando MySQL..." -ForegroundColor Yellow
$mysqlRunning = Get-Process -Name "mysqld" -ErrorAction SilentlyContinue
if ($mysqlRunning) {
    Write-Host "✓ MySQL rodando" -ForegroundColor Green
} else {
    Write-Host "⚠ MySQL não está rodando. Inicie pelo XAMPP Control Panel." -ForegroundColor Yellow
}

# 3. Instalar dependências Composer
Write-Host "[3/8] Instalando dependências PHP..." -ForegroundColor Yellow
& C:\xampp\php\php.exe C:\xampp\php\composer.phar install --no-interaction
if ($?) {
    Write-Host "✓ Dependências PHP instaladas" -ForegroundColor Green
} else {
    Write-Host "✗ Erro ao instalar dependências PHP" -ForegroundColor Red
    exit 1
}

# 4. Copiar .env
Write-Host "[4/8] Configurando .env..." -ForegroundColor Yellow
if (-not (Test-Path ".env")) {
    Copy-Item ".env.example" ".env"
    Write-Host "✓ Arquivo .env criado" -ForegroundColor Green
} else {
    Write-Host "✓ Arquivo .env já existe" -ForegroundColor Green
}

# 5. Gerar APP_KEY
Write-Host "[5/8] Gerando APP_KEY..." -ForegroundColor Yellow
& C:\xampp\php\php.exe artisan key:generate --force
Write-Host "✓ APP_KEY gerada" -ForegroundColor Green

# 6. Criar banco de dados
Write-Host "[6/8] Criando banco de dados..." -ForegroundColor Yellow
& C:\xampp\mysql\bin\mysql.exe -u root -e "CREATE DATABASE IF NOT EXISTS portal_pedidos CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
Write-Host "✓ Banco de dados criado" -ForegroundColor Green

# 7. Rodar migrations
Write-Host "[7/8] Executando migrations..." -ForegroundColor Yellow
& C:\xampp\php\php.exe artisan migrate --force
if ($?) {
    Write-Host "✓ Migrations executadas" -ForegroundColor Green
} else {
    Write-Host "✗ Erro nas migrations" -ForegroundColor Red
    exit 1
}

# 8. Rodar seeders
Write-Host "[8/8] Populando banco de dados..." -ForegroundColor Yellow
& C:\xampp\php\php.exe artisan db:seed --force
if ($?) {
    Write-Host "✓ Dados de teste criados" -ForegroundColor Green
} else {
    Write-Host "✗ Erro ao popular banco" -ForegroundColor Red
}

Write-Host ""
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "  Setup Concluído!" -ForegroundColor Green
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "Próximos passos:" -ForegroundColor Yellow
Write-Host "1. Instale dependências Node: npm install --force" -ForegroundColor White
Write-Host "2. Compile assets: npm run build" -ForegroundColor White
Write-Host "3. Inicie servidor: php artisan serve --port=8000" -ForegroundColor White
Write-Host "4. Acesse: http://localhost:8000" -ForegroundColor White
Write-Host ""
Write-Host "Usuários de teste:" -ForegroundColor Yellow
Write-Host "  Admin:    admin@portalpedidos.com / admin123" -ForegroundColor Cyan
Write-Host "  Operador: operador@portalpedidos.com / operador123" -ForegroundColor Cyan
Write-Host "  Loja:     loja1@cliente.com / cliente123" -ForegroundColor Cyan
Write-Host ""
