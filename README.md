# Portal de Pedidos do Parceiro

Sistema completo de gestão de pedidos desenvolvido em Laravel 11, com interface moderna em Vue 3 + Inertia.js.

## 📋 Sobre o Projeto

Sistema migrado de Node.js/Express para Laravel 11, oferecendo:

- **Gestão de Pedidos**: Sistema completo de criação e acompanhamento de pedidos
- **Controle de Crédito**: Validação automática de limites de crédito com transações seguras
- **Múltiplos Perfis**: Admin, Operador e Loja com permissões específicas
- **Auditoria Completa**: Log de todas as operações críticas
- **Notificações em Tempo Real**: Sistema de notificações para usuários
- **Histórico de Preços**: Rastreamento completo de alterações de preços
- **Integração Winthor**: Logs de sincronização com sistema legado

## 🚀 Tecnologias

- **Backend**: Laravel 11 (Framework 12.44.0)
- **Frontend**: Vue 3.5 + Inertia.js 2.0
- **CSS Framework**: Bulma 1.0 + Font Awesome
- **Database**: MySQL 8.0
- **Build Tool**: Vite 7.3
- **PHP**: 8.2.12 (XAMPP)
- **Autenticação**: Laravel Breeze
- **Permissões**: Spatie Laravel Permission 6.24

## ⚙️ Instalação Rápida

### Pré-requisitos
- XAMPP (PHP 8.2.12, MySQL 8.0)
- Composer 2.9+
- Node.js 18+

### Método Automatizado
```powershell
# Execute o script de instalação automática
.\setup.ps1
```

### Método Manual

1. **Instalar dependências**
```bash
composer install
npm install
```

2. **Configurar ambiente**
```bash
cp .env.example .env
php artisan key:generate
```

3. **Configurar banco de dados no .env**
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=portal_pedidos
DB_USERNAME=root
DB_PASSWORD=
```

4. **Executar migrações e seeders**
```bash
php artisan migrate --seed
```

5. **Compilar frontend**
```bash
npm run build
```

6. **Iniciar servidor**
```bash
php artisan serve --port=8000
```

## 👤 Usuários Padrão

Após executar os seeders, você terá acesso a:

| Email | Senha | Papel |
|-------|-------|-------|
| admin@example.com | password | Admin |
| operador@example.com | password | Operador |
| loja1@example.com | password | Loja |
| loja2@example.com | password | Loja |
| loja3@example.com | password | Loja |

## 📁 Estrutura do Projeto

```
app/
├── Http/
│   ├── Controllers/      # OrderController, ProductController, etc.
│   └── Middleware/       # CheckRole, AuditMiddleware
├── Models/              # User, Order, Product, etc.
└── Services/            # CreditService, PaymentService, etc.

resources/
├── js/
│   ├── Pages/           # Admin, Operador, Loja Dashboards
│   └── Components/      # Componentes reutilizáveis Vue
└── css/                 # Estilos Bulma customizados

database/
├── migrations/          # Esquema completo do banco
└── seeders/            # Dados iniciais

routes/
└── web.php             # Todas as rotas da aplicação
```

## 🔧 Funcionalidades Principais

### Sistema de Pedidos
- Criação de pedidos com validação de crédito
- Cálculo automático de descontos por forma de pagamento
- Status: Pendente, Aprovado, Rejeitado, Cancelado
- Bloqueio de crédito durante processamento

### Gestão de Crédito
- Limite de crédito por loja
- Validação transacional com locks (FOR UPDATE)
- Histórico completo de alterações
- Liberação automática de crédito em cancelamentos

### Auditoria
- Log automático de operações críticas
- Rastreamento de IP, user agent e dados alterados
- Middleware para auditoria em rotas sensíveis

### Notificações
- Sistema de notificações por usuário
- Marcação de leitura
- Filtros por tipo (info, success, warning, error)

## 📚 Documentação Adicional

- [README_LARAVEL.md](README_LARAVEL.md) - Documentação detalhada do Laravel
- [GUIA_RAPIDO.md](GUIA_RAPIDO.md) - Guia rápido de uso
- [CHANGELOG_MIGRACAO.md](CHANGELOG_MIGRACAO.md) - Log da migração Node.js → Laravel

## 🗂️ Projeto Antigo

O projeto original em Node.js/Express está arquivado na pasta `/old` para referência.

## 📄 Licença

Este projeto é proprietário e confidencial.
