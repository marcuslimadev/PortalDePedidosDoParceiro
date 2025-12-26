# Sentry Configuration Guide

## Backend (Node.js)

### Environment Variables

Add to `.env`:

```env
SENTRY_DSN=https://your-sentry-dsn@sentry.io/project-id
NODE_ENV=production
RENDER_GIT_COMMIT=auto  # Render automatically provides this
RENDER_SERVICE_NAME=portal-pedidos-backend
```

### Features Enabled

- **Error Tracking**: Automatic capture of uncaught exceptions
- **Performance Monitoring**: 10% sample rate in production
- **Profiling**: CPU and memory profiling
- **Request Context**: User ID, email, role tracked
- **Breadcrumbs**: HTTP requests logged
- **Release Tracking**: Git commit SHA

### Error Filtering

- Ignores 4xx client errors (only logs 5xx)
- Ignores common network errors (ECONNREFUSED, etc)
- Full stack traces in development

### Usage Examples

```javascript
import { captureException, captureMessage, setUserContext } from './config/sentry.js';

// Capture exception manually
try {
  // risky operation
} catch (error) {
  captureException(error, { orderId: 123 });
}

// Log informational message
captureMessage('Order approved', 'info', { orderId: 123 });

// Set user context
setUserContext({ id: 1, email: 'user@example.com', role: 'admin' });
```

## Frontend (Vue 3)

### Environment Variables

Add to `.env`:

```env
VITE_SENTRY_DSN=https://your-sentry-dsn@sentry.io/project-id
VITE_APP_VERSION=1.0.0
```

### Features Enabled

- **Error Tracking**: Vue error handler integration
- **Performance Monitoring**: Router tracing
- **Session Replay**: 10% normal, 100% on errors
- **Component Tracking**: Vue component errors
- **User Context**: Set on login/logout
- **API Error Logging**: 5xx errors logged automatically

### Source Maps

Enabled in `vite.config.js`:
- Source maps generated for production
- Source code excluded from maps (security)
- Upload to Sentry for stack trace resolution

### Usage Examples

```javascript
import * as Sentry from '@sentry/vue';

// Capture error manually
Sentry.captureException(new Error('Something went wrong'));

// Add breadcrumb
Sentry.addBreadcrumb({
  message: 'User clicked button',
  category: 'ui',
  level: 'info'
});

// Set custom context
Sentry.setContext('order', {
  id: 123,
  total: 1000
});
```

## Render.com Deployment

### Backend Environment Variables

Set in Render dashboard:

```
SENTRY_DSN=https://...
NODE_ENV=production
```

### Frontend Environment Variables

Set in Render dashboard:

```
VITE_SENTRY_DSN=https://...
VITE_APP_VERSION=1.0.0
```

### Build Commands

Backend (unchanged):
```bash
npm install
```

Frontend:
```bash
npm install && npm run build
```

## Sentry Project Setup

1. **Create Project**: Go to sentry.io and create new project
2. **Choose Platform**: 
   - Backend: Node.js
   - Frontend: Vue
3. **Get DSN**: Copy DSN from project settings
4. **Configure Alerts**: Set up email/Slack notifications
5. **Set Sample Rates**:
   - Errors: 100%
   - Transactions: 10% (production)
   - Replays: 10% (production)

## Monitoring Best Practices

### What Gets Logged

**Backend:**
- Unhandled exceptions
- Database errors
- Authentication failures (500s only)
- API errors (5xx only)

**Frontend:**
- Vue component errors
- API call failures (5xx only)
- Network errors
- Unhandled promise rejections

### What Doesn't Get Logged

- Client errors (4xx) - expected behavior
- Network timeouts (common in mobile)
- Rate limit errors (expected)
- Validation errors (user input)

### Performance Impact

- Minimal overhead (~1-2ms per request)
- Async error reporting
- Configurable sample rates
- No impact on user experience

## Troubleshooting

### No errors showing in Sentry

1. Check DSN is set correctly
2. Verify environment is not 'test'
3. Check network connectivity
4. Look for initialization logs

### Source maps not working

1. Ensure `sourcemap: true` in vite.config.js
2. Verify build generates .map files
3. Upload source maps to Sentry (optional)

### Too many errors

1. Adjust sample rates
2. Add more error filters
3. Use `ignoreErrors` array
4. Set up error grouping rules

## Cost Optimization

- Use 10% transaction sample rate in production
- Enable replay only on errors
- Set quotas in Sentry dashboard
- Use error filtering to reduce noise

## Security Considerations

- Never log sensitive data (passwords, tokens)
- Exclude source code from source maps
- Use environment variables for DSN
- Sanitize user data in error context
- Review PII scrubbing rules

## Integration with Existing Systems

- Compatible with existing logging
- Works with helmet security headers
- No conflict with rate limiting
- Integrates with CI/CD pipeline
- No changes needed for testing
