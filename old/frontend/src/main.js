import { createApp } from 'vue';
import * as Sentry from '@sentry/vue';
import App from './App.vue';
import router from './router';
import 'bulma/css/bulma.css';
import '@fortawesome/fontawesome-free/css/all.css';
import './assets/styles.css';

const app = createApp(App);

// Initialize Sentry for error tracking
if (import.meta.env.VITE_SENTRY_DSN) {
  Sentry.init({
    app,
    dsn: import.meta.env.VITE_SENTRY_DSN,
    environment: import.meta.env.MODE || 'development',
    integrations: [
      Sentry.browserTracingIntegration({ router }),
      Sentry.replayIntegration({
        maskAllText: false,
        blockAllMedia: false,
      }),
    ],
    
    // Performance Monitoring
    tracesSampleRate: import.meta.env.MODE === 'production' ? 0.1 : 1.0,
    
    // Session Replay
    replaysSessionSampleRate: 0.1,
    replaysOnErrorSampleRate: 1.0,
    
    // Track components
    trackComponents: true,
    
    // Release tracking
    release: import.meta.env.VITE_APP_VERSION || 'development',
  });
  
  console.log('Sentry initialized for frontend');
} else {
  console.log('Sentry DSN not configured - error tracking disabled');
}

app.use(router).mount('#app');

// Register Service Worker for PWA
if ('serviceWorker' in navigator) {
  window.addEventListener('load', () => {
    navigator.serviceWorker
      .register('/service-worker.js')
      .then((registration) => {
        console.log('Service Worker registrado com sucesso:', registration.scope);
        
        // Check for updates every hour
        setInterval(() => {
          registration.update();
        }, 60 * 60 * 1000);
      })
      .catch((error) => {
        console.error('Falha ao registrar Service Worker:', error);
      });
  });
}

