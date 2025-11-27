import api from './api';

export const orderService = {
  async list() {
    const response = await api.get('/orders');
    return response.data.orders;
  },

  async create(payload) {
    const response = await api.post('/orders', payload);
    return response.data.order;
  },

  async updateStatus(orderId, status) {
    const response = await api.patch(`/orders/${orderId}/status`, { status });
    return response.data.order;
  }
};
