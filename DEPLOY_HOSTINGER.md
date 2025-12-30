# 🚀 Guia de Deploy - Hostinger

## ✅ Preparação Local Concluída

- [x] Assets compilados com `npm run build` (pasta `/public/build`)
- [x] Composer otimizado
- [x] Cache de configurações, rotas e views criado
- [x] Arquivos Svelte desnecessários removidos
- [x] Projeto limpo e pronto para produção

---

## 📦 O que Enviar para Hostinger

### Arquivos e Pastas a Enviar:
```
✅ /app
✅ /bootstrap
✅ /config
✅ /database
✅ /public (INCLUINDO /public/build)
✅ /resources
✅ /routes
✅ /storage (estrutura de pastas)
✅ /vendor (se não usar composer na hospedagem)
✅ composer.json
✅ composer.lock
✅ artisan
✅ .htaccess (se não existir, criar)
```

### Arquivos a NÃO Enviar:
```
❌ /node_modules
❌ .env (criar novo na hospedagem)
❌ /tests
❌ /old
❌ *.ps1, *.bat
❌ debug-login.html
❌ test_login.php
```

---

## 🔧 Configuração na Hostinger

### 1. Upload dos Arquivos
- Faça upload de todos os arquivos via FTP/SFTP ou File Manager
- **IMPORTANTE**: O document root deve apontar para `/public`

### 2. Configurar Document Root
No painel da Hostinger:
1. Vá em **Websites** → Seu domínio
2. Clique em **Configurações Avançadas**
3. Altere o **Document Root** para: `/public_html/public` (ou `/domains/seudominio.com/public_html/public`)

### 3. Criar arquivo `.env`
Crie o arquivo `.env` na raiz do projeto com as seguintes configurações:

```env
APP_NAME="Portal de Pedidos"
APP_ENV=production
APP_KEY=base64:GERAR_NOVA_CHAVE_AQUI
APP_DEBUG=false
APP_URL=https://seudominio.com

APP_LOCALE=pt_BR
APP_FALLBACK_LOCALE=pt_BR
APP_FAKER_LOCALE=pt_BR

LOG_CHANNEL=stack
LOG_STACK=single
LOG_LEVEL=error

# Database - MySQL Hostinger
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=nome_do_banco
DB_USERNAME=usuario_mysql
DB_PASSWORD=senha_mysql

SESSION_DRIVER=database
SESSION_LIFETIME=120

CACHE_STORE=database
QUEUE_CONNECTION=database

MAIL_MAILER=smtp
MAIL_HOST=smtp.hostinger.com
MAIL_PORT=587
MAIL_USERNAME=seu@email.com
MAIL_PASSWORD=sua_senha
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=seu@email.com
MAIL_FROM_NAME="${APP_NAME}"
```

### 4. Gerar APP_KEY
Via SSH (se disponível):
```bash
php artisan key:generate
```

OU gere manualmente em: https://generate-random.org/laravel-key-generator
Depois cole no `.env`

### 5. Permissões de Pastas
Execute via SSH ou File Manager:
```bash
chmod -R 755 storage
chmod -R 755 bootstrap/cache
```

### 6. Executar Migrations
```bash
php artisan migrate --force
```

### 7. Criar Cache de Produção
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 8. Criar Usuário Admin (se necessário)
```bash
php artisan db:seed --class=AdminUserSeeder
```

---

## 🔐 Configuração do Banco de Dados MySQL

No painel da Hostinger:
1. Vá em **Bancos de Dados MySQL**
2. Crie um novo banco de dados
3. Anote: nome do banco, usuário e senha
4. Use essas credenciais no `.env`

---

## 📋 Checklist Pós-Deploy

- [ ] Site carrega sem erros
- [ ] CSS e JS estão funcionando
- [ ] Login funciona
- [ ] Dashboard carrega
- [ ] Importação de produtos funciona
- [ ] Listagem de produtos com DataTables funciona
- [ ] Pedidos funcionam
- [ ] Envio de emails funciona (se configurado)

---

## 🐛 Resolução de Problemas

### Erro 500
1. Ative debug temporariamente: `APP_DEBUG=true`
2. Verifique logs em: `/storage/logs/laravel.log`
3. Verifique permissões de `/storage` e `/bootstrap/cache`

### Assets não carregam (CSS/JS)
1. Verifique se a pasta `/public/build` foi enviada
2. Limpe cache: `php artisan cache:clear`
3. Verifique o APP_URL no `.env`

### Erro de APP_KEY
```bash
php artisan key:generate
```

### Erro de Banco de Dados
1. Verifique credenciais no `.env`
2. Teste conexão no painel MySQL da Hostinger
3. Certifique-se que o usuário tem permissões

---

## 🔄 Atualizações Futuras

Para atualizar o site:
1. Compile assets localmente: `npm run build`
2. Envie os arquivos atualizados via FTP
3. Limpe cache:
```bash
php artisan cache:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## 📞 Suporte

- Documentação Hostinger: https://support.hostinger.com
- Laravel Docs: https://laravel.com/docs

---

## ⚡ Performance - Otimizações Já Implementadas

✅ Autoloader otimizado
✅ Assets compilados e minificados
✅ Cache de configurações
✅ Cache de rotas
✅ Cache de views
✅ DataTables para listagens grandes
✅ Lazy loading de imagens (se aplicável)

---

**Data de preparação**: 29/12/2025
**Versão**: 1.0 - Pronto para Deploy
