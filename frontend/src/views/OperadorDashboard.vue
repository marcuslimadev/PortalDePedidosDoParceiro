<template>
  <div class="dashboard">
    <nav class="navbar is-info" role="navigation">
      <div class="navbar-brand">
        <span class="navbar-item">
          <strong>Portal de Pedidos - Operador</strong>
        </span>
      </div>
      <div class="navbar-menu">
        <div class="navbar-end">
          <div class="navbar-item">
            <span class="tag is-light">{{ user?.nome }}</span>
          </div>
          <div class="navbar-item">
            <button @click="handleLogout" class="button is-light is-small">
              <span class="icon"><i class="fas fa-sign-out-alt"></i></span>
              <span>Sair</span>
            </button>
          </div>
        </div>
      </div>
    </nav>

    <section class="section">
      <div class="container">
        <div class="level">
          <div class="level-left">
            <div>
              <p class="subtitle is-6 has-text-grey">Bem-vindo, {{ user?.nome }}</p>
              <h1 class="title">Central do Operador</h1>
              <p class="has-text-grey-dark">Acompanhe o volume de pedidos e garanta que catálogo e limites estejam em dia.</p>
            </div>
          </div>
          <div class="level-right">
            <div class="box kpi-box has-background-info has-text-white">
              <p class="is-size-7 has-text-white-bis">Pedidos nas últimas 24h</p>
              <p class="title is-4 has-text-white">{{ recentOrdersCount }}</p>
              <p class="is-size-7">Ticket médio: R$ {{ averageTicket.toFixed(2) }}</p>
            </div>
          </div>
        </div>

        <div class="columns is-multiline mt-2 kpi-grid">
          <div class="column is-3-desktop is-6-tablet">
            <div class="box has-background-info-light">
              <p class="heading">Pedidos totais</p>
              <p class="title is-4">{{ orders.length }}</p>
              <p class="is-size-7">Últimos 50 registros</p>
            </div>
          </div>
          <div class="column is-3-desktop is-6-tablet">
            <div class="box has-background-success-light">
              <p class="heading">Aprovados</p>
              <p class="title is-4">{{ approvedOrders }}</p>
              <p class="is-size-7">Liberados para faturamento</p>
            </div>
          </div>
          <div class="column is-3-desktop is-6-tablet">
            <div class="box has-background-warning-light">
              <p class="heading">Em análise</p>
              <p class="title is-4">{{ pendingOrders }}</p>
              <p class="is-size-7">Aguardando conferência</p>
            </div>
          </div>
          <div class="column is-3-desktop is-6-tablet">
            <div class="box has-background-light">
              <p class="heading">Valor total</p>
              <p class="title is-4">R$ {{ totalOrdersValue.toFixed(2) }}</p>
              <p class="is-size-7">Soma dos pedidos listados</p>
            </div>
          </div>
        </div>

        <div class="box open-orders-box mt-5">
          <div class="level mb-4">
            <div class="level-left">
              <div>
                <p class="heading">Pedidos em aberto</p>
                <p class="title is-5">Dashboard em tempo real</p>
              </div>
            </div>
            <div class="level-right">
              <button class="button is-light is-small" :class="{ 'is-loading': loadingSummary }" @click="loadOpenSummary()">
                <span class="icon"><i class="fas fa-sync-alt"></i></span>
                <span>Atualizar</span>
              </button>
            </div>
          </div>

          <div v-if="loadingSummary && !openSummary" class="has-text-centered py-4">
            <span class="icon has-text-info"><i class="fas fa-spinner fa-spin"></i></span>
            Carregando painel de pedidos...
          </div>
          <div v-else-if="summaryError" class="notification is-danger is-light">
            {{ summaryError }}
          </div>
          <div v-else-if="!openSummary" class="has-text-grey has-text-centered">
            Nenhum pedido pendente no momento.
          </div>
          <div v-else>
            <div class="columns is-multiline summary-grid">
              <div class="column is-4-desktop is-12-tablet">
                <div class="summary-card">
                  <p class="heading">Pedidos pendentes</p>
                  <p class="title is-3">{{ openSummary.summary.totalOpen }}</p>
                  <p class="is-size-7 has-text-grey">Fila aguardando aprovação</p>
                </div>
              </div>
              <div class="column is-4-desktop is-6-tablet">
                <div class="summary-card">
                  <p class="heading">Valor em aberto</p>
                  <p class="title is-4">{{ formatCurrency(openSummary.summary.totalValue) }}</p>
                  <p class="is-size-7 has-text-grey">Somatório dos pendentes</p>
                </div>
              </div>
              <div class="column is-4-desktop is-6-tablet">
                <div class="summary-card">
                  <p class="heading">Mais antigo</p>
                  <p class="title is-4">{{ formatWaiting(openSummary.summary.oldestMinutes) }}</p>
                  <p class="is-size-7 has-text-grey">Tempo em análise</p>
                </div>
              </div>
            </div>

            <div class="status-tags mt-3">
              <span
                v-for="status in openSummary.byStatus"
                :key="status.status"
                class="tag is-medium"
                :class="statusClass(status.status)"
              >
                {{ statusLabel(status.status) }} · {{ status.count }}
              </span>
            </div>

            <div class="columns is-variable is-6 mt-4">
              <div class="column is-5">
                <h4 class="title is-6 mb-3">Aging por faixa</h4>
                <div class="aging-grid">
                  <div v-for="bucket in openSummary.aging" :key="bucket.label" class="aging-card">
                    <p class="heading">{{ bucket.label }}</p>
                    <p class="title is-5">{{ bucket.count }}</p>
                    <p class="is-size-7 has-text-grey">{{ formatCurrency(bucket.totalValue) }}</p>
                  </div>
                </div>
              </div>
              <div class="column is-7">
                <h4 class="title is-6 mb-3">Fila de pendentes</h4>
                <div class="table-container queue-table">
                  <table class="table is-fullwidth is-hoverable">
                    <thead>
                      <tr>
                        <th>#</th>
                        <th>Loja</th>
                        <th class="has-text-right">Valor</th>
                        <th>Espera</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr v-if="!openSummary.queue.length">
                        <td colspan="4" class="has-text-centered has-text-grey">Nenhum pedido pendente.</td>
                      </tr>
                      <tr v-for="item in openSummary.queue" :key="item.id">
                        <td><span class="tag is-light">{{ item.id }}</span></td>
                        <td>
                          <p class="has-text-weight-semibold">{{ item.loja_nome }}</p>
                          <p class="is-size-7 has-text-grey">{{ statusLabel(item.status) }}</p>
                        </td>
                        <td class="has-text-right">{{ formatCurrency(item.total) }}</td>
                        <td>{{ formatWaiting(item.waitingMinutes) }}</td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>

            <p class="is-size-7 has-text-grey mt-3">Atualizado em {{ formatDate(openSummary.updatedAt) }}</p>
          </div>
        </div>

        <div class="columns is-variable is-6">
          <div class="column is-7">
            <div class="box">
              <div class="level mb-3">
                <div class="level-left">
                  <div>
                    <p class="heading">Monitoramento de pedidos</p>
                    <p class="title is-5">Histórico recente</p>
                  </div>
                </div>
                <div class="level-right">
                  <div class="buttons">
                    <button class="button is-light" :class="{ 'is-loading': downloadingExport }" @click="downloadOrdersCsv">
                      <span class="icon"><i class="fas fa-file-download"></i></span>
                      <span>Exportar CSV</span>
                    </button>
                    <button class="button is-info" :class="{ 'is-loading': loading }" @click="loadOrders">
                      <span class="icon"><i class="fas fa-sync-alt"></i></span>
                      <span>Atualizar</span>
                    </button>
                  </div>
                </div>
              </div>

              <div v-if="loading" class="has-text-centered py-5">
                <span class="icon has-text-info"><i class="fas fa-spinner fa-spin"></i></span>
                Carregando pedidos...
              </div>

              <div v-else-if="!orders.length" class="has-text-centered has-text-grey">Nenhum pedido encontrado.</div>

              <div v-else>
                <table class="table is-fullwidth is-striped">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>Loja</th>
                      <th>Itens</th>
                      <th class="has-text-right">Valor</th>
                      <th>Status</th>
                      <th>Data</th>
                      <th class="has-text-centered">Ações</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="order in orders" :key="order.id">
                      <td><span class="tag is-light">{{ order.id }}</span></td>
                      <td>
                        <p class="has-text-weight-semibold">{{ order.loja_nome }}</p>
                        <p class="is-size-7 has-text-grey">Pagamento: {{ order.payment_terms || 'Pendente' }}</p>
                      </td>
                      <td>
                        <ul class="is-size-7 has-text-grey-dark">
                          <li v-for="item in order.items" :key="item.product_id">{{ item.quantidade }}x {{ item.codigo }}</li>
                        </ul>
                      </td>
                      <td class="has-text-right">R$ {{ Number(order.total).toFixed(2) }}</td>
                      <td>
                        <span class="tag" :class="statusClass(order.status)">{{ order.status }}</span>
                      </td>
                      <td class="is-size-7">{{ formatDate(order.created_at) }}</td>
                      <td class="has-text-centered">
                        <div class="buttons are-small is-centered">
                          <button
                            class="button is-success is-light"
                            :disabled="order.status === 'aprovado' || isUpdating(order.id)"
                            :class="{ 'is-loading': isUpdating(order.id) && targetStatus[order.id] === 'aprovado' }"
                            @click="updateStatus(order, 'aprovado')"
                          >
                            <span class="icon"><i class="fas fa-check"></i></span>
                          </button>
                          <button
                            class="button is-danger is-light"
                            :disabled="order.status === 'cancelado' || isUpdating(order.id)"
                            :class="{ 'is-loading': isUpdating(order.id) && targetStatus[order.id] === 'cancelado' }"
                            @click="updateStatus(order, 'cancelado')"
                          >
                            <span class="icon"><i class="fas fa-times"></i></span>
                          </button>
                        </div>
                      </td>
                    </tr>
                  </tbody>
                </table>
                <article v-if="feedback.message" class="message mt-3" :class="feedback.type">
                  <div class="message-body">
                    {{ feedback.message }}
                  </div>
                </article>
              </div>
            </div>
          </div>

          <div class="column is-5">
            <div class="box">
              <p class="heading">Rotinas operacionais</p>
              <p class="title is-5">Ações rápidas</p>

              <div class="buttons">
                <button class="button is-info is-outlined">
                  <span class="icon"><i class="fas fa-tags"></i></span>
                  <span>Atualizar catálogo</span>
                </button>
                <button class="button is-warning is-outlined">
                  <span class="icon"><i class="fas fa-credit-card"></i></span>
                  <span>Limites e prazos</span>
                </button>
                <button class="button is-success is-outlined">
                  <span class="icon"><i class="fas fa-sync"></i></span>
                  <span>Sincronizar Winthor</span>
                </button>
              </div>

              <div class="content is-size-7 has-text-grey-dark">
                <p class="mb-2"><strong>Checklist rápido</strong></p>
                <ul>
                  <li>Valide preços alterados pelo time de Admin.</li>
                  <li>Reveja pedidos acima do limite e sinalize o financeiro.</li>
                  <li>Confirme integrações com o ERP antes de liberar faturamento.</li>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup>
import { computed, onMounted, onBeforeUnmount, reactive, ref } from 'vue';
import { useRouter } from 'vue-router';
import { authService, API_URL } from '../services/api';
import { orderService } from '../services/orderService';

const router = useRouter();
const user = ref(null);
const orders = ref([]);
const loading = ref(false);
const downloadingExport = ref(false);
const updatingStatus = reactive({});
const targetStatus = reactive({});
const feedback = reactive({ message: '', type: 'is-success' });
const openSummary = ref(null);
const loadingSummary = ref(false);
const summaryError = ref('');
let orderStream = null;
let summaryRefreshTimeout = null;

onMounted(() => {
  user.value = authService.getUser();
  if (!user.value || user.value.role !== 'operador') {
    router.push('/login');
    return;
  }
  loadOrders();
  loadOpenSummary();
  subscribeToOrderStream();
});

onBeforeUnmount(() => {
  if (orderStream) {
    orderStream.close();
    orderStream = null;
  }
  if (summaryRefreshTimeout) {
    clearTimeout(summaryRefreshTimeout);
    summaryRefreshTimeout = null;
  }
});

const handleLogout = () => {
  authService.logout();
  router.push('/');
};

const loadOrders = async () => {
  loading.value = true;
  try {
    orders.value = await orderService.list({ limit: 200 });
  } catch (error) {
    console.error('Erro ao carregar pedidos', error);
    feedback.message = error.response?.data?.error || 'Falha ao carregar pedidos';
    feedback.type = 'is-danger';
  } finally {
    loading.value = false;
  }
};

const loadOpenSummary = async (silent = false) => {
  if (!silent) {
    loadingSummary.value = true;
    summaryError.value = '';
  }

  try {
    openSummary.value = await orderService.openSummary();
  } catch (error) {
    if (!silent) {
      summaryError.value = error.response?.data?.error || 'Não foi possível carregar o dashboard de pedidos';
    } else {
      console.error('Falha ao atualizar painel de pedidos em aberto', error);
    }
  } finally {
    if (!silent) {
      loadingSummary.value = false;
    }
  }
};

const scheduleSummaryRefresh = () => {
  if (summaryRefreshTimeout) return;
  summaryRefreshTimeout = setTimeout(() => {
    loadOpenSummary(true);
    summaryRefreshTimeout = null;
  }, 1200);
};

const isUpdating = (orderId) => updatingStatus[orderId] === true;

const updateStatus = async (order, status) => {
  updatingStatus[order.id] = true;
  targetStatus[order.id] = status;
  feedback.message = '';

  try {
    const updated = await orderService.updateStatus(order.id, status);
    const index = orders.value.findIndex(o => o.id === order.id);
    if (index !== -1) {
      orders.value.splice(index, 1, updated);
    }
    feedback.message = status === 'aprovado'
      ? `Pedido #${order.id} aprovado.`
      : `Pedido #${order.id} cancelado.`;
    feedback.type = status === 'aprovado' ? 'is-success' : 'is-danger';
    scheduleSummaryRefresh();
  } catch (error) {
    console.error('Erro ao atualizar status', error);
    feedback.message = error.response?.data?.error || 'Não foi possível atualizar o status';
    feedback.type = 'is-danger';
  } finally {
    updatingStatus[order.id] = false;
    targetStatus[order.id] = null;
  }
};

const approvedOrders = computed(() => orders.value.filter(order => order.status === 'aprovado').length);
const pendingOrders = computed(() => orders.value.filter(order => order.status === 'em_analise' || order.status === 'pendente').length);
const totalOrdersValue = computed(() => orders.value.reduce((sum, order) => sum + Number(order.total || 0), 0));
const recentOrdersCount = computed(() => {
  const now = Date.now();
  const dayMs = 24 * 60 * 60 * 1000;
  return orders.value.filter(order => now - new Date(order.created_at).getTime() <= dayMs).length;
});
const averageTicket = computed(() => {
  if (!orders.value.length) return 0;
  return totalOrdersValue.value / orders.value.length;
});

const formatDate = (value) => {
  return new Intl.DateTimeFormat('pt-BR', {
    dateStyle: 'short',
    timeStyle: 'short'
  }).format(new Date(value));
};

const formatCurrency = (value) => {
  return new Intl.NumberFormat('pt-BR', {
    style: 'currency',
    currency: 'BRL'
  }).format(Number(value || 0));
};

const formatWaiting = (minutes) => {
  if (minutes === null || minutes === undefined) return '—';
  if (minutes < 60) return `${minutes} min`;
  const hours = Math.floor(minutes / 60);
  const remainingMinutes = minutes % 60;
  if (hours >= 24) {
    const days = Math.floor(hours / 24);
    const restHours = hours % 24;
    if (restHours === 0) {
      return `${days}d`;
    }
    return `${days}d ${restHours}h`;
  }
  if (remainingMinutes === 0) {
    return `${hours}h`;
  }
  return `${hours}h ${remainingMinutes}m`;
};

const statusClass = (status) => {
  if (status === 'aprovado') return 'is-success';
  if (status === 'cancelado') return 'is-danger';
  return 'is-warning';
};

const statusLabel = (status) => {
  const labels = {
    pendente: 'Em análise',
    aprovado: 'Aprovado',
    cancelado: 'Cancelado'
  };
  return labels[status] || status;
};

const subscribeToOrderStream = () => {
  const token = localStorage.getItem('token');
  if (!token) return;

  const source = new EventSource(`${API_URL}/orders/stream?token=${token}`);
  orderStream = source;

  const upsertOrder = (payload) => {
    const index = orders.value.findIndex(order => order.id === payload.id);
    if (index >= 0) {
      orders.value.splice(index, 1, {
        ...orders.value[index],
        ...payload
      });
    } else {
      orders.value = [payload, ...orders.value].slice(0, 50);
    }
  };

  const parsePayload = (event) => {
    try {
      return JSON.parse(event.data);
    } catch (error) {
      console.error('Falha ao interpretar evento SSE', error);
      return null;
    }
  };

  source.addEventListener('order.created', (event) => {
    const payload = parsePayload(event);
    if (!payload) return;
    upsertOrder(payload);
    feedback.message = `Novo pedido #${payload.id} criado pela loja ${payload.loja_nome || ''}`.trim();
    feedback.type = 'is-info';
    scheduleSummaryRefresh();
  });

  source.addEventListener('order.status_updated', (event) => {
    const payload = parsePayload(event);
    if (!payload) return;
    upsertOrder(payload);
    feedback.message = `Pedido #${payload.id} atualizado para ${payload.status}.`;
    feedback.type = 'is-info';
    scheduleSummaryRefresh();
  });

  source.onerror = () => {
    source.close();
    setTimeout(subscribeToOrderStream, 3000);
  };
};

const triggerDownload = (blob, filename) => {
  const url = window.URL.createObjectURL(blob);
  const link = document.createElement('a');
  link.href = url;
  link.setAttribute('download', filename);
  document.body.appendChild(link);
  link.click();
  document.body.removeChild(link);
  window.URL.revokeObjectURL(url);
};

const downloadOrdersCsv = async () => {
  downloadingExport.value = true;
  feedback.message = '';
  try {
    const blob = await orderService.exportCsv();
    const filename = `pedidos-${new Date().toISOString().split('T')[0]}.csv`;
    triggerDownload(blob, filename);
    feedback.message = 'Exportação de pedidos concluída com sucesso';
    feedback.type = 'is-success';
  } catch (error) {
    feedback.message = error.response?.data?.error || 'Não foi possível exportar os pedidos';
    feedback.type = 'is-danger';
  } finally {
    downloadingExport.value = false;
  }
};
</script>

<style scoped>
.dashboard {
  min-height: 100vh;
  background: #f7f9fb;
}

.navbar {
  border-bottom: 1px solid rgba(0,0,0,0.05);
}

.section {
  padding: 2.5rem 1.5rem;
}

.box {
  border-radius: 10px;
  box-shadow: 0 12px 30px rgba(0, 0, 0, 0.06);
}

.kpi-box {
  min-width: 240px;
}

.kpi-grid .box {
  border-left: 4px solid transparent;
}

.kpi-grid .box:hover {
  border-left-color: #0ea5e9;
  transition: all 0.2s ease;
}

.table td, .table th {
  vertical-align: middle;
}

.buttons .button {
  margin-right: 0.5rem;
}

.open-orders-box {
  border: none;
  box-shadow: 0 20px 40px rgba(14, 165, 233, 0.08);
}

.summary-card {
  background: #f8fafc;
  border-radius: 12px;
  padding: 1.5rem;
  height: 100%;
  box-shadow: inset 0 0 0 1px rgba(14, 165, 233, 0.1);
}

.status-tags .tag {
  margin-right: 0.5rem;
  margin-bottom: 0.5rem;
}

.aging-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
  gap: 0.75rem;
}

.aging-card {
  border: 1px dashed rgba(14, 165, 233, 0.25);
  border-radius: 10px;
  padding: 0.75rem;
  background: #f0f9ff;
}

.queue-table {
  max-height: 280px;
  overflow-y: auto;
}
</style>
