<template>
  <div class="dashboard">
    <nav class="navbar is-primary" role="navigation">
      <div class="navbar-brand">
        <span class="navbar-item">
          <strong>Portal de Pedidos - Admin</strong>
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
        <div class="columns is-vcentered mb-5">
          <div class="column">
            <p class="subtitle is-6 has-text-grey">Ola, {{ user?.nome }}!</p>
            <h1 class="title is-3">Central de administracao</h1>
            <p class="has-text-grey-dark">Cadastre, edite e acompanhe o catalogo disponivel para as lojas parceiras.</p>
          </div>
          <div class="column is-narrow">
            <div class="box kpi-box has-background-primary has-text-white">
              <p class="is-size-7 has-text-white-bis">Catalogo atualizado</p>
              <p class="title is-4 has-text-white">{{ products.length }} produtos</p>
              <p class="is-size-7">Filtro ativo: "{{ searchTerm || 'Nenhum' }}"</p>
            </div>
          </div>
        </div>

        <div class="columns is-multiline kpi-grid">
          <div class="column is-3-desktop is-6-tablet">
            <div class="box has-background-primary-light">
              <p class="heading">Catalogo</p>
              <p class="title is-4">{{ products.length }}</p>
              <p class="is-size-7">Produtos disponiveis</p>
            </div>
          </div>
          <div class="column is-3-desktop is-6-tablet">
            <div class="box has-background-info-light">
              <p class="heading">Fluxo</p>
              <p class="title is-4">Cadastro + Edicao</p>
              <p class="is-size-7">Validado com campos Winthor</p>
            </div>
          </div>
          <div class="column is-3-desktop is-6-tablet">
            <div class="box has-background-success-light">
              <p class="heading">Equipe</p>
              <p class="title is-4">Admins e Operadores</p>
              <p class="is-size-7">Permissoes aplicadas na API</p>
            </div>
          </div>
          <div class="column is-3-desktop is-6-tablet">
            <div class="box has-background-warning-light">
              <p class="heading">Status</p>
              <p class="title is-4">Produtos vivos</p>
              <p class="is-size-7">Listagem com busca por codigo/descricao</p>
            </div>
          </div>
        </div>

        <div class="box">
          <div class="level mb-4">
            <div class="level-left">
              <div>
                <p class="heading">Catalogo de produtos</p>
                <p class="title is-5">Cadastro e edicao</p>
              </div>
            </div>
            <div class="level-right">
              <div class="field has-addons">
                <p class="control is-expanded">
                  <input
                    v-model="searchTerm"
                    class="input"
                    type="text"
                    placeholder="Buscar por codigo ou descricao"
                    @keyup.enter="handleSearch"
                  >
                </p>
                <p class="control">
                  <button class="button is-primary" @click="handleSearch" :disabled="loadingProducts">
                    <span class="icon"><i class="fas fa-search"></i></span>
                    <span>Buscar</span>
                  </button>
                </p>
              </div>
            </div>
          </div>

          <div v-if="feedback.message" class="notification" :class="feedback.type">
            {{ feedback.message }}
          </div>

          <form @submit.prevent="handleSubmit" class="mb-5">
            <div class="columns is-multiline">
              <div class="column is-3">
                <label class="label">Codigo</label>
                <input v-model="productForm.codigo" class="input" placeholder="WIN123" required>
              </div>
              <div class="column is-5">
                <label class="label">Descricao</label>
                <input v-model="productForm.descricao" class="input" placeholder="Produto conforme Winthor" required>
              </div>
              <div class="column is-2">
                <label class="label">Preco (R$)</label>
                <input v-model.number="productForm.preco" class="input" type="number" min="0" step="0.01" required>
              </div>
              <div class="column is-2">
                <label class="label">Unidade</label>
                <input v-model="productForm.unidade" class="input" placeholder="UN/PC/CX" required>
              </div>
              <div class="column is-3">
                <label class="label">Tributacao</label>
                <input v-model="productForm.tributacao" class="input" placeholder="ICMS/IPI" required>
              </div>
              <div class="column is-2">
                <label class="label">Estoque</label>
                <input v-model.number="productForm.estoque" class="input" type="number" min="0" step="1">
              </div>
              <div class="column is-3">
                <label class="label">Categoria</label>
                <input v-model="productForm.categoria" class="input" placeholder="Bebidas, Higiene...">
              </div>
            </div>

            <div class="buttons">
              <button type="submit" class="button is-primary" :class="{ 'is-loading': saving }">
                <span class="icon"><i class="fas" :class="isEditing ? 'fa-save' : 'fa-plus-circle'"></i></span>
                <span>{{ isEditing ? 'Salvar alteracoes' : 'Cadastrar produto' }}</span>
              </button>
              <button type="button" class="button is-light" @click="resetForm" :disabled="saving">
                Limpar
              </button>
            </div>
          </form>

          <div class="table-container">
            <table class="table is-fullwidth is-striped">
              <thead>
                <tr>
                  <th>Codigo</th>
                  <th>Descricao</th>
                  <th>Preco</th>
                  <th>Unidade</th>
                  <th>Tributacao</th>
                  <th>Estoque</th>
                  <th>Categoria</th>
                  <th class="has-text-centered">Acoes</th>
                </tr>
              </thead>
              <tbody>
                <tr v-if="!products.length && !loadingProducts">
                  <td colspan="8" class="has-text-centered has-text-grey">Nenhum produto cadastrado ainda.</td>
                </tr>
                <tr v-if="loadingProducts">
                  <td colspan="8" class="has-text-centered">
                    <span class="icon has-text-info"><i class="fas fa-spinner fa-spin"></i></span>
                    Carregando catalogo...
                  </td>
                </tr>
                <tr v-for="product in products" :key="product.id">
                  <td><span class="tag is-light is-uppercase">{{ product.codigo }}</span></td>
                  <td>{{ product.descricao }}</td>
                  <td>R$ {{ Number(product.preco).toFixed(2) }}</td>
                  <td>{{ product.unidade }}</td>
                  <td>{{ product.tributacao }}</td>
                  <td>{{ product.estoque }}</td>
                  <td>{{ product.categoria || '-' }}</td>
                  <td class="has-text-centered">
                    <div class="buttons is-centered">
                      <button class="button is-small is-info" @click="startEdit(product)">
                        <span class="icon is-small"><i class="fas fa-edit"></i></span>
                      </button>
                      <button
                        class="button is-small is-danger"
                        @click="handleDelete(product)"
                        :disabled="saving"
                      >
                        <span class="icon is-small"><i class="fas fa-trash"></i></span>
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <div class="box mt-6">
          <div class="level mb-4">
            <div class="level-left">
              <div>
                <p class="heading">Gestao de clientes</p>
                <p class="title is-5">Limites e status operacionais</p>
              </div>
            </div>
            <div class="level-right">
              <div class="buttons">
                <button class="button is-light" :class="{ 'is-loading': downloadingOrdersCsv }" @click="exportOrdersCsv">
                  <span class="icon"><i class="fas fa-file-download"></i></span>
                  <span>Exportar pedidos (CSV)</span>
                </button>
                <button class="button is-primary" :class="{ 'is-loading': loadingClients }" @click="loadClients">
                  <span class="icon"><i class="fas fa-sync-alt"></i></span>
                  <span>Atualizar clientes</span>
                </button>
              </div>
            </div>
          </div>

          <div class="columns is-variable is-6">
            <div class="column is-7">
              <div v-if="clientFeedback.message" class="notification" :class="clientFeedback.type">
                {{ clientFeedback.message }}
              </div>
              <div class="table-container clients-table">
                <table class="table is-fullwidth is-hoverable">
                  <thead>
                    <tr>
                      <th>Loja</th>
                      <th>Contato</th>
                      <th class="has-text-right">Limite</th>
                      <th class="has-text-right">Utilizado</th>
                      <th>Status</th>
                      <th>Prazos</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-if="loadingClients">
                      <td colspan="6" class="has-text-centered">
                        <span class="icon has-text-primary"><i class="fas fa-spinner fa-spin"></i></span>
                        Carregando clientes...
                      </td>
                    </tr>
                    <tr v-else-if="!clients.length">
                      <td colspan="6" class="has-text-centered has-text-grey">Nenhuma loja cadastrada.</td>
                    </tr>
                    <tr
                      v-for="client in clients"
                      :key="client.id"
                      :class="{ 'is-selected-row': clientForm.id === client.id }"
                      @click="selectClient(client)"
                    >
                      <td>
                        <p class="has-text-weight-semibold">{{ client.nome }}</p>
                        <p class="is-size-7 has-text-grey">CNPJ: {{ client.cnpj || '-' }}</p>
                      </td>
                      <td>
                        <p>{{ client.email }}</p>
                        <p class="is-size-7 has-text-grey">Rota: {{ client.rota || '-' }}</p>
                      </td>
                      <td class="has-text-right">{{ formatCurrency(client.credit_limit) }}</td>
                      <td class="has-text-right">{{ formatCurrency(client.credit_used) }}</td>
                      <td>
                        <span class="tag" :class="statusTagClass(client.cliente_status)">
                          {{ client.cliente_status || 'indefinido' }}
                        </span>
                      </td>
                      <td>{{ client.payment_terms || 'Padrao' }}</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>

            <div class="column is-5">
              <form @submit.prevent="handleClientSave" class="client-form">
                <p class="heading">Editar cliente selecionado</p>
                <h3 class="title is-6">{{ clientForm.nome || 'Selecione uma loja' }}</h3>

                <div class="columns is-multiline">
                  <div class="column is-12">
                    <label class="label">Email</label>
                    <input class="input" :value="clientForm.email" disabled>
                  </div>
                  <div class="column is-6">
                    <label class="label">CNPJ</label>
                    <input v-model="clientForm.cnpj" class="input" placeholder="00.000.000/0000-00">
                  </div>
                  <div class="column is-6">
                    <label class="label">Inscricao Estadual</label>
                    <input v-model="clientForm.inscricao_estadual" class="input" placeholder="ISENTO">
                  </div>
                  <div class="column is-6">
                    <label class="label">Rota</label>
                    <input v-model="clientForm.rota" class="input" placeholder="Interior / Capital">
                  </div>
                  <div class="column is-6">
                    <label class="label">Segmentacao</label>
                    <input v-model="clientForm.segmentacao" class="input" placeholder="Varejo / Food Service">
                  </div>
                  <div class="column is-6">
                    <label class="label">Limite de credito (R$)</label>
                    <input v-model.number="clientForm.credit_limit" class="input" type="number" min="0" step="100">
                  </div>
                  <div class="column is-6">
                    <label class="label">Credito utilizado</label>
                    <input class="input" :value="formatCurrency(clientForm.credit_used)" disabled>
                  </div>
                  <div class="column is-6">
                    <label class="label">Condicao de pagamento</label>
                    <div class="select is-fullwidth">
                      <select v-model="clientForm.payment_terms">
                        <option value="">Padrao</option>
                        <option v-for="option in paymentOptions" :key="option" :value="option">
                          {{ option }}
                        </option>
                      </select>
                    </div>
                  </div>
                  <div class="column is-6">
                    <label class="label">Status</label>
                    <div class="select is-fullwidth">
                      <select v-model="clientForm.cliente_status">
                        <option v-for="option in statusOptions" :key="option" :value="option">
                          {{ option }}
                        </option>
                      </select>
                    </div>
                  </div>
                </div>

                <div class="buttons">
                  <button type="submit" class="button is-primary" :class="{ 'is-loading': clientSaving }" :disabled="!clientForm.id">
                    <span class="icon"><i class="fas fa-save"></i></span>
                    <span>Salvar cliente</span>
                  </button>
                  <button
                    type="button"
                    class="button is-warning is-light"
                    :disabled="!clientForm.id || clientSaving"
                    @click="handleResetAccess"
                  >
                    <span class="icon"><i class="fas fa-key"></i></span>
                    <span>Gerar acesso portal</span>
                  </button>
                  <button type="button" class="button is-light" @click="resetClientForm" :disabled="clientSaving">
                    Limpar selecao
                  </button>
                </div>
              </form>

              <div class="box history-box mt-4">
                <p class="heading">Historico de alteracoes</p>
                <h3 class="title is-6">{{ clientForm.nome || 'Selecione um cliente' }}</h3>

                <div v-if="!clientForm.id" class="has-text-grey is-size-7">
                  Escolha uma loja para visualizar o historico de limite, status e prazos.
                </div>

                <div v-else>
                  <div v-if="loadingHistory" class="has-text-centered py-4">
                    <span class="icon has-text-primary"><i class="fas fa-spinner fa-spin"></i></span>
                    Carregando historico...
                  </div>

                  <div v-else-if="historyError" class="notification is-danger is-light">
                    {{ historyError }}
                  </div>

                  <div v-else-if="!clientHistory.length" class="has-text-grey is-size-7">
                    Nenhuma alteracao registrada ainda.
                  </div>

                  <ul v-else class="history-timeline">
                    <li v-for="entry in clientHistory" :key="entry.id" class="history-item">
                      <p class="has-text-weight-semibold is-size-7">
                        {{ formatDateTime(entry.created_at) }} - {{ entry.autor || 'Sistema' }}
                      </p>
                      <p v-if="entry.previous_credit_limit !== entry.new_credit_limit" class="is-size-7">
                        Limite: {{ formatCurrency(entry.previous_credit_limit) }} -> {{ formatCurrency(entry.new_credit_limit) }}
                      </p>
                      <p v-if="entry.previous_payment_terms !== entry.new_payment_terms" class="is-size-7">
                        Prazo: {{ entry.previous_payment_terms || 'Padrao' }} -> {{ entry.new_payment_terms || 'Padrao' }}
                      </p>
                      <p v-if="entry.previous_status !== entry.new_status" class="is-size-7">
                        Status: {{ entry.previous_status || 'indefinido' }} -> {{ entry.new_status || 'indefinido' }}
                      </p>
                    </li>
                  </ul>
                </div>
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
import { productService } from '../services/productService';
import { clientService } from '../services/clientService';
import { orderService } from '../services/orderService';

const router = useRouter();
const user = ref(null);

// Produtos
const products = ref([]);
const loadingProducts = ref(false);
const saving = ref(false);
const searchTerm = ref('');
const feedback = reactive({ message: '', type: 'is-primary' });
const productForm = reactive({
  id: null,
  codigo: '',
  descricao: '',
  preco: 0,
  unidade: 'UN',
  tributacao: '',
  estoque: 0,
  categoria: ''
});
const isEditing = computed(() => productForm.id !== null);

// Clientes
const clients = ref([]);
const loadingClients = ref(false);
const clientSaving = ref(false);
const downloadingOrdersCsv = ref(false);
const clientFeedback = reactive({ message: '', type: 'is-info' });
const clientHistory = ref([]);
const loadingHistory = ref(false);
const historyError = ref('');
const clientForm = reactive({
  id: null,
  nome: '',
  email: '',
  cnpj: '',
  inscricao_estadual: '',
  rota: '',
  segmentacao: '',
  credit_limit: null,
  credit_used: 0,
  payment_terms: '',
  cliente_status: 'ativo'
});
const statusOptions = ['ativo', 'inativo', 'bloqueado'];
const paymentOptions = ['30 dias', '45 dias', '60 dias', '90 dias'];
const hasClientSelected = computed(() => clientForm.id !== null);

onMounted(() => {
  user.value = authService.getUser();
  if (!user.value || user.value.role !== 'admin') {
    router.push('/login');
    return;
  }
  loadProducts();
  loadClients();
});

const handleLogout = () => {
  authService.logout();
  router.push('/');
};

const loadProducts = async () => {
  loadingProducts.value = true;
  try {
    products.value = await productService.list(searchTerm.value);
  } catch (error) {
    feedback.message = error.response?.data?.error || 'Nao foi possivel carregar os produtos';
    feedback.type = 'is-danger';
  } finally {
    loadingProducts.value = false;
  }
};

const loadClients = async () => {
  loadingClients.value = true;
  clientFeedback.message = '';
  try {
    const result = await clientService.list();
    clients.value = result;

    if (clientForm.id) {
      const updated = result.find(item => item.id === clientForm.id);
      if (updated) {
        selectClient(updated);
      }
    }
  } catch (error) {
    clientFeedback.message = error.response?.data?.error || 'Nao foi possivel carregar os clientes';
    clientFeedback.type = 'is-danger';
  } finally {
    loadingClients.value = false;
  }
};

const loadClientHistory = async (clientId) => {
  loadingHistory.value = true;
  historyError.value = '';
  try {
    clientHistory.value = await clientService.history(clientId);
  } catch (error) {
    historyError.value = error.response?.data?.error || 'Erro ao carregar historico';
    clientHistory.value = [];
  } finally {
    loadingHistory.value = false;
  }
};

const resetForm = () => {
  productForm.id = null;
  productForm.codigo = '';
  productForm.descricao = '';
  productForm.preco = 0;
  productForm.unidade = 'UN';
  productForm.tributacao = '';
  productForm.estoque = 0;
  productForm.categoria = '';
  feedback.message = '';
};

const handleSubmit = async () => {
  saving.value = true;
  feedback.message = '';

  try {
    const payload = { ...productForm };
    if (isEditing.value) {
      const updated = await productService.update(productForm.id, payload);
      products.value = products.value.map(product => product.id === updated.id ? updated : product);
      feedback.message = 'Produto atualizado com sucesso';
      feedback.type = 'is-success';
    } else {
      const created = await productService.create(payload);
      products.value = [created, ...products.value];
      feedback.message = 'Produto cadastrado com sucesso';
      feedback.type = 'is-success';
    }

    resetForm();
  } catch (error) {
    feedback.message = error.response?.data?.error || 'Erro ao salvar produto';
    feedback.type = 'is-danger';
  } finally {
    saving.value = false;
  }
};

const startEdit = (product) => {
  productForm.id = product.id;
  productForm.codigo = product.codigo;
  productForm.descricao = product.descricao;
  productForm.preco = Number(product.preco);
  productForm.unidade = product.unidade;
  productForm.tributacao = product.tributacao;
  productForm.estoque = product.estoque;
  productForm.categoria = product.categoria || '';
  feedback.message = '';
};

const handleSearch = async () => {
  await loadProducts();
};

const handleDelete = async (product) => {
  if (!confirm(`Deseja remover o produto ${product.codigo}?`)) return;
  saving.value = true;
  feedback.message = '';
  try {
    await productService.remove(product.id);
    products.value = products.value.filter(item => item.id !== product.id);
    feedback.message = 'Produto removido com sucesso';
    feedback.type = 'is-success';
  } catch (error) {
    feedback.message = error.response?.data?.error || 'Erro ao remover produto';
    feedback.type = 'is-danger';
  } finally {
    saving.value = false;
  }
};

const selectClient = (client) => {
  clientForm.id = client.id;
  clientForm.nome = client.nome;
  clientForm.email = client.email;
  clientForm.cnpj = client.cnpj || '';
  clientForm.inscricao_estadual = client.inscricao_estadual || '';
  clientForm.rota = client.rota || '';
  clientForm.segmentacao = client.segmentacao || '';
  clientForm.credit_limit = client.credit_limit !== null ? Number(client.credit_limit) : null;
  clientForm.credit_used = client.credit_used !== null ? Number(client.credit_used) : 0;
  clientForm.payment_terms = client.payment_terms || '';
  clientForm.cliente_status = client.cliente_status || 'ativo';
  clientFeedback.message = '';
  loadClientHistory(client.id);
};

const resetClientForm = () => {
  clientForm.id = null;
  clientForm.nome = '';
  clientForm.email = '';
  clientForm.cnpj = '';
  clientForm.inscricao_estadual = '';
  clientForm.rota = '';
  clientForm.segmentacao = '';
  clientForm.credit_limit = null;
  clientForm.credit_used = 0;
  clientForm.payment_terms = '';
  clientForm.cliente_status = 'ativo';
  clientFeedback.message = '';
  clientHistory.value = [];
  historyError.value = '';
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

const exportOrdersCsv = async () => {
  downloadingOrdersCsv.value = true;
  clientFeedback.message = '';
  try {
    const blob = await orderService.exportCsv();
    const filename = `pedidos-${new Date().toISOString().split('T')[0]}.csv`;
    triggerDownload(blob, filename);
    clientFeedback.message = 'Arquivo de pedidos exportado com sucesso';
    clientFeedback.type = 'is-success';
  } catch (error) {
    clientFeedback.message = error.response?.data?.error || 'Nao foi possivel exportar os pedidos';
    clientFeedback.type = 'is-danger';
  } finally {
    downloadingOrdersCsv.value = false;
  }
};

const generateTempPassword = () => Math.random().toString(36).slice(-8);

const handleResetAccess = async () => {
  if (!hasClientSelected.value) return;
  const suggestedEmail = clientForm.email || '';
  const email = window.prompt('Informe o email de acesso do cliente', suggestedEmail);
  if (!email) return;

  const tempPassword = generateTempPassword();
  clientSaving.value = true;
  clientFeedback.message = '';

  try {
    const { client, temporary_password: returnedPassword } = await clientService.resetAccess(clientForm.id, {
      email,
      password: tempPassword,
      nome: clientForm.nome
    });

    const updatedClient = client;
    const index = clients.value.findIndex(item => item.id === client.id);
    if (index !== -1) {
      clients.value.splice(index, 1, updatedClient);
    }
    selectClient(updatedClient);
    const passwordToShow = returnedPassword || tempPassword;
    clientFeedback.message = `Acesso configurado. Email: ${email}. Senha temporaria: ${passwordToShow}`;
    clientFeedback.type = 'is-success';
  } catch (error) {
    clientFeedback.message = error.response?.data?.error || 'Erro ao configurar acesso do cliente';
    clientFeedback.type = 'is-danger';
  } finally {
    clientSaving.value = false;
  }
};

const handleClientSave = async () => {
  if (!hasClientSelected.value) return;
  clientSaving.value = true;
  clientFeedback.message = '';
  try {
    const payload = {
      cnpj: clientForm.cnpj,
      inscricao_estadual: clientForm.inscricao_estadual,
      rota: clientForm.rota,
      segmentacao: clientForm.segmentacao,
      credit_limit: clientForm.credit_limit === null || clientForm.credit_limit === ''
        ? null
        : Number(clientForm.credit_limit),
      payment_terms: clientForm.payment_terms,
      cliente_status: clientForm.cliente_status
    };

    const updated = await clientService.update(clientForm.id, payload);
    const index = clients.value.findIndex(client => client.id === updated.id);
    if (index !== -1) {
      clients.value.splice(index, 1, updated);
    }
    selectClient(updated);
    clientFeedback.message = 'Dados do cliente atualizados com sucesso';
    clientFeedback.type = 'is-success';
  } catch (error) {
    clientFeedback.message = error.response?.data?.error || 'Erro ao atualizar cliente';
    clientFeedback.type = 'is-danger';
  } finally {
    clientSaving.value = false;
  }
};

const statusTagClass = (status) => {
  if (status === 'ativo') return 'is-success';
  if (status === 'bloqueado') return 'is-danger';
  return 'is-warning';
};

const formatCurrency = (value) => {
  if (value === null || value === undefined || value === '') {
    return '-';
  }
  return new Intl.NumberFormat('pt-BR', {
    style: 'currency',
    currency: 'BRL'
  }).format(Number(value));
};

const formatDateTime = (value) => {
  return new Intl.DateTimeFormat('pt-BR', {
    dateStyle: 'short',
    timeStyle: 'short'
  }).format(new Date(value));
};
</script>

<style scoped>
.kpi-grid .box {
  border: none;
  box-shadow: 0 6px 20px rgba(0, 0, 0, 0.05);
}

.kpi-box {
  min-width: 240px;
  border-radius: 12px;
  box-shadow: 0 8px 24px rgba(30, 58, 95, 0.25);
}

.table td,
.table th {
  vertical-align: middle;
}

.table-container {
  max-height: 480px;
  overflow-y: auto;
}

.clients-table {
  max-height: 520px;
}

.clients-table tr.is-selected-row {
  background: #eef6ff;
}

.client-form {
  border: 1px dashed rgba(0, 0, 0, 0.1);
  border-radius: 12px;
}

.history-box {
  border-radius: 12px;
  background: #f8fafc;
}

.history-timeline {
  list-style: none;
  padding: 0;
  margin: 0;
  max-height: 260px;
  overflow-y: auto;
}

.history-item {
  padding: 0.75rem 0;
  border-bottom: 1px solid rgba(0, 0, 0, 0.05);
}

.history-item:last-child {
  border-bottom: none;
}
</style>
