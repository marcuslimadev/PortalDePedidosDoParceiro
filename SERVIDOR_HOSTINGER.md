# Configuração do Servidor Hostinger

## Informações do Servidor

- **Servidor:** us-phx-web1005
- **Usuário:** u815655858
- **Path SSH:** /home/u815655858/domains/darkred-wombat-992258.hostingersite.com/public_html
- **PHP 8.3:** /opt/alt/php83/usr/bin/php
- **PHP padrão CLI:** /usr/bin/php (versão 8.0.30 - NÃO USAR)

## Comandos para Deploy

### 1. Atualizar código
```bash
cd /home/u815655858/domains/darkred-wombat-992258.hostingersite.com/public_html
git pull
```

### 2. Executar migrations
```bash
/opt/alt/php83/usr/bin/php artisan migrate
```

### 3. Limpar cache
```bash
/opt/alt/php83/usr/bin/php artisan config:clear
/opt/alt/php83/usr/bin/php artisan cache:clear
/opt/alt/php83/usr/bin/php artisan view:clear
```

### 4. Verificar versão do PHP
```bash
php -v
```

## PATH do Sistema
```
/usr/share/Modules/bin
/usr/local/bin
/usr/bin
/usr/local/sbin
/usr/sbin
/opt/golang/1.22.0/bin
/opt/go/bin
```

## Notas
- O PHP 8.3 está configurado via .htaccess (AddHandler application/x-httpd-php83)
- Para CLI, usar simplesmente `php` (sem versão específica)
- O servidor usa CloudLinux 8
