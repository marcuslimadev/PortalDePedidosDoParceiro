# Script para configurar o Virtual Host do projeto PPP
# Execute como Administrador

Write-Host "=== Configuração do Virtual Host PPP ===" -ForegroundColor Cyan

# 1. Adicionar entrada no arquivo hosts
$hostsPath = "C:\Windows\System32\drivers\etc\hosts"
$hostEntry = "127.0.0.1 ppp.local"

Write-Host "`n1. Adicionando entrada no arquivo hosts..." -ForegroundColor Yellow

try {
    $hostsContent = Get-Content $hostsPath -Raw
    
    if ($hostsContent -notmatch "ppp\.local") {
        Add-Content -Path $hostsPath -Value "`n$hostEntry"
        Write-Host "   ✓ Entrada 'ppp.local' adicionada ao arquivo hosts" -ForegroundColor Green
    } else {
        Write-Host "   ✓ Entrada 'ppp.local' já existe no arquivo hosts" -ForegroundColor Green
    }
} catch {
    Write-Host "   ✗ Erro ao modificar o arquivo hosts. Execute como Administrador!" -ForegroundColor Red
    Write-Host "   Adicione manualmente: $hostEntry" -ForegroundColor Yellow
}

# 2. Adicionar Virtual Host no Apache
$vhostPath = "C:\xampp\apache\conf\extra\httpd-vhosts.conf"
$vhostConfig = @"

# Virtual Host para PPP
<VirtualHost *:80>
    DocumentRoot "C:/xampp/htdocs/ppp/public"
    ServerName ppp.local
    ServerAlias www.ppp.local
    
    <Directory "C:/xampp/htdocs/ppp/public">
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog "logs/ppp-error.log"
    CustomLog "logs/ppp-access.log" common
</VirtualHost>
"@

Write-Host "`n2. Adicionando Virtual Host no Apache..." -ForegroundColor Yellow

try {
    if (Test-Path $vhostPath) {
        $vhostContent = Get-Content $vhostPath -Raw
        
        if ($vhostContent -notmatch "ServerName ppp\.local") {
            Add-Content -Path $vhostPath -Value $vhostConfig
            Write-Host "   ✓ Virtual Host adicionado ao httpd-vhosts.conf" -ForegroundColor Green
        } else {
            Write-Host "   ✓ Virtual Host já existe no httpd-vhosts.conf" -ForegroundColor Green
        }
    } else {
        Write-Host "   ✗ Arquivo httpd-vhosts.conf não encontrado!" -ForegroundColor Red
        Write-Host "   Verifique se o XAMPP está instalado em C:\xampp" -ForegroundColor Yellow
    }
} catch {
    Write-Host "   ✗ Erro ao modificar httpd-vhosts.conf. Execute como Administrador!" -ForegroundColor Red
}

# 3. Instruções para reiniciar o Apache
Write-Host "`n3. Próximos passos:" -ForegroundColor Yellow
Write-Host "   • Abra o XAMPP Control Panel" -ForegroundColor White
Write-Host "   • Clique em 'Stop' no Apache" -ForegroundColor White
Write-Host "   • Clique em 'Start' no Apache" -ForegroundColor White
Write-Host "   • Acesse http://ppp.local no navegador" -ForegroundColor White

Write-Host "`n=== Configuração concluída! ===" -ForegroundColor Cyan
Write-Host "Lembre-se de reiniciar o Apache para aplicar as mudanças.`n" -ForegroundColor Green
