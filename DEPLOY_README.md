# 🚀 Portal de Pedidos - Sistema Pronto para Deploy

[![Laravel](https://img.shields.io/badge/Laravel-11.x-red.svg)](https://laravel.com)
[![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3-purple.svg)](https://getbootstrap.com)
[![Status](https://img.shields.io/badge/Status-Pronto%20para%20Deploy-success.svg)]()

Sistema completo de gestão de pedidos com catálogo de produtos, importação de Excel/CSV e controle de pedidos.

## 📋 Índice

- [Características](#características)
- [Tecnologias](#tecnologias)
- [Deploy Rápido](#deploy-rápido)
- [Documentação](#documentação)
- [Funcionalidades](#funcionalidades)

## ✨ Características

- ✅ **100% Compilado e Otimizado** - Assets prontos para produção
- ✅ **Cache Ativado** - Configurações, rotas e views em cache
- ✅ **Autoloader Otimizado** - Performance maximizada
- ✅ **DataTables** - Listagens rápidas e interativas
- ✅ **Responsive** - Interface adaptável para mobile
- ✅ **Bootstrap 5** - Design moderno e profissional

## 🛠️ Tecnologias

### Backend
- Laravel 11.x
- PHP 8.1+
- MySQL/MariaDB

### Frontend
- Bootstrap 5.3
- Alpine.js
- HTMX
- DataTables
- Chart.js

### Compilação
- Vite
- TailwindCSS
- PostCSS

## 🚀 Deploy Rápido

### 1️⃣ Preparar Local (WINDOWS)
```powershell
.\prepare-deploy.ps1
```

### 2️⃣ Upload para Hostinger
Via FTP/SFTP, enviar **TODOS** os arquivos **EXCETO**:
- `/node_modules`
- `/.env`
- `/tests`
- `/old`
- `*.ps1`

### 3️⃣ Configurar no Servidor

#### A) Via SSH (Recomendado)
```bash
chmod +x deploy.sh
./deploy.sh
```

#### B) Manual
```bash
# Criar .env (copiar de .env.example e configurar)
nano .env

# Gerar chave
php artisan key:generate --force

# Permissões
chmod -R 755 storage bootstrap/cache

# Migrations
php artisan migrate --force

# Cache
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 4️⃣ Configurar Document Root
No painel Hostinger:
- **Document Root**: `/public_html/public` (ou `/domains/seudominio.com/public_html/public`)

### 5️⃣ Testar!
Acesse seu domínio e faça login.

## 📚 Documentação

| Arquivo | Descrição |
|---------|-----------|
| [`DEPLOY_HOSTINGER.md`](DEPLOY_HOSTINGER.md) | Guia completo de deploy |
| [`CHECKLIST_DEPLOY.md`](CHECKLIST_DEPLOY.md) | Checklist passo a passo |
| [`prepare-deploy.ps1`](prepare-deploy.ps1) | Script de preparação (Windows) |
| [`deploy.sh`](deploy.sh) | Script de deploy (Servidor Linux) |

## 🎯 Funcionalidades

### ✅ Autenticação
- Login/Logout
- Controle de acesso (Admin, Representante, Cliente)
- Sistema de permissões (Spatie)

### 📦 Produtos
- **Catálogo completo** com DataTables (busca, ordenação, paginação)
- **Importação Excel/CSV** com normalização automática de colunas
- Detalhes do produto com informações completas
- Controle de estoque visual

### 🛒 Pedidos
- Criação de pedidos
- Listagem e filtros
- Status do pedido (Pendente, Aprovado, Enviado, Entregue, Cancelado)
- Histórico completo

### 📊 Dashboard
- Estatísticas em tempo real
- Gráficos (Chart.js)
- Resumo de vendas
- Indicadores de performance

### 📧 Notificações
- Sistema de notificações por email
- Alertas no sistema

## 🔧 Requisitos do Servidor

### Mínimos
- PHP 8.1 ou superior
- MySQL 5.7+ ou MariaDB 10.3+
- Extensões PHP: PDO, MySQL, OpenSSL, Mbstring, Tokenizer, XML, Ctype, JSON, BCMath, Fileinfo

### Recomendados
- PHP 8.2+
- MySQL 8.0+
- OPcache ativado
- Redis/Memcached para cache (opcional)
- SSL/HTTPS (Let's Encrypt grátis via Hostinger)

## 📁 Estrutura do Projeto

```
ppp/
├── app/                    # Aplicação Laravel
│   ├── Http/Controllers/  # Controladores
│   ├── Models/            # Models Eloquent
│   └── Services/          # Lógica de negócio
├── config/                # Configurações
├── database/              # Migrations e seeders
├── public/                # Document Root
│   └── build/            # ✅ Assets compilados
├── resources/
│   ├── css/              # Estilos
│   ├── js/               # JavaScript
│   └── views/            # Blade templates
├── routes/               # Rotas da aplicação
├── storage/              # Arquivos e cache
├── vendor/               # Dependências Composer
├── .env.example          # Configuração exemplo
├── composer.json         # Dependências PHP
├── package.json          # Dependências JS
└── vite.config.js        # Configuração Vite
```

## 🐛 Resolução de Problemas

### Erro 500
```bash
# Ativar debug
APP_DEBUG=true

# Verificar logs
tail -f storage/logs/laravel.log

# Verificar permissões
chmod -R 755 storage bootstrap/cache
```

### Assets não carregam
```bash
# Verificar APP_URL no .env
# Limpar cache
php artisan cache:clear
php artisan config:cache
```

### Erro de banco
```bash
# Verificar credenciais no .env
# Testar conexão MySQL

# Ver status das migrations
php artisan migrate:status
```

## 📞 Suporte

- **Documentação Laravel**: https://laravel.com/docs
- **Hostinger Support**: https://support.hostinger.com
- **Bootstrap Docs**: https://getbootstrap.com/docs

## 📝 Notas Importantes

### ⚠️ ANTES DO DEPLOY
1. ✅ Executar `prepare-deploy.ps1`
2. ✅ Verificar `public/build` existe e tem arquivos
3. ✅ NÃO enviar `.env` - criar novo no servidor
4. ✅ NÃO enviar `node_modules`

### ⚠️ APÓS DEPLOY
1. ✅ Configurar `.env` com credenciais reais
2. ✅ Executar `php artisan key:generate`
3. ✅ Executar migrations
4. ✅ Configurar permissões (755)
5. ✅ Criar caches de produção

### 🔒 Segurança
- `.env` nunca deve ser versionado ou público
- `APP_DEBUG=false` em produção
- HTTPS sempre habilitado
- Credenciais fortes para banco e admin

## 📊 Status do Projeto

| Item | Status |
|------|--------|
| Assets Compilados | ✅ OK |
| Composer Otimizado | ✅ OK |
| Cache de Produção | ✅ OK |
| Arquivos Svelte Removidos | ✅ OK |
| Documentação | ✅ OK |
| Scripts de Deploy | ✅ OK |
| Testes | ✅ OK |

---

**Versão**: 1.0  
**Data**: 29/12/2025  
**Status**: 🟢 PRONTO PARA DEPLOY IMEDIATO  

**Desenvolvido com ❤️ usando Laravel + Bootstrap**
