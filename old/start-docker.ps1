# Script para iniciar o Portal de Pedidos com Docker

Write-Host @"

🐳 PORTAL DE PEDIDOS - DOCKER SETUP
====================================

"@ -ForegroundColor Cyan

# Verificar se Docker está instalado
try {
    $dockerVersion = docker --version
    Write-Host "✅ Docker instalado: $dockerVersion" -ForegroundColor Green
} catch {
    Write-Host "❌ Docker não encontrado! Instale o Docker Desktop:" -ForegroundColor Red
    Write-Host "   https://www.docker.com/products/docker-desktop" -ForegroundColor Yellow
    exit 1
}

# Parar containers antigos se existirem
Write-Host "`n🛑 Parando containers antigos..." -ForegroundColor Yellow
docker-compose down 2>$null

# Construir e iniciar containers
Write-Host "`n🔨 Construindo e iniciando containers..." -ForegroundColor Yellow
docker-compose up -d --build

if ($LASTEXITCODE -eq 0) {
    Write-Host "`n✅ CONTAINERS INICIADOS COM SUCESSO!" -ForegroundColor Green
    Write-Host @"

📦 SERVIÇOS RODANDO:
  🗄️  PostgreSQL: localhost:5432
  🔧 Backend API: http://localhost:3000/api
  🌐 Frontend:    http://localhost:5173

👤 LOGIN PADRÃO:
  Email: admin@portalpedidos.com
  Senha: admin123

📋 COMANDOS ÚTEIS:
  Ver logs:           docker-compose logs -f
  Ver logs backend:   docker-compose logs -f backend
  Ver logs frontend:  docker-compose logs -f frontend
  Parar:              docker-compose down
  Restart:            docker-compose restart

🚀 Aguarde ~30 segundos para o backend rodar migrations e seed...
   Depois acesse: http://localhost:5173

"@ -ForegroundColor Cyan

    # Abrir navegador automaticamente após 30 segundos
    Write-Host "⏳ Aguardando inicialização (30s)..." -ForegroundColor Yellow
    Start-Sleep -Seconds 30
    
    Write-Host "`n🌐 Abrindo navegador..." -ForegroundColor Green
    Start-Process "http://localhost:5173"
    
    Write-Host "`n📊 Monitorando logs do backend (Ctrl+C para sair)..." -ForegroundColor Cyan
    docker-compose logs -f backend

} else {
    Write-Host "`n❌ ERRO ao iniciar containers!" -ForegroundColor Red
    Write-Host "Execute 'docker-compose logs' para ver os erros" -ForegroundColor Yellow
    exit 1
}
