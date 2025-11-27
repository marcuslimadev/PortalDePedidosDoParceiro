<template>
  <div class="notification-bell" :class="{ 'is-active': isDropdownOpen }">
    <button 
      class="button is-light" 
      @click="toggleDropdown"
      :class="{ 'has-unread': unreadCount > 0 }"
    >
      <span class="icon">
        <i class="fas fa-bell"></i>
      </span>
      <span v-if="unreadCount > 0" class="badge">{{ unreadCount > 99 ? '99+' : unreadCount }}</span>
    </button>

    <div v-if="isDropdownOpen" class="notification-dropdown">
      <div class="dropdown-header">
        <span class="has-text-weight-semibold">Notificações</span>
        <button 
          v-if="unreadCount > 0" 
          class="button is-small is-text" 
          @click="handleMarkAllAsRead"
        >
          Marcar todas como lidas
        </button>
      </div>

      <div class="dropdown-content">
        <div v-if="loading" class="has-text-centered py-4">
          <span class="icon has-text-info">
            <i class="fas fa-spinner fa-spin"></i>
          </span>
        </div>

        <div v-else-if="notifications.length === 0" class="empty-state">
          <span class="icon has-text-grey">
            <i class="fas fa-bell-slash"></i>
          </span>
          <p class="has-text-grey">Nenhuma notificação</p>
        </div>

        <div v-else class="notification-list">
          <div 
            v-for="notification in notifications" 
            :key="notification.id" 
            class="notification-item"
            :class="{ 'is-unread': !notification.read }"
            @click="handleNotificationClick(notification)"
          >
            <div class="notification-icon">
              <span class="icon" :class="getIconClass(notification.type)">
                <i :class="getIcon(notification.type)"></i>
              </span>
            </div>
            <div class="notification-content">
              <p class="notification-title">{{ notification.title }}</p>
              <p class="notification-message">{{ notification.message }}</p>
              <p class="notification-time">{{ formatTime(notification.created_at) }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div v-if="isDropdownOpen" class="dropdown-overlay" @click="closeDropdown"></div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { notificationService } from '../services/notificationService';

const emit = defineEmits(['notification-click']);

const notifications = ref([]);
const unreadCount = ref(0);
const loading = ref(false);
const isDropdownOpen = ref(false);
let pollInterval = null;

const loadNotifications = async () => {
  loading.value = true;
  try {
    notifications.value = await notificationService.list();
  } catch (error) {
    console.error('Erro ao carregar notificações:', error);
  } finally {
    loading.value = false;
  }
};

const loadUnreadCount = async () => {
  try {
    unreadCount.value = await notificationService.getUnreadCount();
  } catch (error) {
    console.error('Erro ao contar notificações:', error);
  }
};

const toggleDropdown = () => {
  isDropdownOpen.value = !isDropdownOpen.value;
  if (isDropdownOpen.value) {
    loadNotifications();
  }
};

const closeDropdown = () => {
  isDropdownOpen.value = false;
};

const handleNotificationClick = async (notification) => {
  if (!notification.read) {
    try {
      await notificationService.markAsRead(notification.id);
      notification.read = true;
      unreadCount.value = Math.max(0, unreadCount.value - 1);
    } catch (error) {
      console.error('Erro ao marcar notificação como lida:', error);
    }
  }
  emit('notification-click', notification);
  closeDropdown();
};

const handleMarkAllAsRead = async () => {
  try {
    await notificationService.markAllAsRead();
    notifications.value.forEach(n => { n.read = true; });
    unreadCount.value = 0;
  } catch (error) {
    console.error('Erro ao marcar todas como lidas:', error);
  }
};

const getIcon = (type) => {
  const icons = {
    novo_pedido: 'fas fa-shopping-cart',
    status_pedido: 'fas fa-info-circle',
    default: 'fas fa-bell'
  };
  return icons[type] || icons.default;
};

const getIconClass = (type) => {
  const classes = {
    novo_pedido: 'has-text-success',
    status_pedido: 'has-text-info',
    default: 'has-text-grey'
  };
  return classes[type] || classes.default;
};

const formatTime = (dateString) => {
  const date = new Date(dateString);
  const now = new Date();
  const diff = now - date;
  const minutes = Math.floor(diff / 60000);
  const hours = Math.floor(diff / 3600000);
  const days = Math.floor(diff / 86400000);

  if (minutes < 1) return 'Agora mesmo';
  if (minutes < 60) return `${minutes}min atrás`;
  if (hours < 24) return `${hours}h atrás`;
  if (days < 7) return `${days}d atrás`;
  
  return new Intl.DateTimeFormat('pt-BR', { 
    dateStyle: 'short', 
    timeStyle: 'short' 
  }).format(date);
};

onMounted(() => {
  loadUnreadCount();
  // Poll for new notifications every 30 seconds
  pollInterval = setInterval(loadUnreadCount, 30000);
});

onUnmounted(() => {
  if (pollInterval) {
    clearInterval(pollInterval);
  }
});

defineExpose({
  refresh: () => {
    loadUnreadCount();
    if (isDropdownOpen.value) {
      loadNotifications();
    }
  }
});
</script>

<style scoped>
.notification-bell {
  position: relative;
  display: inline-block;
}

.notification-bell .button {
  position: relative;
}

.notification-bell .badge {
  position: absolute;
  top: -5px;
  right: -5px;
  background: #f14668;
  color: white;
  font-size: 10px;
  font-weight: bold;
  padding: 2px 5px;
  border-radius: 10px;
  min-width: 18px;
  text-align: center;
}

.notification-bell .button.has-unread .icon {
  animation: bell-shake 0.5s ease-in-out;
}

@keyframes bell-shake {
  0%, 100% { transform: rotate(0); }
  25% { transform: rotate(10deg); }
  75% { transform: rotate(-10deg); }
}

.notification-dropdown {
  position: absolute;
  top: 100%;
  right: 0;
  width: 360px;
  max-height: 480px;
  background: white;
  border-radius: 8px;
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
  z-index: 1000;
  margin-top: 8px;
  overflow: hidden;
}

.dropdown-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 12px 16px;
  border-bottom: 1px solid #f0f0f0;
  background: #fafafa;
}

.dropdown-content {
  max-height: 400px;
  overflow-y: auto;
}

.empty-state {
  padding: 32px;
  text-align: center;
}

.empty-state .icon {
  font-size: 2rem;
  margin-bottom: 8px;
}

.notification-list {
  padding: 8px 0;
}

.notification-item {
  display: flex;
  padding: 12px 16px;
  cursor: pointer;
  transition: background 0.2s;
}

.notification-item:hover {
  background: #f5f5f5;
}

.notification-item.is-unread {
  background: #f0f7ff;
}

.notification-item.is-unread:hover {
  background: #e0efff;
}

.notification-icon {
  flex-shrink: 0;
  width: 40px;
  height: 40px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #f0f0f0;
  border-radius: 50%;
  margin-right: 12px;
}

.notification-content {
  flex: 1;
  min-width: 0;
}

.notification-title {
  font-weight: 600;
  font-size: 14px;
  margin-bottom: 2px;
  color: #363636;
}

.notification-message {
  font-size: 13px;
  color: #666;
  margin-bottom: 4px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.notification-time {
  font-size: 11px;
  color: #999;
}

.dropdown-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  z-index: 999;
}
</style>
