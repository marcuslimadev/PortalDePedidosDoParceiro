<template>
  <div class="open-orders-dashboard">
    <div class="box">
      <div class="level mb-4">
        <div class="level-left">
          <div>
            <p class="heading">Dashboard</p>
            <p class="title is-5">Pedidos em Aberto</p>
          </div>
        </div>
        <div class="level-right">
          <button 
            class="button is-info" 
            :class="{ 'is-loading': loading }" 
            @click="refresh"
          >
            <span class="icon"><i class="fas fa-sync-alt"></i></span>
            <span>Atualizar</span>
          </button>
        </div>
      </div>

      <!-- Stats Cards -->
      <div class="columns is-multiline mb-5" v-if="stats">
        <div class="column is-3-desktop is-6-tablet">
          <div class="stat-card has-background-warning-light">
            <div class="stat-icon has-background-warning">
              <i class="fas fa-clock has-text-white"></i>
            </div>
            <div class="stat-content">
              <p class="stat-value">{{ stats.pendentes }}</p>
              <p class="stat-label">Pendentes</p>
              <p class="stat-detail">R$ {{ formatCurrency(stats.valorPendente) }}</p>
            </div>
          </div>
        </div>
        <div class="column is-3-desktop is-6-tablet">
          <div class="stat-card has-background-success-light">
            <div class="stat-icon has-background-success">
              <i class="fas fa-check has-text-white"></i>
            </div>
            <div class="stat-content">
              <p class="stat-value">{{ stats.aprovados }}</p>
              <p class="stat-label">Aprovados</p>
              <p class="stat-detail">R$ {{ formatCurrency(stats.valorAprovado) }}</p>
            </div>
          </div>
        </div>
        <div class="column is-3-desktop is-6-tablet">
          <div class="stat-card has-background-info-light">
            <div class="stat-icon has-background-info">
              <i class="fas fa-chart-line has-text-white"></i>
            </div>
            <div class="stat-content">
              <p class="stat-value">{{ stats.ultimas24h }}</p>
              <p class="stat-label">Últimas 24h</p>
              <p class="stat-detail">R$ {{ formatCurrency(stats.valor24h) }}</p>
            </div>
          </div>
        </div>
        <div class="column is-3-desktop is-6-tablet">
          <div class="stat-card has-background-light">
            <div class="stat-icon has-background-grey-light">
              <i class="fas fa-shopping-bag has-text-white"></i>
            </div>
            <div class="stat-content">
              <p class="stat-value">{{ stats.total }}</p>
              <p class="stat-label">Total Geral</p>
              <p class="stat-detail">R$ {{ formatCurrency(stats.valorTotal) }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Open Orders Table -->
      <div class="table-container">
        <table class="table is-fullwidth is-hoverable">
          <thead>
            <tr>
              <th>#</th>
              <th>Loja</th>
              <th>Itens</th>
              <th class="has-text-right">Valor</th>
              <th>Condição</th>
              <th>Criado em</th>
              <th>Tempo Aberto</th>
              <th class="has-text-centered">Ações</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="loading">
              <td colspan="8" class="has-text-centered py-5">
                <span class="icon has-text-info"><i class="fas fa-spinner fa-spin"></i></span>
                <span class="ml-2">Carregando pedidos...</span>
              </td>
            </tr>
            <tr v-else-if="openOrders.length === 0">
              <td colspan="8" class="has-text-centered py-5">
                <span class="icon has-text-success"><i class="fas fa-check-circle"></i></span>
                <span class="ml-2 has-text-grey">Nenhum pedido pendente no momento</span>
              </td>
            </tr>
            <tr 
              v-else 
              v-for="order in openOrders" 
              :key="order.id"
              :class="{ 'is-urgent': isUrgent(order) }"
            >
              <td>
                <span class="tag" :class="{ 'is-danger': isUrgent(order), 'is-warning': !isUrgent(order) }">
                  {{ order.id }}
                </span>
              </td>
              <td>
                <p class="has-text-weight-semibold">{{ order.loja_nome }}</p>
              </td>
              <td>
                <div class="items-list">
                  <span 
                    v-for="(item, index) in order.items.slice(0, 3)" 
                    :key="item.product_id"
                    class="tag is-light mr-1 mb-1"
                  >
                    {{ item.quantidade }}x {{ item.codigo }}
                  </span>
                  <span v-if="order.items.length > 3" class="tag is-info is-light">
                    +{{ order.items.length - 3 }}
                  </span>
                </div>
              </td>
              <td class="has-text-right has-text-weight-semibold">
                R$ {{ formatCurrency(order.total) }}
              </td>
              <td>
                <span class="tag is-light">{{ order.payment_terms || '-' }}</span>
              </td>
              <td class="is-size-7">{{ formatDate(order.created_at) }}</td>
              <td>
                <span 
                  class="tag" 
                  :class="getTimeClass(order.created_at)"
                >
                  {{ getTimeOpen(order.created_at) }}
                </span>
              </td>
              <td class="has-text-centered">
                <div class="buttons are-small is-centered">
                  <button
                    class="button is-success"
                    :disabled="updating[order.id]"
                    :class="{ 'is-loading': updating[order.id] === 'aprovado' }"
                    @click="$emit('approve', order)"
                    title="Aprovar pedido"
                  >
                    <span class="icon"><i class="fas fa-check"></i></span>
                  </button>
                  <button
                    class="button is-danger"
                    :disabled="updating[order.id]"
                    :class="{ 'is-loading': updating[order.id] === 'cancelado' }"
                    @click="$emit('reject', order)"
                    title="Cancelar pedido"
                  >
                    <span class="icon"><i class="fas fa-times"></i></span>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Feedback Message -->
      <article v-if="feedback.message" class="message mt-3" :class="feedback.type">
        <div class="message-body">
          {{ feedback.message }}
        </div>
      </article>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted, onUnmounted } from 'vue';
import { orderService } from '../services/orderService';

const emit = defineEmits(['approve', 'reject']);

const openOrders = ref([]);
const stats = ref(null);
const loading = ref(false);
const updating = reactive({});
const feedback = reactive({ message: '', type: 'is-info' });
let refreshInterval = null;

const loadOpenOrders = async () => {
  loading.value = true;
  try {
    openOrders.value = await orderService.listOpen();
  } catch (error) {
    console.error('Erro ao carregar pedidos em aberto:', error);
    feedback.message = 'Erro ao carregar pedidos em aberto';
    feedback.type = 'is-danger';
  } finally {
    loading.value = false;
  }
};

const loadStats = async () => {
  try {
    stats.value = await orderService.getStats();
  } catch (error) {
    console.error('Erro ao carregar estatísticas:', error);
  }
};

const refresh = async () => {
  await Promise.all([loadOpenOrders(), loadStats()]);
};

const formatCurrency = (value) => {
  return Number(value || 0).toFixed(2);
};

const formatDate = (dateString) => {
  return new Intl.DateTimeFormat('pt-BR', {
    dateStyle: 'short',
    timeStyle: 'short'
  }).format(new Date(dateString));
};

const getTimeOpen = (createdAt) => {
  const created = new Date(createdAt);
  const now = new Date();
  const diff = now - created;
  const minutes = Math.floor(diff / 60000);
  const hours = Math.floor(diff / 3600000);
  const days = Math.floor(diff / 86400000);

  if (days > 0) return `${days}d ${hours % 24}h`;
  if (hours > 0) return `${hours}h ${minutes % 60}min`;
  return `${minutes}min`;
};

const getTimeClass = (createdAt) => {
  const created = new Date(createdAt);
  const now = new Date();
  const hours = (now - created) / 3600000;

  if (hours > 24) return 'is-danger';
  if (hours > 12) return 'is-warning';
  return 'is-success is-light';
};

const isUrgent = (order) => {
  const created = new Date(order.created_at);
  const now = new Date();
  const hours = (now - created) / 3600000;
  return hours > 24;
};

const updateOrderInList = (orderId, status) => {
  if (status !== 'pendente') {
    openOrders.value = openOrders.value.filter(o => o.id !== orderId);
    loadStats();
  }
};

const setUpdating = (orderId, status) => {
  updating[orderId] = status;
};

const clearUpdating = (orderId) => {
  delete updating[orderId];
};

const setFeedback = (message, type) => {
  feedback.message = message;
  feedback.type = type;
};

const handleVisibilityChange = () => {
  if (document.visibilityState === 'visible') {
    refresh();
  }
};

onMounted(() => {
  refresh();
  // Auto-refresh every 60 seconds only when page is visible
  refreshInterval = setInterval(() => {
    if (document.visibilityState === 'visible') {
      refresh();
    }
  }, 60000);
  // Also refresh when page becomes visible
  document.addEventListener('visibilitychange', handleVisibilityChange);
});

onUnmounted(() => {
  if (refreshInterval) {
    clearInterval(refreshInterval);
  }
  document.removeEventListener('visibilitychange', handleVisibilityChange);
});

defineExpose({
  refresh,
  updateOrderInList,
  setUpdating,
  clearUpdating,
  setFeedback
});
</script>

<style scoped>
.stat-card {
  display: flex;
  align-items: center;
  padding: 16px;
  border-radius: 10px;
  transition: transform 0.2s, box-shadow 0.2s;
}

.stat-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
}

.stat-icon {
  width: 48px;
  height: 48px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-right: 16px;
}

.stat-icon i {
  font-size: 20px;
}

.stat-content {
  flex: 1;
}

.stat-value {
  font-size: 24px;
  font-weight: 700;
  line-height: 1.2;
  color: #363636;
}

.stat-label {
  font-size: 13px;
  color: #666;
  font-weight: 500;
}

.stat-detail {
  font-size: 12px;
  color: #888;
  margin-top: 2px;
}

.table-container {
  max-height: 500px;
  overflow-y: auto;
}

.table td, .table th {
  vertical-align: middle;
}

.items-list {
  max-width: 200px;
}

tr.is-urgent {
  background-color: #fff5f5;
}

tr.is-urgent:hover {
  background-color: #fee;
}

.buttons .button {
  margin-bottom: 0;
}
</style>
