<template>
  <div v-if="!isOnline || pendingCount > 0" class="notification is-warning is-light offline-indicator">
    <div class="level is-mobile">
      <div class="level-left">
        <div class="level-item">
          <span class="icon">
            <i :class="isOnline ? 'fas fa-sync-alt' : 'fas fa-wifi-slash'"></i>
          </span>
          <span class="ml-2">
            <strong v-if="!isOnline">Modo Offline</strong>
            <strong v-else>Sincronizando...</strong>
          </span>
        </div>
      </div>
      <div class="level-right">
        <div class="level-item">
          <span v-if="pendingCount > 0" class="tag is-warning">
            {{ pendingCount }} pedido(s) pendente(s)
          </span>
          <button 
            v-if="isOnline && pendingCount > 0" 
            @click="syncNow" 
            class="button is-small is-warning ml-2"
            :class="{ 'is-loading': syncing }"
          >
            <span class="icon">
              <i class="fas fa-sync-alt"></i>
            </span>
            <span>Sincronizar Agora</span>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import offlineSync from '../services/offlineSync';

export default {
  name: 'OfflineIndicator',
  data() {
    return {
      isOnline: navigator.onLine,
      pendingCount: 0,
      syncing: false,
      checkInterval: null
    };
  },
  mounted() {
    // Listen to online/offline events
    window.addEventListener('online', this.handleOnline);
    window.addEventListener('offline', this.handleOffline);
    
    // Check pending orders count periodically
    this.updatePendingCount();
    this.checkInterval = setInterval(() => {
      this.updatePendingCount();
    }, 5000); // Check every 5 seconds
  },
  beforeUnmount() {
    window.removeEventListener('online', this.handleOnline);
    window.removeEventListener('offline', this.handleOffline);
    
    if (this.checkInterval) {
      clearInterval(this.checkInterval);
    }
  },
  methods: {
    handleOnline() {
      this.isOnline = true;
      this.updatePendingCount();
      // Sync automatically when coming back online
      setTimeout(() => {
        this.syncNow();
      }, 1000);
    },
    handleOffline() {
      this.isOnline = false;
    },
    async updatePendingCount() {
      try {
        this.pendingCount = await offlineSync.getPendingOrdersCount();
      } catch (error) {
        console.error('Erro ao verificar pedidos pendentes:', error);
      }
    },
    async syncNow() {
      if (!this.isOnline || this.syncing) return;
      
      this.syncing = true;
      try {
        await offlineSync.syncPendingData();
        await this.updatePendingCount();
        
        this.$emit('sync-complete');
      } catch (error) {
        console.error('Erro ao sincronizar:', error);
      } finally {
        this.syncing = false;
      }
    }
  }
};
</script>

<style scoped>
.offline-indicator {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  z-index: 1000;
  margin: 0;
  border-radius: 0;
  animation: slideDown 0.3s ease-out;
}

@keyframes slideDown {
  from {
    transform: translateY(-100%);
  }
  to {
    transform: translateY(0);
  }
}

.offline-indicator .level {
  margin-bottom: 0;
}

@media screen and (max-width: 768px) {
  .offline-indicator .level-item span:not(.icon):not(.tag) {
    font-size: 0.875rem;
  }
}
</style>
