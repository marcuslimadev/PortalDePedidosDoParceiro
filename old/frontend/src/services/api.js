import axios from 'axios';
import * as Sentry from '@sentry/vue';

export const API_URL = import.meta.env.VITE_API_URL || 'http://localhost:3000/api';

const api = axios.create({
  baseURL: API_URL,
  headers: {
    'Content-Type': 'application/json'
  }
});

api.interceptors.request.use((config) => {
  const token = localStorage.getItem('token');
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
});

// Response interceptor to handle errors and send to Sentry
api.interceptors.response.use(
  (response) => response,
  (error) => {
    // Log API errors to Sentry
    if (error.response) {
      // Server responded with error
      const status = error.response.status;
      
      // Only log 5xx errors to Sentry (server errors)
      if (status >= 500) {
        Sentry.captureException(error, {
          contexts: {
            api: {
              url: error.config.url,
              method: error.config.method,
              status: status,
              data: error.response.data,
            },
          },
          tags: {
            api_error: true,
            status_code: status,
          },
        });
      }
    } else if (error.request) {
      // Request made but no response (network error)
      Sentry.captureException(error, {
        contexts: {
          network: {
            url: error.config.url,
            method: error.config.method,
          },
        },
        tags: {
          network_error: true,
        },
      });
    }
    
    return Promise.reject(error);
  }
);

export const authService = {
  async login(email, password) {
    const response = await api.post('/auth/login', { email, password });
    if (response.data.token) {
      localStorage.setItem('token', response.data.token);
      localStorage.setItem('user', JSON.stringify(response.data.user));
      
      // Set user context in Sentry
      Sentry.setUser({
        id: response.data.user.id,
        email: response.data.user.email,
        username: response.data.user.nome,
        role: response.data.user.role,
      });
    }
    return response.data;
  },

  async register(email, password, nome, role = 'loja') {
    const response = await api.post('/auth/register', { email, password, nome, role });
    if (response.data.token) {
      localStorage.setItem('token', response.data.token);
      localStorage.setItem('user', JSON.stringify(response.data.user));
      
      // Set user context in Sentry
      Sentry.setUser({
        id: response.data.user.id,
        email: response.data.user.email,
        username: response.data.user.nome,
        role: response.data.user.role,
      });
    }
    return response.data;
  },

  async verify() {
    const response = await api.get('/auth/verify');
    return response.data;
  },

  logout() {
    localStorage.removeItem('token');
    localStorage.removeItem('user');
    
    // Clear user context in Sentry
    Sentry.setUser(null);
  },

  getToken() {
    return localStorage.getItem('token');
  },

  loadUserFromToken() {
    const token = localStorage.getItem('token');
    if (!token) return null;
    try {
      const payload = JSON.parse(atob(token.split('.')[1] || ''));
      if (!payload || !payload.id || !payload.role) return null;
      const user = {
        id: payload.id,
        email: payload.email,
        role: payload.role,
        nome: payload.nome || payload.email?.split('@')[0]
      };
      localStorage.setItem('user', JSON.stringify(user));
      return user;
    } catch (error) {
      console.error('Falha ao restaurar sessão do token', error);
      return null;
    }
  },

  getUser() {
    const user = localStorage.getItem('user');
    return user ? JSON.parse(user) : null;
  },

  isAuthenticated() {
    return !!localStorage.getItem('token');
  }
};

export default api;
