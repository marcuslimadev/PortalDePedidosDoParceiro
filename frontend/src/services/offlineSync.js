/**
 * Offline Sync Service
 * Gerencia sincronização de dados quando offline usando IndexedDB
 */

const DB_NAME = 'portal-pedidos-db';
const DB_VERSION = 1;
const STORES = {
  PENDING_ORDERS: 'pending-orders',
  CACHED_PRODUCTS: 'cached-products',
  CACHED_CLIENTS: 'cached-clients'
};

class OfflineSyncService {
  constructor() {
    this.db = null;
    this.isOnline = navigator.onLine;
    this.syncInProgress = false;
    this.initDB();
    this.setupEventListeners();
  }

  /**
   * Initialize IndexedDB
   */
  async initDB() {
    return new Promise((resolve, reject) => {
      const request = indexedDB.open(DB_NAME, DB_VERSION);

      request.onerror = () => {
        console.error('Falha ao abrir IndexedDB:', request.error);
        reject(request.error);
      };

      request.onsuccess = () => {
        this.db = request.result;
        console.log('IndexedDB inicializado com sucesso');
        resolve(this.db);
      };

      request.onupgradeneeded = (event) => {
        const db = event.target.result;

        // Create object stores if they don't exist
        if (!db.objectStoreNames.contains(STORES.PENDING_ORDERS)) {
          const orderStore = db.createObjectStore(STORES.PENDING_ORDERS, {
            keyPath: 'id',
            autoIncrement: true
          });
          orderStore.createIndex('timestamp', 'timestamp', { unique: false });
          orderStore.createIndex('synced', 'synced', { unique: false });
        }

        if (!db.objectStoreNames.contains(STORES.CACHED_PRODUCTS)) {
          const productStore = db.createObjectStore(STORES.CACHED_PRODUCTS, {
            keyPath: 'id'
          });
          productStore.createIndex('updated_at', 'updated_at', { unique: false });
        }

        if (!db.objectStoreNames.contains(STORES.CACHED_CLIENTS)) {
          const clientStore = db.createObjectStore(STORES.CACHED_CLIENTS, {
            keyPath: 'id'
          });
          clientStore.createIndex('updated_at', 'updated_at', { unique: false });
        }
      };
    });
  }

  /**
   * Setup online/offline event listeners
   */
  setupEventListeners() {
    window.addEventListener('online', () => {
      console.log('Conexão restaurada - sincronizando dados pendentes...');
      this.isOnline = true;
      this.syncPendingData();
    });

    window.addEventListener('offline', () => {
      console.log('Conexão perdida - modo offline ativado');
      this.isOnline = false;
    });
  }

  /**
   * Save order to pending queue when offline
   */
  async savePendingOrder(orderData, token) {
    if (!this.db) await this.initDB();

    return new Promise((resolve, reject) => {
      const transaction = this.db.transaction([STORES.PENDING_ORDERS], 'readwrite');
      const store = transaction.objectStore(STORES.PENDING_ORDERS);

      const order = {
        data: orderData,
        token,
        timestamp: new Date().toISOString(),
        synced: false
      };

      const request = store.add(order);

      request.onsuccess = () => {
        console.log('Pedido salvo para sincronização posterior:', request.result);
        resolve(request.result);
      };

      request.onerror = () => {
        console.error('Erro ao salvar pedido pendente:', request.error);
        reject(request.error);
      };
    });
  }

  /**
   * Get all pending orders
   */
  async getPendingOrders() {
    if (!this.db) await this.initDB();

    return new Promise((resolve, reject) => {
      const transaction = this.db.transaction([STORES.PENDING_ORDERS], 'readonly');
      const store = transaction.objectStore(STORES.PENDING_ORDERS);
      const index = store.index('synced');
      const request = index.getAll(false);

      request.onsuccess = () => {
        resolve(request.result);
      };

      request.onerror = () => {
        reject(request.error);
      };
    });
  }

  /**
   * Remove order from pending queue
   */
  async removePendingOrder(id) {
    if (!this.db) await this.initDB();

    return new Promise((resolve, reject) => {
      const transaction = this.db.transaction([STORES.PENDING_ORDERS], 'readwrite');
      const store = transaction.objectStore(STORES.PENDING_ORDERS);
      const request = store.delete(id);

      request.onsuccess = () => {
        console.log('Pedido removido da fila de sincronização:', id);
        resolve();
      };

      request.onerror = () => {
        reject(request.error);
      };
    });
  }

  /**
   * Sync all pending data to server
   */
  async syncPendingData() {
    if (!this.isOnline || this.syncInProgress) {
      return;
    }

    this.syncInProgress = true;

    try {
      const pendingOrders = await this.getPendingOrders();
      console.log(`Sincronizando ${pendingOrders.length} pedidos pendentes...`);

      for (const order of pendingOrders) {
        try {
          const response = await fetch(`${import.meta.env.VITE_API_URL}/orders`, {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'Authorization': `Bearer ${order.token}`
            },
            body: JSON.stringify(order.data)
          });

          if (response.ok) {
            await this.removePendingOrder(order.id);
            console.log('Pedido sincronizado com sucesso:', order.id);
          } else {
            const error = await response.json();
            console.error('Erro ao sincronizar pedido:', error);
          }
        } catch (error) {
          console.error('Falha ao sincronizar pedido:', error);
        }
      }

      console.log('Sincronização concluída');
    } catch (error) {
      console.error('Erro durante sincronização:', error);
    } finally {
      this.syncInProgress = false;
    }
  }

  /**
   * Cache products for offline access
   */
  async cacheProducts(products) {
    if (!this.db) await this.initDB();

    return new Promise((resolve, reject) => {
      const transaction = this.db.transaction([STORES.CACHED_PRODUCTS], 'readwrite');
      const store = transaction.objectStore(STORES.CACHED_PRODUCTS);

      products.forEach((product) => {
        store.put({
          ...product,
          updated_at: new Date().toISOString()
        });
      });

      transaction.oncomplete = () => {
        console.log(`${products.length} produtos em cache`);
        resolve();
      };

      transaction.onerror = () => {
        reject(transaction.error);
      };
    });
  }

  /**
   * Get cached products
   */
  async getCachedProducts() {
    if (!this.db) await this.initDB();

    return new Promise((resolve, reject) => {
      const transaction = this.db.transaction([STORES.CACHED_PRODUCTS], 'readonly');
      const store = transaction.objectStore(STORES.CACHED_PRODUCTS);
      const request = store.getAll();

      request.onsuccess = () => {
        resolve(request.result);
      };

      request.onerror = () => {
        reject(request.error);
      };
    });
  }

  /**
   * Cache clients for offline access
   */
  async cacheClients(clients) {
    if (!this.db) await this.initDB();

    return new Promise((resolve, reject) => {
      const transaction = this.db.transaction([STORES.CACHED_CLIENTS], 'readwrite');
      const store = transaction.objectStore(STORES.CACHED_CLIENTS);

      clients.forEach((client) => {
        store.put({
          ...client,
          updated_at: new Date().toISOString()
        });
      });

      transaction.oncomplete = () => {
        console.log(`${clients.length} clientes em cache`);
        resolve();
      };

      transaction.onerror = () => {
        reject(transaction.error);
      };
    });
  }

  /**
   * Get cached clients
   */
  async getCachedClients() {
    if (!this.db) await this.initDB();

    return new Promise((resolve, reject) => {
      const transaction = this.db.transaction([STORES.CACHED_CLIENTS], 'readonly');
      const store = transaction.objectStore(STORES.CACHED_CLIENTS);
      const request = store.getAll();

      request.onsuccess = () => {
        resolve(request.result);
      };

      request.onerror = () => {
        reject(request.error);
      };
    });
  }

  /**
   * Clear all cached data
   */
  async clearCache() {
    if (!this.db) await this.initDB();

    const stores = [STORES.CACHED_PRODUCTS, STORES.CACHED_CLIENTS];

    return Promise.all(
      stores.map((storeName) => {
        return new Promise((resolve, reject) => {
          const transaction = this.db.transaction([storeName], 'readwrite');
          const store = transaction.objectStore(storeName);
          const request = store.clear();

          request.onsuccess = () => resolve();
          request.onerror = () => reject(request.error);
        });
      })
    );
  }

  /**
   * Get online status
   */
  getOnlineStatus() {
    return this.isOnline;
  }

  /**
   * Get pending orders count
   */
  async getPendingOrdersCount() {
    const orders = await this.getPendingOrders();
    return orders.length;
  }
}

// Export singleton instance
export default new OfflineSyncService();
