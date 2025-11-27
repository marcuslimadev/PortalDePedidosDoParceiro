import api from './api';

export const productService = {
  async list(query = '') {
    const params = query ? { params: { q: query } } : {};
    const response = await api.get('/products', params);
    return response.data.products;
  },

  async create(payload) {
    const response = await api.post('/products', payload);
    return response.data.product;
  },

  async update(id, payload) {
    const response = await api.put(`/products/${id}`, payload);
    return response.data.product;
  },

  async remove(id) {
    await api.delete(`/products/${id}`);
  },

  async exportCsv() {
    const response = await api.get('/products/export/csv', { responseType: 'blob' });
    return response.data;
  },

  async importCsv(file) {
    const formData = new FormData();
    formData.append('file', file);
    const response = await api.post('/products/import/csv', formData, {
      headers: { 'Content-Type': 'multipart/form-data' }
    });
    return response.data;
  }
};
