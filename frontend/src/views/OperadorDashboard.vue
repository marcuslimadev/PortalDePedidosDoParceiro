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
            <NotificationBell @notification-click="handleNotificationClick" ref="notificationBell" />
          </div>
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
        </div>

        <!-- Open Orders Dashboard -->
        <OpenOrdersDashboard 
          ref="openOrdersDashboard"
          @approve="handleApprove"
          @reject="handleReject"
        />

        <div class="columns is-variable is-6 mt-5">
          <div class="column is-7">
            <div class="box">
              <div class="level mb-3">
                <div class="level-left">
                  <div>
                    <p class="heading">Todos os pedidos</p>
                    <p class="title is-5">Histórico recente</p>
                  </div>
                </div>
                <div class="level-right">
                  <button class="button is-info" :class="{ 'is-loading': loading }" @click="loadOrders">
                    <span class="icon"><i class="fas fa-sync-alt"></i></span>
                    <span>Atualizar</span>
                  </button>
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
import { computed, onMounted, reactive, ref } from 'vue';
import { useRouter } from 'vue-router';
import { authService } from '../services/api';
import { orderService } from '../services/orderService';
import NotificationBell from '../components/NotificationBell.vue';
import OpenOrdersDashboard from '../components/OpenOrdersDashboard.vue';

const router = useRouter();
const user = ref(null);
const orders = ref([]);
const loading = ref(false);
const updatingStatus = reactive({});
const targetStatus = reactive({});
const feedback = reactive({ message: '', type: 'is-success' });
const notificationBell = ref(null);
const openOrdersDashboard = ref(null);

onMounted(() => {
  user.value = authService.getUser();
  if (!user.value || user.value.role !== 'operador') {
    router.push('/login');
  }
  loadOrders();
});

const handleLogout = () => {
  authService.logout();
  router.push('/');
};

const loadOrders = async () => {
  loading.value = true;
  try {
    orders.value = await orderService.list();
  } catch (error) {
    console.error('Erro ao carregar pedidos', error);
    feedback.message = error.response?.data?.error || 'Falha ao carregar pedidos';
    feedback.type = 'is-danger';
  } finally {
    loading.value = false;
  }
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
    
    // Refresh notifications and open orders dashboard
    if (notificationBell.value) {
      notificationBell.value.refresh();
    }
    if (openOrdersDashboard.value) {
      openOrdersDashboard.value.updateOrderInList(order.id, status);
    }
  } catch (error) {
    console.error('Erro ao atualizar status', error);
    feedback.message = error.response?.data?.error || 'Não foi possível atualizar o status';
    feedback.type = 'is-danger';
  } finally {
    updatingStatus[order.id] = false;
    targetStatus[order.id] = null;
  }
};

const handleApprove = async (order) => {
  if (openOrdersDashboard.value) {
    openOrdersDashboard.value.setUpdating(order.id, 'aprovado');
  }
  try {
    const updated = await orderService.updateStatus(order.id, 'aprovado');
    const index = orders.value.findIndex(o => o.id === order.id);
    if (index !== -1) {
      orders.value.splice(index, 1, updated);
    }
    if (openOrdersDashboard.value) {
      openOrdersDashboard.value.updateOrderInList(order.id, 'aprovado');
      openOrdersDashboard.value.setFeedback(`Pedido #${order.id} aprovado com sucesso!`, 'is-success');
    }
    if (notificationBell.value) {
      notificationBell.value.refresh();
    }
  } catch (error) {
    console.error('Erro ao aprovar pedido:', error);
    if (openOrdersDashboard.value) {
      openOrdersDashboard.value.setFeedback(error.response?.data?.error || 'Erro ao aprovar pedido', 'is-danger');
    }
  } finally {
    if (openOrdersDashboard.value) {
      openOrdersDashboard.value.clearUpdating(order.id);
    }
  }
};

const handleReject = async (order) => {
  if (openOrdersDashboard.value) {
    openOrdersDashboard.value.setUpdating(order.id, 'cancelado');
  }
  try {
    const updated = await orderService.updateStatus(order.id, 'cancelado');
    const index = orders.value.findIndex(o => o.id === order.id);
    if (index !== -1) {
      orders.value.splice(index, 1, updated);
    }
    if (openOrdersDashboard.value) {
      openOrdersDashboard.value.updateOrderInList(order.id, 'cancelado');
      openOrdersDashboard.value.setFeedback(`Pedido #${order.id} cancelado.`, 'is-warning');
    }
    if (notificationBell.value) {
      notificationBell.value.refresh();
    }
  } catch (error) {
    console.error('Erro ao cancelar pedido:', error);
    if (openOrdersDashboard.value) {
      openOrdersDashboard.value.setFeedback(error.response?.data?.error || 'Erro ao cancelar pedido', 'is-danger');
    }
  } finally {
    if (openOrdersDashboard.value) {
      openOrdersDashboard.value.clearUpdating(order.id);
    }
  }
};

const handleNotificationClick = (notification) => {
  if (notification.order_id) {
    // Scroll to or highlight the order
    console.log('Clicked notification for order:', notification.order_id);
  }
};

const formatDate = (value) => {
  return new Intl.DateTimeFormat('pt-BR', {
    dateStyle: 'short',
    timeStyle: 'short'
  }).format(new Date(value));
};

const statusClass = (status) => {
  if (status === 'aprovado') return 'is-success';
  if (status === 'cancelado') return 'is-danger';
  return 'is-warning';
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

.table td, .table th {
  vertical-align: middle;
}

.buttons .button {
  margin-right: 0.5rem;
}
</style>
