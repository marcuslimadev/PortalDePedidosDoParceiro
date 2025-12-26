# Portal de Pedidos do Parceiro - AI Coding Guide

## Architecture Overview

**Mono-repo structure** with Node.js/Express backend and Vue 3 SPA frontend, deployed to Render.com with managed PostgreSQL.

- **Backend** (`backend/`): REST API with JWT auth, role-based access (admin/operador/loja), event-driven notifications
- **Frontend** (`frontend/`): Vue 3 + Vite + Bulma, hash-based routing, localStorage for auth state
- **Database**: PostgreSQL with migration-based schema management (no ORM)

## Critical Patterns

### Database & Migrations

**Raw SQL only** - No ORM. Use `query()` and `getClient()` from `config/database.js`:

```javascript
import { query, getClient } from '../config/database.js';

// Simple queries
const result = await query('SELECT * FROM users WHERE id = $1', [userId]);

// Transactions (REQUIRED for multi-table writes)
const client = await getClient();
try {
  await client.query('BEGIN');
  await client.query('INSERT INTO orders...', [...]);
  await client.query('UPDATE users SET credit_used = ...', [...]);
  await client.query('COMMIT');
} finally {
  client.release();
}
```

**Migrations** run automatically on server start (`runMigrations()` in `server.js`). Add new `.sql` files to `backend/src/migrations/` with numeric prefixes (e.g., `009_*.sql`). All migrations must be **idempotent** using `IF NOT EXISTS`, `ON CONFLICT DO NOTHING`, etc.

### Authentication & Authorization

Three roles: `admin`, `operador`, `loja`. JWT tokens stored in localStorage (frontend) and validated via middleware (backend):

```javascript
// Protect routes with role requirements
import { authenticateToken, requireRole } from '../middleware/auth.js';

router.get('/clients', authenticateToken, requireRole('admin', 'operador'), ...);
```

User object from token includes: `id`, `role`, `email`, `nome`. Access via `req.user` after authentication.

### Credit Limit System

**Critical business logic**: All order creation MUST validate `credit_limit` vs `credit_used` before committing. Use `FOR UPDATE` row locks when reading user credit data in transactions:

```javascript
const lojaResult = await client.query(
  `SELECT credit_limit, credit_used FROM users WHERE id = $1 FOR UPDATE`,
  [req.user.id]
);
```

After order creation, atomically increment `credit_used` in the same transaction.

### Event-Driven Notifications

Use `eventBus` (Node.js EventEmitter) for order state changes. Emit events in controllers, handle in service layer with email notifications:

```javascript
import { eventBus } from '../events/eventBus.js';
import { sendEmail, emailTemplates } from '../services/emailService.js';

// Emit order events
eventBus.emit('order-event', { type: 'order.created', lojaId, payload: order });
eventBus.emit('order-event', { type: 'order.status_updated', lojaId, payload: order });
eventBus.emit('order-event', { type: 'order.cancelled', lojaId, payload: order, motivo });

// Event listeners automatically handle notifications (system + email)
// See services/eventListeners.js for registered handlers
```

### Payment Terms & Discounts

Use `paymentService` for automatic discount calculation based on payment terms:

```javascript
import { getPaymentTerms, calculateOrderTotals } from '../services/paymentService.js';

// Apply payment terms (client default or custom)
const paymentTerms = getPaymentTerms(requestedTerms, clientDefaultTerms);

// Calculate totals with automatic discounts
const { subtotal, discount, discountPercentage, total } = calculateOrderTotals(subtotal, paymentTerms);
// Antecipado: 5% discount, 30 dias: 2% discount, others: 0%
```

### Rate Limiting & Security

Rate limiters protect critical endpoints. Import from `middleware/rateLimiter.js`:

```javascript
import { loginLimiter, orderCreationLimiter, exportLimiter } from '../middleware/rateLimiter.js';

// Apply to routes
router.post('/login', loginLimiter, login); // 5 attempts per 15min
router.post('/orders', orderCreationLimiter, createOrder); // 20 orders per hour
router.get('/export', exportLimiter, exportData); // 10 exports per hour
```

### Audit System

All critical operations are automatically logged via `auditMiddleware`:

```javascript
import { auditMiddleware } from '../middleware/audit.js';
import { logAudit } from '../services/auditService.js';

// Automatic audit for routes
router.post('/orders', auditMiddleware('create', 'order'), createOrder);
router.put('/clients/:id', auditMiddleware('credit_update', 'client'), updateClient);

// Manual audit logging
await logAudit({
  userId, userEmail, userRole, action, resourceType, resourceId,
  description, ipAddress, userAgent, oldValues, newValues
});
```

View audit logs: `GET /api/audit?userId=1&action=create&limit=50` (admin only)

## Development Workflows

### Local Development

**Backend:**
```powershell
cd backend
npm install
# Create .env with DATABASE_URL, JWT_SECRET, PORT
npm run dev  # nodemon watches for changes
```

**Frontend:**
```powershell
cd frontend
npm install
# Create .env with VITE_API_URL=http://localhost:3000/api
npm run dev  # Vite dev server on port 5173
```

### Database Setup

**Auto-migration** runs on server start. For manual migration or seeding:

```powershell
# Run migrations only
node backend/src/migrations/run.js

# Seed mock data (users + products + orders)
npm run seed:mock  # Creates admin/operador/3 lojas, 15 products, 3 orders
```

Default credentials after seed:
- Admin: `admin@portalpedidos.com` / `admin123`
- Operador: `operador@portalpedidos.com` / `operador123`
- Lojas: `loja1@cliente.com`, `loja2@cliente.com`, `loja3@cliente.com` / `cliente123`

### Deployment

**Render.com** auto-deploys on push to `master`. Config in `render.yaml`:
- Backend: Node web service (`backend/` rootDir)
- Frontend: Static site (`frontend/dist`)
- PostgreSQL: Managed database

Environment variables set via Render dashboard or `render.yaml`. `DATABASE_URL` auto-injected from managed DB.

## Code Conventions

- **ES Modules**: All files use `import`/`export` (set `"type": "module"` in package.json)
- **ESLint**: Standard config enforced in CI. Run `npm run lint` before committing
- **Error handling**: Return structured JSON errors with HTTP status codes (400/403/404/500)
- **Async/await**: Always use for DB queries, no callbacks or raw promises
- **Password hashing**: Use `bcryptjs` with 10 rounds for all user passwords

## Frontend Architecture

- **Router guards**: Check auth state in `router/index.js` before each navigation
- **API service**: Centralized in `services/api.js` with axios interceptors for token injection
- **Role-based views**: Each role has dedicated dashboard (`AdminDashboard.vue`, `OperadorDashboard.vue`, `LojaDashboard.vue`)
- **Bulma CSS**: Use Bulma classes for styling, no custom CSS unless necessary

## Testing & CI

GitHub Actions workflow (`.github/workflows/ci.yml`) runs on push/PR:
1. Backend lint (`npm run lint`)
2. Frontend build (`npm run build`)

No automated tests yet. Manual testing against local DB required.

## Key Files Reference

- `backend/src/server.js`: App entry, runs migrations + seed on startup, registers event listeners
- `backend/src/controllers/orderController.js`: Order creation logic with credit validation and payment discounts
- `backend/src/middleware/auth.js`: JWT auth + role-based access control
- `backend/src/middleware/rateLimiter.js`: Rate limiting for API endpoints
- `backend/src/middleware/audit.js`: Automatic audit logging middleware
- `backend/src/services/paymentService.js`: Payment terms and discount calculation
- `backend/src/services/emailService.js`: Email notifications with nodemailer
- `backend/src/services/eventListeners.js`: Event handlers for order notifications
- `backend/src/services/auditService.js`: Audit log management
- `backend/src/routes/reports.js`: ABC analysis and analytics endpoints
- `backend/src/scripts/seedMockData.js`: Seed script for development data
- `frontend/src/services/api.js`: Axios wrapper with auth token handling
- `render.yaml`: Production deployment configuration

## Common Tasks

**Add a new migration:**
1. Create `backend/src/migrations/00X_description.sql`
2. Ensure idempotency (use `IF NOT EXISTS`, `ON CONFLICT`, etc.)
3. Migration runs automatically on next server start

**Add a new API endpoint:**
1. Create route in `backend/src/routes/`
2. Implement controller in `backend/src/controllers/`
3. Add authentication middleware if needed
4. Update frontend service in `frontend/src/services/`

**Update user credit limits:**
Only admin/operador can modify via `PUT /api/clients/:id`. Frontend updates via `AdminDashboard.vue` or `OperadorDashboard.vue`.
