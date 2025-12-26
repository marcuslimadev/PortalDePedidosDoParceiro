import * as Sentry from '@sentry/node';

/**
 * Initialize Sentry for error tracking and performance monitoring
 */
let SENTRY_ENABLED = false;

export function initSentry (app) {
  const sentryDsn = process.env.SENTRY_DSN;

  // Only initialize in production or if DSN is provided
  if (!sentryDsn) {
    console.log('Sentry DSN not configured - error tracking disabled');
    SENTRY_ENABLED = false;
    return;
  }

  Sentry.init({
    dsn: sentryDsn,
    environment: process.env.NODE_ENV || 'development',

    // Set sample rate for production
    tracesSampleRate: process.env.NODE_ENV === 'production' ? 0.1 : 1.0,
    profilesSampleRate: process.env.NODE_ENV === 'production' ? 0.1 : 1.0,

    integrations: [
      // HTTP integration for Express
      new Sentry.Integrations.Http({ tracing: true }),

      // Express integration
      new Sentry.Integrations.Express({ app }),

    ],

    // Ignore common errors
    ignoreErrors: [
      'ECONNREFUSED',
      'ECONNRESET',
      'EPIPE',
      'ETIMEDOUT'
    ],

    // Release tracking
    release: process.env.RENDER_GIT_COMMIT || 'development',

    // Server name
    serverName: process.env.RENDER_SERVICE_NAME || 'local-dev'
  });

  SENTRY_ENABLED = true;
  console.log(`Sentry initialized for environment: ${process.env.NODE_ENV}`);
}

/**
 * Express error handler middleware that sends to Sentry
 */
export function sentryErrorHandler () {
  if (!SENTRY_ENABLED) return (err, req, res, next) => next(err);
  return Sentry.Handlers.errorHandler({
    shouldHandleError (error) {
      // Send all errors to Sentry except 4xx errors
      if (error.status >= 400 && error.status < 500) {
        return false;
      }
      return true;
    }
  });
}

/**
 * Request handler middleware for Sentry tracing
 */
export function sentryRequestHandler () {
  if (!SENTRY_ENABLED) return (req, res, next) => next();
  return Sentry.Handlers.requestHandler({
    user: ['id', 'email', 'role'],
    ip: true
  });
}

/**
 * Tracing handler middleware for Sentry
 */
export function sentryTracingHandler () {
  if (!SENTRY_ENABLED) return (req, res, next) => next();
  return Sentry.Handlers.tracingHandler();
}

/**
 * Capture exception manually
 */
export function captureException (error, context = {}) {
  Sentry.captureException(error, {
    contexts: {
      custom: context
    }
  });
}

/**
 * Capture message manually
 */
export function captureMessage (message, level = 'info', context = {}) {
  Sentry.captureMessage(message, {
    level,
    contexts: {
      custom: context
    }
  });
}

/**
 * Set user context for Sentry
 */
export function setUserContext (user) {
  if (!user) {
    Sentry.setUser(null);
    return;
  }

  Sentry.setUser({
    id: user.id,
    email: user.email,
    username: user.nome,
    role: user.role
  });
}

/**
 * Add breadcrumb to Sentry
 */
export function addBreadcrumb (message, category = 'custom', level = 'info', data = {}) {
  Sentry.addBreadcrumb({
    message,
    category,
    level,
    data
  });
}

/**
 * Start a new transaction for performance monitoring
 */
export function startTransaction (name, op = 'http.server') {
  return Sentry.startTransaction({
    name,
    op
  });
}

/**
 * Flush Sentry events (useful before shutdown)
 */
export async function flushSentry (timeout = 2000) {
  try {
    await Sentry.close(timeout);
    console.log('Sentry events flushed successfully');
  } catch (error) {
    console.error('Error flushing Sentry events:', error);
  }
}

export default Sentry;
