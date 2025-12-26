import api from './api';

export const notificationService = {
  async list(limit = 20) {
    const response = await api.get('/notifications', { params: { limit } });
    return response.data;
  },
  async markRead(id) {
    const response = await api.patch(`/notifications/${id}/read`);
    return response.data.notification;
  },
  async markAll() {
    await api.post('/notifications/read-all');
  }
};
