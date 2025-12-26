# 🚀 Portal de Pedidos do Parceiro - Laravel 11

Sistema moderno de gestão de pedidos B2B com controle de crédito e múltiplos perfis de usuário.

## 📦 Stack Tecnológico

- **Backend:** Laravel 11 (PHP 8.2)
- **Frontend:** Vue 3 + Inertia.js
- **Database:** MySQL 8.0 (XAMPP)
- **UI Framework:** Bulma CSS
- **Icons:** Font Awesome
- **Build Tool:** Vite 7

## ✨ Funcionalidades Principais

### Gerenciamento de Usuários
- **3 Perfis:** Admin, Operador, Loja
- Autenticação via Laravel Sanctum
- Sistema de roles e permissões

### Gestão de Pedidos
- Criação de pedidos pela loja
- Validação automática de crédito
- Cálculo de descontos por prazo de pagamento:
  - **Antecipado:** 5% de desconto
  - **30 dias:** 2% de desconto
  - **60/90 dias:** sem desconto
- Aprovação/rejeição por admin/operador
- Cancelamento com registro de motivo

### Controle de Crédito
- Limite de crédito configurável por loja
- Validação transacional (evita race conditions)
- Histórico de alterações de crédito
- Liberação automática em caso de cancelamento

### Sistema de Notificações
- Notificações em tempo real
- Alertas de novos pedidos
- Mudanças de status
- Contador de não lidas

### Auditoria
- Log automático de todas ações críticas
- Rastreamento de alterações (old/new values)
- IP e User Agent registrados
- Filtros por usuário, ação e recurso

### Catálogo de Produtos
- Busca e filtros por categoria
- Controle de estoque
- Histórico de preços
- Integração preparada para Winthor

## 🔧 Instalação

### Pré-requisitos
- XAMPP (PHP 8.2 + MySQL)
- Composer
- Node.js 18+

### Passo a Passo

1. **Clone o repositório:**
```powershell
cd C:\xampp\htdocs\PortalDePedidosDoParceiro\laravel
```

2. **Instale dependências PHP:**
```powershell
C:\xampp\php\php.exe C:\xampp\php\composer.phar install
```

3. **Instale dependências Node:**
```powershell
npm install --force
```

4. **Configure .env:**
```env
APP_NAME="Portal de Pedidos do Parceiro"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=portal_pedidos
DB_USERNAME=root
DB_PASSWORD=
```

5. **Gere APP_KEY:**
```powershell
C:\xampp\php\php.exe artisan key:generate
```

6. **Rode as migrations:**
```powershell
C:\xampp\php\php.exe artisan migrate
```

7. **Popule o banco de dados:**
```powershell
C:\xampp\php\php.exe artisan db:seed
```

8. **Compile o frontend:**
```powershell
npm run build
```

9. **Inicie o servidor:**
```powershell
C:\xampp\php\php.exe artisan serve --port=8000
```

10. **Acesse:**
```
http://localhost:8000
```

## 👥 Usuários de Teste

### Admin
- **Email:** admin@portalpedidos.com
- **Senha:** admin123
- **Permissões:** Todas

### Operador
- **Email:** operador@portalpedidos.com
- **Senha:** operador123
- **Permissões:** Gerenciar pedidos, visualizar relatórios

### Lojas
- **Email:** loja1@cliente.com | loja2@cliente.com | loja3@cliente.com
- **Senha:** cliente123
- **Permissões:** Criar pedidos, visualizar próprios pedidos

## 🗂️ Estrutura de Pastas

```
laravel/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── AuthController.php
│   │   │   ├── OrderController.php
│   │   │   ├── ProductController.php
│   │   │   └── NotificationController.php
│   │   └── Middleware/
│   │       ├── CheckRole.php
│   │       └── AuditMiddleware.php
│   ├── Models/
│   │   ├── User.php
│   │   ├── Order.php
│   │   ├── Product.php
│   │   └── ...
│   └── Services/
│       ├── PaymentService.php
│       ├── CreditService.php
│       ├── AuditService.php
│       └── NotificationService.php
├── database/
│   ├── migrations/
│   └── seeders/
├── resources/
│   ├── js/
│   │   ├── Pages/
│   │   │   ├── Admin/Dashboard.vue
│   │   │   ├── Operador/Dashboard.vue
│   │   │   ├── Loja/Dashboard.vue
│   │   │   └── Orders/Index.vue
│   │   └── Components/
│   └── css/
│       └── app.css (Bulma)
└── routes/
    └── web.php
```

## 🔐 Segurança

- **CSRF Protection:** Automático via Inertia
- **SQL Injection:** Proteção via Eloquent ORM
- **XSS:** Escapamento automático do Vue
- **Rate Limiting:** Configurável por rota
- **Transações:** Lock de linha (FOR UPDATE) em operações de crédito
- **Audit Logs:** Rastreamento completo de ações

## 📊 Modelos de Dados

### User
- Roles: admin, operador, loja
- Campos de cliente (CNPJ, crédito, etc)
- Relacionamentos: orders, notifications, creditHistory

### Order
- Status: pendente, aprovado, cancelado
- Totais com desconto automático
- Relacionamentos: loja, items

### Product
- Catálogo com estoque
- Histórico de preços
- Campos Winthor (preparado para integração)

### Notification
- Sistema de notificações
- Marcação de leitura
- Filtros por tipo

### AuditLog
- Registro de todas ações
- Old/new values (JSON)
- Metadata extensível

## 🛣️ Rotas Principais

```php
// Dashboard (role-based)
GET  /dashboard

// Produtos
GET  /products
GET  /products/{id}

// Pedidos
GET  /orders
GET  /orders/create        (role: loja)
POST /orders               (role: loja)
GET  /orders/{id}
PATCH /orders/{id}/status  (role: admin,operador)

// Notificações
GET  /notifications
GET  /notifications/unread
PATCH /notifications/{id}/read
POST /notifications/read-all
```

## 🎨 Componentes Vue Principais

### Layouts
- **AuthenticatedLayout:** Layout padrão autenticado
- **GuestLayout:** Layout para login/registro

### Pages
- **Admin/Dashboard:** Visão geral administrativa
- **Operador/Dashboard:** Gestão de pedidos
- **Loja/Dashboard:** Área do cliente
- **Orders/Index:** Listagem de pedidos

## 🔄 Fluxo de Pedido

1. **Loja cria pedido:**
   - Seleciona produtos e quantidades
   - Escolhe prazo de pagamento
   - Sistema calcula desconto automaticamente

2. **Validação de crédito:**
   - Verifica limite disponível
   - Usa transação com lock para evitar concorrência
   - Bloqueia se crédito insuficiente

3. **Pedido criado:**
   - Status: pendente
   - Crédito reservado
   - Notificações enviadas

4. **Admin/Operador aprova:**
   - Muda status para aprovado
   - Notifica loja

5. **Cancelamento:**
   - Libera crédito automaticamente
   - Registra motivo
   - Notifica interessados

## 📈 Próximas Implementações

- [ ] Relatórios ABC de produtos
- [ ] Export de pedidos (Excel/PDF)
- [ ] Integração Winthor (sync produtos, clientes, envio de pedidos)
- [ ] Dashboard com gráficos (Chart.js)
- [ ] Sistema de chat/comentários em pedidos
- [ ] Multi-tenant para múltiplas empresas
- [ ] API REST documentada (Scribe)

## 🐛 Debug

### Logs
```powershell
# Ver logs em tempo real
Get-Content -Path "storage\logs\laravel.log" -Tail 50 -Wait
```

### Cache
```powershell
# Limpar cache
C:\xampp\php\php.exe artisan cache:clear
C:\xampp\php\php.exe artisan config:clear
C:\xampp\php\php.exe artisan route:clear
C:\xampp\php\php.exe artisan view:clear
```

### Migrations
```powershell
# Resetar banco
C:\xampp\php\php.exe artisan migrate:fresh --seed
```

## 📞 Suporte

Para dúvidas ou problemas:
1. Verifique os logs em `storage/logs/laravel.log`
2. Revise a documentação do Laravel 11
3. Consulte o plano de migração em `PLANO_MIGRACAO_LARAVEL_INERTIA.md`

## 📄 Licença

Uso interno - Todos os direitos reservados

---

**Desenvolvido com ❤️ usando Laravel 11 + Inertia.js + Vue 3**
