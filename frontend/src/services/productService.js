import api from './api';
import offlineSync from './offlineSync';

export const productService = {
  async list(query = '') {
    try {
      const params = query ? { params: { q: query } } : {};
      const response = await api.get('/products', params);
      const products = response.data.products;
      
      // Cache products for offline access
      await offlineSync.cacheProducts(products);
      
      return products;
    } catch (error) {
      // If offline, return cached products
      if (!navigator.onLine) {
        console.warn('Offline - usando produtos em cache');
        const cachedProducts = await offlineSync.getCachedProducts();
        
        // Apply query filter if needed
        if (query) {
          const queryLower = query.toLowerCase();
          return cachedProducts.filter(p => 
            p.nome.toLowerCase().includes(queryLower) ||
            p.codigo?.toString().includes(queryLower)
          );
        }
        
        return cachedProducts;
      }
      throw error;
    }
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

