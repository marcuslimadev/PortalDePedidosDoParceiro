import api from './api';

export const notificationService = {
  async list() {
    const response = await api.get('/notifications');
    return response.data.notifications;
  },

  async getUnreadCount() {
    const response = await api.get('/notifications/unread-count');
    return response.data.count;
  },

  async markAsRead(notificationId) {
    const response = await api.patch(`/notifications/${notificationId}/read`);
    return response.data.notification;
  },

  async markAllAsRead() {
    const response = await api.patch('/notifications/mark-all-read');
    return response.data.success;
  }
};
