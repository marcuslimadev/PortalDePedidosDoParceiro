import api from './api';

export const clientService = {
  async list() {
    const response = await api.get('/clients');
    return response.data.clients;
  },

  async get(id) {
    const response = await api.get(`/clients/${id}`);
    return response.data.client;
  },

  async update(id, payload) {
    const response = await api.put(`/clients/${id}`, payload);
    return response.data.client;
  },

  async history(id) {
    const response = await api.get(`/clients/${id}/history`);
    return response.data.history;
  },

  async resetAccess(id, payload) {
    const response = await api.put(`/clients/${id}/access`, payload);
    return response.data;
  }
};
