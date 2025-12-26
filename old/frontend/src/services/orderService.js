import api from './api';
import offlineSync from './offlineSync';

export const orderService = {
  async list(params = {}) {
    try {
      const response = await api.get('/orders', { params });
      return response.data;
    } catch (error) {
      // If offline, return empty array or cached data
      if (!navigator.onLine) {
        console.warn('Offline - não foi possível buscar pedidos');
        return { orders: [], total: 0 };
      }
      throw error;
    }
  },

  async create(payload) {
    try {
      const response = await api.post('/orders', payload);
      return response.data.order;
    } catch (error) {
      // If offline, save to pending queue
      if (!navigator.onLine) {
        const token = localStorage.getItem('token');
        await offlineSync.savePendingOrder(payload, token);
        
        // Return mock order for UI feedback
        return {
          ...payload,
          id: 'pending-' + Date.now(),
          status: 'pending_sync',
          created_at: new Date().toISOString(),
          offline: true
        };
      }
      throw error;
    }
  },

  async updateStatus(orderId, status) {
    const response = await api.patch(`/orders/${orderId}/status`, { status });
    return response.data.order;
  },

  async repeat(orderId, payload = {}) {
    const response = await api.post(`/orders/${orderId}/repeat`, payload);
    return response.data.order;
  },

  async exportCsv(limit = 200) {
    const response = await api.get('/orders/export/csv', {
      params: { limit },
      responseType: 'blob'
    });
    return response.data;
  },

  async openSummary () {
    try {
      const response = await api.get('/orders/open-summary');
      return response.data;
    } catch (error) {
      if (!navigator.onLine) {
        console.warn('Offline - não foi possível buscar resumo');
        return { open_orders: 0, total_value: 0 };
      }
      throw error;
    }
  },

  async getById(orderId) {
    const response = await api.get(`/orders/${orderId}`);
    return response.data;
  },

  async cancel(orderId, motivo) {
    const response = await api.post(`/orders/${orderId}/cancel`, { motivo });
    return response.data;
  }
};

