# ✅ CHECKLIST DE DEPLOY - HOSTINGER

## 📋 PRÉ-DEPLOY (Local)

- [x] Assets compilados: `npm run build` executado
- [x] Composer otimizado: `composer install --optimize-autoloader`
- [x] Caches criados: config, route, view
- [x] Arquivos Svelte removidos
- [x] .env.example atualizado com valores de produção
- [x] Documentação de deploy criada (DEPLOY_HOSTINGER.md)
- [x] Script de deploy criado (deploy.sh)

## 📤 UPLOAD (FTP/SFTP)

- [ ] Todos os arquivos enviados (exceto node_modules, .env, tests, old)
- [ ] Pasta `/public/build` enviada com assets compilados
- [ ] Pasta `/vendor` enviada (ou executar composer no servidor)
- [ ] Pasta `/storage` com estrutura de diretórios

## ⚙️ CONFIGURAÇÃO SERVIDOR

### Document Root
- [ ] Document root apontando para `/public`
- [ ] Versão PHP: 8.1 ou superior
- [ ] Extensões PHP ativadas: PDO, MySQL, OpenSSL, Mbstring, Tokenizer, XML, Ctype, JSON, BCMath, Fileinfo

### Banco de Dados
- [ ] Banco MySQL criado
- [ ] Usuário MySQL criado com permissões
- [ ] Credenciais anotadas

### Arquivo .env
- [ ] Arquivo `.env` criado na raiz
- [ ] APP_NAME configurado
- [ ] APP_ENV=production
- [ ] APP_DEBUG=false
- [ ] APP_URL com domínio correto (https://)
- [ ] APP_KEY gerada
- [ ] Credenciais MySQL configuradas (DB_HOST, DB_DATABASE, DB_USERNAME, DB_PASSWORD)
- [ ] MAIL configurado (se necessário)

### Permissões
- [ ] `chmod -R 755 storage`
- [ ] `chmod -R 755 bootstrap/cache`

## 🚀 DEPLOY

### Via SSH (Recomendado)
```bash
# Fazer upload do script deploy.sh
chmod +x deploy.sh
./deploy.sh
```

### Manual
```bash
# 1. Gerar APP_KEY
php artisan key:generate --force

# 2. Executar migrations
php artisan migrate --force

# 3. Criar caches
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 4. Link de storage
php artisan storage:link
```

## 🧪 TESTES PÓS-DEPLOY

### Funcionalidades Básicas
- [ ] Site carrega sem erros
- [ ] CSS e JavaScript funcionando
- [ ] Imagens e assets carregando

### Autenticação
- [ ] Página de login acessível
- [ ] Login funciona
- [ ] Logout funciona
- [ ] Redirecionamento após login OK

### Dashboard
- [ ] Dashboard carrega
- [ ] Estatísticas aparecem
- [ ] Menu de navegação funciona

### Produtos
- [ ] Listagem de produtos funciona
- [ ] DataTables funciona (busca, ordenação, paginação)
- [ ] Detalhes do produto carregam
- [ ] Importação de Excel/CSV funciona

### Pedidos
- [ ] Criar novo pedido funciona
- [ ] Listagem de pedidos funciona
- [ ] Detalhes do pedido carregam
- [ ] Status de pedido atualiza

### Email (se configurado)
- [ ] Envio de emails funciona
- [ ] Notificações chegam

## 🔧 TROUBLESHOOTING

### Erro 500
1. Ativar debug: `APP_DEBUG=true`
2. Verificar: `storage/logs/laravel.log`
3. Verificar permissões de storage e bootstrap/cache

### Assets não carregam
1. Verificar se `/public/build` foi enviado
2. Verificar APP_URL no .env
3. Limpar cache: `php artisan cache:clear`

### Erro de banco
1. Testar conexão MySQL no painel
2. Verificar credenciais no .env
3. Verificar se migrations rodaram: `php artisan migrate:status`

### Erro de APP_KEY
```bash
php artisan key:generate --force
php artisan config:cache
```

## 📊 MONITORAMENTO

- [ ] Configurar backup automático do banco
- [ ] Configurar SSL/HTTPS (Hostinger oferece Let's Encrypt grátis)
- [ ] Configurar cron job para queue (se usar)
- [ ] Monitorar logs: `tail -f storage/logs/laravel.log`

## 🎯 OTIMIZAÇÕES ADICIONAIS

- [ ] Ativar OPcache no PHP
- [ ] Configurar Redis/Memcached (se disponível)
- [ ] Ativar compressão Gzip
- [ ] Configurar CDN (se necessário)

## 📝 NOTAS

- **Build Assets**: Sempre compile localmente (`npm run build`) antes de fazer upload
- **Vendor**: Se o servidor tem Composer, não precisa enviar `/vendor`
- **Cache**: Sempre limpe e recrie caches após atualizações
- **Backup**: Faça backup do banco antes de executar migrations em produção

---

**Data**: 29/12/2025  
**Status**: ✅ PRONTO PARA DEPLOY  
**Versão**: 1.0
