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
                    </tr>
                  </tbody>
                </table>
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
import { computed, onMounted, ref } from 'vue';
import { useRouter } from 'vue-router';
import { authService } from '../services/api';
import { orderService } from '../services/orderService';

const router = useRouter();
const user = ref(null);
const orders = ref([]);
const loading = ref(false);

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
  } finally {
    loading.value = false;
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
</style>
