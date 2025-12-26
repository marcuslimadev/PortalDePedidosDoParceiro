# Plano Completo de Migração: Node.js/Express/Vue → Laravel + Inertia.js + Vue

## 📊 Progresso Atual: **5% - Análise Completa e Planejamento**

---

## 🎯 Objetivos da Migração

1. **Simplificar arquitetura** - Eliminar mono-repo separado front/back
2. **Usar framework moderno** - Laravel 11 com estrutura consolidada
3. **Integrar Vue nativamente** - Inertia.js elimina necessidade de API REST separada
4. **Manter funcionalidades** - 100% das features existentes preservadas
5. **Melhorar performance** - Cache, queue, eventos nativos do Laravel
6. **Facilitar manutenção** - Código mais idiomático e documentado

---

## 📋 Inventário do Projeto Atual

### Backend (Node.js/Express)
- **Autenticação**: JWT manual (bcryptjs)
- **Banco**: PostgreSQL com migrations raw SQL
- **Rotas**: 9 arquivos de rotas (auth, orders, products, clients, etc.)
- **Controllers**: 5 controllers principais
- **Middleware**: auth, audit, rateLimiter, security
- **Services**: 6 serviços (email, payment, notification, winthor, audit, event listeners)
- **Eventos**: EventEmitter customizado
- **Rate Limiting**: express-rate-limit
- **Monitoramento**: Sentry integrado

### Frontend (Vue 3/Vite)
- **Rotas**: 5 views principais (Login, HomePage, Admin/Operador/Loja Dashboards)
- **Componentes**: RoleCard, ThemeToggle, OfflineIndicator
- **Services**: API axios, offline sync, notificações
- **CSS**: Bulma + custom styles
- **PWA**: Service worker configurado

### Banco de Dados
- **11 migrations** criando:
  - users (com campos de cliente)
  - products (com histórico de preços)
  - orders + order_items
  - notifications
  - audit_logs
  - winthor_sync_logs
  - client_credit_history

### Funcionalidades Core
1. **3 perfis de acesso**: admin, operador, loja
2. **Sistema de crédito**: limite + utilizado por cliente
3. **Catálogo de produtos**: importação CSV, histórico de preços
4. **Pedidos**: criação, validação de crédito, repetição, exportação CSV
5. **Notificações**: sistema + email (nodemailer)
6. **Auditoria**: log de todas ações críticas
7. **Rate limiting**: proteção de endpoints
8. **Integração Winthor**: sync bidirecional (parcial)
9. **Relatórios**: ABC produtos/clientes, exportação
10. **PWA**: modo offline básico

---

## 🚀 Plano de Migração em 10 Etapas

### **ETAPA 1: Setup Inicial (5%)** ⏳ EM ANDAMENTO
- [x] Análise completa do projeto atual
- [ ] Instalação do Laravel 11 em `/laravel`
- [ ] Configuração do Inertia.js + Vue 3
- [ ] Setup do PostgreSQL no Laravel
- [ ] Configuração de autenticação (Laravel Breeze + Inertia)

**Entregáveis**: 
- Laravel instalado e rodando
- Login básico funcionando
- Conexão com PostgreSQL

---

### **ETAPA 2: Migração do Banco de Dados (15%)**
- [ ] Converter 11 migrations SQL para Laravel migrations
- [ ] Criar Models Eloquent:
  - User (com traits de cliente)
  - Product
  - Order, OrderItem
  - Notification
  - AuditLog
  - WinthorSyncLog
  - ClientCreditHistory
- [ ] Seeders para dados de teste (equivalente ao seedMockData.js)
- [ ] Factories para testes

**Entregáveis**:
- Todas tabelas criadas via `php artisan migrate`
- Models com relacionamentos configurados
- Seeders funcionando

---

### **ETAPA 3: Sistema de Autenticação e Autorização (25%)**
- [ ] Configurar roles (admin, operador, loja) com Spatie Permission ou Gates
- [ ] Middleware de autorização por role
- [ ] Login com Inertia.js
- [ ] Recuperação de senha
- [ ] Telas Vue: LoginPage, HomePage
- [ ] Logout e proteção de rotas

**Entregáveis**:
- Auth completa com 3 perfis
- Middleware de autorização
- Telas de login/home funcionando

---

### **ETAPA 4: Gestão de Produtos (35%)**
- [ ] ProductController com CRUD completo
- [ ] Importação CSV de produtos
- [ ] Upload de imagens (Laravel Storage)
- [ ] Histórico de preços (ProductPriceHistory model)
- [ ] Views Inertia:
  - ProductList.vue
  - ProductForm.vue
  - ProductImport.vue
- [ ] API pública de catálogo

**Entregáveis**:
- CRUD de produtos completo
- Importação CSV funcionando
- Catálogo público acessível

---

### **ETAPA 5: Gestão de Clientes (45%)**
- [ ] ClientController com CRUD
- [ ] Gestão de limite de crédito
- [ ] Histórico de alterações de crédito
- [ ] Validação de CNPJ/IE
- [ ] Views Inertia:
  - ClientList.vue
  - ClientForm.vue
  - ClientCreditHistory.vue

**Entregáveis**:
- CRUD de clientes completo
- Sistema de crédito funcionando
- Validações implementadas

---

### **ETAPA 6: Sistema de Pedidos (60%)**
- [ ] OrderController com lógica de negócio
- [ ] Validação de crédito ao criar pedido
- [ ] Cálculo de descontos (PaymentService → Laravel Service)
- [ ] Carrinho de compras
- [ ] Repetir pedido anterior
- [ ] Exportação CSV
- [ ] Views Inertia:
  - Cart.vue
  - OrderList.vue
  - OrderDetail.vue
  - OrderHistory.vue

**Entregáveis**:
- Sistema de pedidos completo
- Validação de crédito funcionando
- Carrinho e histórico operacionais

---

### **ETAPA 7: Notificações e Eventos (70%)**
- [ ] Converter EventBus para Laravel Events
- [ ] Event Listeners:
  - OrderCreated
  - OrderStatusUpdated
  - OrderCancelled
- [ ] Notification system (banco + email)
- [ ] Jobs para envio assíncrono de emails
- [ ] Queue configurada (database driver)
- [ ] Views Inertia:
  - NotificationCenter.vue

**Entregáveis**:
- Eventos Laravel funcionando
- Emails sendo enviados
- Notificações in-app operacionais

---

### **ETAPA 8: Auditoria e Segurança (80%)**
- [ ] Middleware de auditoria
- [ ] AuditService → Laravel Service
- [ ] Rate limiting (Laravel throttle middleware)
- [ ] Helmet → Laravel Security Headers
- [ ] Logs centralizados
- [ ] Views Inertia:
  - AuditLogList.vue

**Entregáveis**:
- Auditoria completa de ações
- Rate limiting configurado
- Logs acessíveis

---

### **ETAPA 9: Relatórios e Integração Winthor (90%)**
- [ ] ReportController com análises ABC
- [ ] WinthorService para sync
- [ ] Importação de produtos/clientes Winthor
- [ ] Exportação de pedidos Winthor
- [ ] Views Inertia:
  - Reports.vue (ABC, vendas, etc.)
  - WinthorSync.vue

**Entregáveis**:
- Relatórios funcionando
- Integração Winthor básica operacional

---

### **ETAPA 10: PWA, Testes e Deploy (100%)**
- [ ] Configurar PWA no Laravel (service worker)
- [ ] Modo offline com Inertia
- [ ] Testes Feature:
  - AuthTest
  - OrderTest
  - ProductTest
  - ClientTest
- [ ] Testes Browser (Laravel Dusk)
- [ ] Configurar deploy (adaptar render.yaml para Laravel)
- [ ] Documentação final
- [ ] Migração de dados de produção

**Entregáveis**:
- PWA funcionando
- Testes passando
- App deployada em produção
- Dados migrados

---

## 🛠️ Stack Tecnológica Final

### Backend
- **Framework**: Laravel 11
- **Auth**: Laravel Breeze + Inertia.js
- **ORM**: Eloquent
- **Database**: PostgreSQL
- **Queue**: Database driver (escalável para Redis)
- **Cache**: File/Redis
- **Email**: Laravel Mail (mantendo nodemailer configs)
- **Validation**: Form Requests
- **Authorization**: Gates + Policies

### Frontend
- **Framework**: Vue 3 (Composition API)
- **Rendering**: Inertia.js (SSR-like, sem API REST)
- **CSS**: Bulma (mantendo design atual)
- **Build**: Vite (integrado ao Laravel)
- **PWA**: Laravel PWA package
- **Icons**: FontAwesome (manter atual)

### DevOps
- **Deploy**: Render.com (adaptar render.yaml)
- **CI/CD**: GitHub Actions (adaptar para Laravel)
- **Monitoring**: Laravel Telescope + Sentry
- **Database**: PostgreSQL gerenciado

---

## 📁 Nova Estrutura de Diretórios

```
/laravel
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── AuthController.php
│   │   │   ├── ProductController.php
│   │   │   ├── OrderController.php
│   │   │   ├── ClientController.php
│   │   │   ├── ReportController.php
│   │   │   └── WinthorController.php
│   │   ├── Middleware/
│   │   │   ├── AuditMiddleware.php
│   │   │   ├── RoleMiddleware.php
│   │   │   └── CheckCreditLimit.php
│   │   └── Requests/
│   │       ├── OrderStoreRequest.php
│   │       ├── ProductStoreRequest.php
│   │       └── ClientUpdateRequest.php
│   ├── Models/
│   │   ├── User.php
│   │   ├── Product.php
│   │   ├── Order.php
│   │   ├── OrderItem.php
│   │   ├── Notification.php
│   │   ├── AuditLog.php
│   │   └── WinthorSyncLog.php
│   ├── Services/
│   │   ├── PaymentService.php
│   │   ├── EmailService.php
│   │   ├── AuditService.php
│   │   ├── WinthorService.php
│   │   └── NotificationService.php
│   ├── Events/
│   │   ├── OrderCreated.php
│   │   ├── OrderStatusUpdated.php
│   │   └── OrderCancelled.php
│   ├── Listeners/
│   │   ├── SendOrderCreatedNotification.php
│   │   ├── UpdateCreditUsed.php
│   │   └── LogOrderEvent.php
│   └── Jobs/
│       ├── SendEmailJob.php
│       └── SyncWinthorJob.php
├── database/
│   ├── migrations/
│   ├── seeders/
│   └── factories/
├── resources/
│   ├── js/
│   │   ├── Pages/
│   │   │   ├── Auth/
│   │   │   │   ├── Login.vue
│   │   │   │   └── Register.vue
│   │   │   ├── Admin/
│   │   │   │   └── Dashboard.vue
│   │   │   ├── Operador/
│   │   │   │   └── Dashboard.vue
│   │   │   ├── Loja/
│   │   │   │   ├── Dashboard.vue
│   │   │   │   ├── Cart.vue
│   │   │   │   └── OrderHistory.vue
│   │   │   ├── Products/
│   │   │   │   ├── Index.vue
│   │   │   │   └── Form.vue
│   │   │   ├── Orders/
│   │   │   │   ├── Index.vue
│   │   │   │   └── Show.vue
│   │   │   └── Clients/
│   │   │       ├── Index.vue
│   │   │       └── Form.vue
│   │   ├── Components/
│   │   │   ├── RoleCard.vue
│   │   │   ├── ThemeToggle.vue
│   │   │   └── OfflineIndicator.vue
│   │   ├── Layouts/
│   │   │   ├── AppLayout.vue
│   │   │   └── GuestLayout.vue
│   │   └── app.js
│   ├── css/
│   │   └── app.css
│   └── views/
│       └── app.blade.php
├── routes/
│   ├── web.php
│   └── api.php (mínimo, para catálogo público)
├── tests/
│   ├── Feature/
│   │   ├── AuthTest.php
│   │   ├── OrderTest.php
│   │   ├── ProductTest.php
│   │   └── ClientTest.php
│   └── Browser/
│       └── OrderFlowTest.php
├── .env
├── composer.json
├── package.json
└── vite.config.js
```

---

## 🔄 Equivalências: Node.js → Laravel

| Node.js/Express | Laravel |
|----------------|---------|
| `express.Router()` | `Route::` facades |
| `bcryptjs.hash()` | `Hash::make()` |
| `jsonwebtoken` | Laravel Sanctum/Breeze |
| Middleware manual | Laravel Middleware |
| `query()` raw SQL | Eloquent ORM |
| EventEmitter | Laravel Events |
| nodemailer | Laravel Mail |
| express-rate-limit | `throttle` middleware |
| helmet | Laravel Security Headers |
| Manual validation | Form Requests |
| Manual audit log | Middleware + Events |
| pg driver | PostgreSQL via PDO |

---

## ⚠️ Pontos de Atenção

1. **Sessões vs JWT**: Laravel Breeze usa sessões. Avaliar se mantemos JWT (Sanctum SPA) ou migramos para sessions
2. **Sync vs Queue**: Emails síncronos no Node → Queue no Laravel
3. **Client-side routing**: Vue Router → Inertia.js (server-side routing)
4. **localStorage auth**: Migrar para cookies httpOnly
5. **Service Worker**: Recriar PWA configs para Inertia
6. **Rate limiting**: Configurar por rota no Laravel
7. **Winthor sync**: Avaliar se fica síncrono ou vira Job
8. **Render deploy**: Adaptar Dockerfile e build commands

---

## 📈 Cronograma Estimado

- **Etapa 1-2**: 3 dias (setup + banco)
- **Etapa 3-4**: 5 dias (auth + produtos)
- **Etapa 5-6**: 7 dias (clientes + pedidos)
- **Etapa 7-8**: 4 dias (eventos + auditoria)
- **Etapa 9-10**: 6 dias (relatórios + deploy)

**Total**: ~25 dias úteis (5 semanas)

---

## ✅ Próximos Passos Imediatos

1. ✅ Análise completa do projeto (CONCLUÍDO)
2. ⏳ Instalar Laravel 11 em `/laravel` (EM ANDAMENTO)
3. ⏳ Configurar Inertia.js + Vue 3
4. ⏳ Conectar PostgreSQL
5. ⏳ Criar primeira migration e model

---

**Progresso será reportado a cada etapa concluída.**

**Meta**: Migração completa em 5 semanas com 100% das funcionalidades preservadas.
