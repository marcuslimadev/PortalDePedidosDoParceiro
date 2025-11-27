<template>
  <div class="dashboard">
    <nav class="navbar is-success" role="navigation">
      <div class="navbar-brand">
        <span class="navbar-item">
          <strong>Portal de Pedidos - Loja</strong>
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
              <h1 class="title">Pedidos da Loja</h1>
              <p class="has-text-grey-dark">Monte o carrinho a partir do catálogo e acompanhe os últimos pedidos.</p>
            </div>
          </div>
          <div class="level-right">
            <div class="box quick-kpi has-background-success has-text-white">
              <p class="is-size-7">Itens no carrinho</p>
              <p class="title is-3 has-text-white">{{ cart.length }}</p>
              <p class="is-size-7">Total: R$ {{ orderTotal.toFixed(2) }}</p>
            </div>
          </div>
        </div>

        <div class="columns is-variable is-6">
          <div class="column is-7">
            <div class="box">
              <div class="level mb-3">
                <div class="level-left">
                  <div>
                    <p class="heading">Catálogo</p>
                    <p class="title is-5">Produtos disponíveis</p>
                  </div>
                </div>
                <div class="level-right">
                  <div class="field has-addons">
                    <p class="control is-expanded">
                      <input
                        v-model="searchTerm"
                        class="input"
                        type="text"
                        placeholder="Buscar por código ou descrição"
                        @keyup.enter="loadProducts"
                      >
                    </p>
                    <p class="control">
                      <button class="button is-success" @click="loadProducts" :class="{ 'is-loading': loadingProducts }">
                        <span class="icon"><i class="fas fa-search"></i></span>
                      </button>
                    </p>
                  </div>
                </div>
              </div>

              <div class="table-container">
                <table class="table is-fullwidth is-striped">
                  <thead>
                    <tr>
                      <th>Código</th>
                      <th>Descrição</th>
                      <th class="has-text-right">Preço</th>
                      <th class="has-text-centered">Estoque</th>
                      <th class="has-text-centered">Qtd</th>
                      <th></th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-if="loadingProducts">
                      <td colspan="6" class="has-text-centered">
                        <span class="icon has-text-info"><i class="fas fa-spinner fa-spin"></i></span>
                        Carregando catálogo...
                      </td>
                    </tr>
                    <tr v-else-if="!products.length">
                      <td colspan="6" class="has-text-centered has-text-grey">Nenhum produto encontrado.</td>
                    </tr>
                    <tr v-for="product in products" :key="product.id">
                      <td><span class="tag is-light">{{ product.codigo }}</span></td>
                      <td>{{ product.descricao }}</td>
                      <td class="has-text-right">R$ {{ Number(product.preco).toFixed(2) }}</td>
                      <td class="has-text-centered">{{ product.estoque }}</td>
                      <td class="has-text-centered" style="width: 100px;">
                        <input
                          v-model.number="quantitySelections[product.id]"
                          type="number"
                          min="1"
                          class="input is-small"
                        >
                      </td>
                      <td class="has-text-right">
                        <button class="button is-small is-success" @click="addToCart(product)">
                          <span class="icon is-small"><i class="fas fa-cart-plus"></i></span>
                        </button>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          <div class="column is-5">
            <div class="box mb-5">
              <p class="heading">Carrinho</p>
              <p class="title is-5">Revisar itens</p>

              <div v-if="feedback.message" class="notification" :class="feedback.type">
                {{ feedback.message }}
              </div>

              <div v-if="!cart.length" class="has-text-centered has-text-grey">
                Nenhum item no carrinho ainda.
              </div>

              <table v-else class="table is-fullwidth is-striped">
                <thead>
                  <tr>
                    <th>Produto</th>
                    <th class="has-text-centered">Qtd</th>
                    <th class="has-text-right">Subtotal</th>
                    <th></th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="item in cart" :key="item.product_id">
                    <td>
                      <p class="is-size-6">{{ item.codigo }} - {{ item.descricao }}</p>
                      <p class="is-size-7 has-text-grey">R$ {{ Number(item.preco_unitario).toFixed(2) }} / {{ item.unidade }}</p>
                    </td>
                    <td class="has-text-centered">
                      <input
                        v-model.number="item.quantidade"
                        class="input is-small"
                        type="number"
                        min="1"
                        @change="updateCartItem(item)"
                      >
                    </td>
                    <td class="has-text-right">R$ {{ item.subtotal.toFixed(2) }}</td>
                    <td class="has-text-right">
                      <button class="button is-ghost is-small" @click="removeFromCart(item.product_id)">
                        <span class="icon has-text-danger"><i class="fas fa-times"></i></span>
                      </button>
                    </td>
                  </tr>
                </tbody>
                <tfoot>
                  <tr>
                    <th colspan="2" class="has-text-right">Total</th>
                    <th class="has-text-right">R$ {{ orderTotal.toFixed(2) }}</th>
                    <th></th>
                  </tr>
                </tfoot>
              </table>

              <div class="field">
                <label class="label">Condição de pagamento</label>
                <div class="control has-icons-left">
                  <div class="select is-fullwidth">
                    <select v-model="orderForm.paymentTerms">
                      <option value="30">30 dias</option>
                      <option value="30/60">30/60</option>
                      <option value="30/60/90">30/60/90</option>
                      <option value="Antecipado">Antecipado</option>
                    </select>
                  </div>
                  <span class="icon is-small is-left"><i class="fas fa-clock"></i></span>
                </div>
              </div>

              <div class="field">
                <label class="label">Observações do pedido</label>
                <textarea v-model="orderForm.observations" class="textarea" rows="2" placeholder="Instruções de entrega, referência de rota, etc"></textarea>
              </div>

              <div class="buttons is-right">
                <button
                  class="button is-primary"
                  :disabled="!cart.length || saving"
                  :class="{ 'is-loading': saving }"
                  @click="submitOrder"
                >
                  <span class="icon"><i class="fas fa-paper-plane"></i></span>
                  <span>Enviar pedido</span>
                </button>
              </div>
            </div>

            <div class="box">
              <p class="heading">Últimos pedidos</p>
              <p class="title is-5">Histórico recente</p>

              <div v-if="loadingOrders" class="has-text-centered">
                <span class="icon has-text-info"><i class="fas fa-spinner fa-spin"></i></span>
                Carregando...
              </div>

              <div v-else-if="!orders.length" class="has-text-centered has-text-grey">
                Nenhum pedido registrado ainda.
              </div>

              <div v-else>
                <article v-for="order in orders" :key="order.id" class="media order-card">
                  <div class="media-content">
                    <div class="is-flex is-justify-content-space-between is-align-items-center">
                      <div>
                        <p class="is-size-6 has-text-weight-semibold">Pedido #{{ order.id }}</p>
                        <p class="is-size-7 has-text-grey">{{ formatDate(order.created_at) }}</p>
                      </div>
                      <div class="has-text-right">
                        <span class="tag" :class="statusClass(order.status)">{{ order.status }}</span>
                        <p class="has-text-weight-semibold">R$ {{ Number(order.total).toFixed(2) }}</p>
                      </div>
                    </div>
                    <ul class="content is-size-7 mt-2">
                      <li v-for="item in order.items" :key="item.product_id" class="has-text-grey-dark">
                        {{ item.quantidade }}x {{ item.codigo }} - {{ item.descricao }}
                      </li>
                    </ul>
                    <p v-if="order.payment_terms" class="is-size-7 has-text-grey">
                      Pagamento: {{ order.payment_terms }}
                    </p>
                    <div class="has-text-right mt-2">
                      <button
                        class="button is-small is-light"
                        :class="{ 'is-loading': repeatingOrderId === order.id }"
                        @click="repeatExistingOrder(order)"
                      >
                        <span class="icon is-small"><i class="fas fa-redo"></i></span>
                        <span>Repetir pedido</span>
                      </button>
                    </div>
                  </div>
                </article>
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
import { productService } from '../services/productService';
import { orderService } from '../services/orderService';

const router = useRouter();
const user = ref(null);
const products = ref([]);
const loadingProducts = ref(false);
const loadingOrders = ref(false);
const saving = ref(false);
const orders = ref([]);
const repeatingOrderId = ref(null);
const cart = ref([]);
const searchTerm = ref('');
const quantitySelections = reactive({});
const feedback = reactive({ message: '', type: 'is-primary' });
const orderForm = reactive({
  paymentTerms: '30',
  observations: ''
});

const orderTotal = computed(() => cart.value.reduce((sum, item) => sum + item.subtotal, 0));
let orderStream = null;

onMounted(() => {
  user.value = authService.getUser();
  if (!user.value || user.value.role !== 'loja') {
    router.push('/login');
    return;
  }
  loadProducts();
  loadOrders();
  subscribeToOrderStream();
});

onBeforeUnmount(() => {
  if (orderStream) {
    orderStream.close();
    orderStream = null;
  }
});

const handleLogout = () => {
  authService.logout();
  router.push('/');
};

const loadProducts = async () => {
  loadingProducts.value = true;
  try {
    products.value = await productService.list(searchTerm.value);
    products.value.forEach(product => {
      if (!quantitySelections[product.id]) {
        quantitySelections[product.id] = 1;
      }
    });
  } catch (error) {
    feedback.message = error.response?.data?.error || 'Não foi possível carregar o catálogo';
    feedback.type = 'is-danger';
  } finally {
    loadingProducts.value = false;
  }
};

const addToCart = (product) => {
  const quantity = Number(quantitySelections[product.id] || 1);
  if (!quantity || quantity <= 0) return;

  const existing = cart.value.find(item => item.product_id === product.id);

  if (existing) {
    existing.quantidade += quantity;
    existing.subtotal = existing.quantidade * Number(product.preco);
  } else {
    cart.value.push({
      product_id: product.id,
      codigo: product.codigo,
      descricao: product.descricao,
      quantidade: quantity,
      preco_unitario: product.preco,
      unidade: product.unidade,
      subtotal: quantity * Number(product.preco)
    });
  }

  feedback.message = 'Item adicionado ao carrinho';
  feedback.type = 'is-success';
};

const updateCartItem = (item) => {
  if (item.quantidade <= 0) {
    removeFromCart(item.product_id);
    return;
  }
  item.subtotal = item.quantidade * Number(item.preco_unitario);
};

const removeFromCart = (productId) => {
  cart.value = cart.value.filter(item => item.product_id !== productId);
};

const submitOrder = async () => {
  if (!cart.value.length) return;
  saving.value = true;
  feedback.message = '';

  try {
    const orderPayload = {
      paymentTerms: orderForm.paymentTerms,
      observations: orderForm.observations,
      items: cart.value.map(item => ({
        productId: item.product_id,
        quantidade: item.quantidade
      }))
    };

    const created = await orderService.create(orderPayload);
    orders.value = [created, ...orders.value].slice(0, 50);
    cart.value = [];
    orderForm.observations = '';
    feedback.message = `Pedido #${created.id} enviado com sucesso!`;
    feedback.type = 'is-success';
  } catch (error) {
    feedback.message = error.response?.data?.error || 'Não foi possível registrar o pedido';
    feedback.type = 'is-danger';
  } finally {
    saving.value = false;
  }
};

const loadOrders = async () => {
  loadingOrders.value = true;
  try {
    orders.value = await orderService.list({ limit: 100 });
  } catch (error) {
    feedback.message = error.response?.data?.error || 'Falha ao carregar pedidos';
    feedback.type = 'is-danger';
  } finally {
    loadingOrders.value = false;
  }
};

const subscribeToOrderStream = () => {
  const token = localStorage.getItem('token');
  if (!token) return;

  const source = new EventSource(`${API_URL}/orders/stream?token=${token}`);
  orderStream = source;

  const upsertOrder = (payload) => {
    const idx = orders.value.findIndex(order => order.id === payload.id);
    if (idx >= 0) {
      orders.value.splice(idx, 1, {
        ...orders.value[idx],
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
      console.error('Erro ao ler evento SSE', error);
      return null;
    }
  };

  source.addEventListener('order.created', (event) => {
    const payload = parsePayload(event);
    if (!payload) return;
    upsertOrder(payload);
    feedback.message = `Novo pedido #${payload.id} registrado.`;
    feedback.type = 'is-info';
  });

  source.addEventListener('order.status_updated', (event) => {
    const payload = parsePayload(event);
    if (!payload) return;
    upsertOrder(payload);
    feedback.message = `Pedido #${payload.id} agora está ${payload.status}.`;
    feedback.type = 'is-info';
  });

  source.onerror = () => {
    source.close();
    setTimeout(subscribeToOrderStream, 3000);
  };
};

const repeatExistingOrder = async (order) => {
  repeatingOrderId.value = order.id;
  feedback.message = '';
  try {
    const repeated = await orderService.repeat(order.id);
    orders.value = [repeated, ...orders.value].slice(0, 50);
    feedback.message = `Pedido #${repeated.id} criado novamente a partir do pedido #${order.id}`;
    feedback.type = 'is-success';
  } catch (error) {
    feedback.message = error.response?.data?.error || 'Não foi possível repetir o pedido';
    feedback.type = 'is-danger';
  } finally {
    repeatingOrderId.value = null;
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
.quick-kpi {
  min-width: 220px;
  border-radius: 12px;
  box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
}

.navbar {
  margin-bottom: 1.5rem;
}

.table td,
.table th {
  vertical-align: middle;
}

.order-card {
  padding: 12px 0;
  border-bottom: 1px solid #f2f2f2;
}
</style>
