import api from './api';

export const orderService = {
  async list(params = {}) {
    const response = await api.get('/orders', { params });
    return response.data.orders;
  },

  async create(payload) {
    const response = await api.post('/orders', payload);
    return response.data.order;
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
    const response = await api.get('/orders/open-summary');
    return response.data;
  }
};
