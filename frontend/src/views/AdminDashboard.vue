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
            <p class="subtitle is-6 has-text-grey">Olá, {{ user?.nome }}!</p>
            <h1 class="title is-3">Central de administração</h1>
            <p class="has-text-grey-dark">Cadastre, edite e acompanhe o catálogo disponível para as lojas parceiras.</p>
          </div>
          <div class="column is-narrow">
            <div class="box kpi-box has-background-primary has-text-white">
              <p class="is-size-7 has-text-white-bis">Catálogo atualizado</p>
              <p class="title is-4 has-text-white">{{ products.length }} produtos</p>
              <p class="is-size-7">Filtro ativo: "{{ searchTerm || 'Nenhum' }}"</p>
            </div>
          </div>
        </div>

        <div class="columns is-multiline kpi-grid">
          <div class="column is-3-desktop is-6-tablet">
            <div class="box has-background-primary-light">
              <p class="heading">Catálogo</p>
              <p class="title is-4">{{ products.length }}</p>
              <p class="is-size-7">Produtos disponíveis</p>
            </div>
          </div>
          <div class="column is-3-desktop is-6-tablet">
            <div class="box has-background-info-light">
              <p class="heading">Fluxo</p>
              <p class="title is-4">Cadastro + Edição</p>
              <p class="is-size-7">Validado com campos Winthor</p>
            </div>
          </div>
          <div class="column is-3-desktop is-6-tablet">
            <div class="box has-background-success-light">
              <p class="heading">Equipe</p>
              <p class="title is-4">Admins e Operadores</p>
              <p class="is-size-7">Permissões aplicadas na API</p>
            </div>
          </div>
          <div class="column is-3-desktop is-6-tablet">
            <div class="box has-background-warning-light">
              <p class="heading">Status</p>
              <p class="title is-4">Produtos vivos</p>
              <p class="is-size-7">Listagem com busca por código/descrição</p>
            </div>
          </div>
        </div>

        <div class="box">
          <div class="level mb-4">
            <div class="level-left">
              <div>
                <p class="heading">Catálogo de produtos</p>
                <p class="title is-5">Cadastro e edição</p>
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
                <label class="label">Código</label>
                <input v-model="productForm.codigo" class="input" placeholder="WIN123" required>
              </div>
              <div class="column is-5">
                <label class="label">Descrição</label>
                <input v-model="productForm.descricao" class="input" placeholder="Produto conforme Winthor" required>
              </div>
              <div class="column is-2">
                <label class="label">Preço (R$)</label>
                <input v-model.number="productForm.preco" class="input" type="number" min="0" step="0.01" required>
              </div>
              <div class="column is-2">
                <label class="label">Unidade</label>
                <input v-model="productForm.unidade" class="input" placeholder="UN/PC/CX" required>
              </div>
              <div class="column is-3">
                <label class="label">Tributação</label>
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
                <span>{{ isEditing ? 'Salvar alterações' : 'Cadastrar produto' }}</span>
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
                  <th>Código</th>
                  <th>Descrição</th>
                  <th>Preço</th>
                  <th>Unidade</th>
                  <th>Tributação</th>
                  <th>Estoque</th>
                  <th>Categoria</th>
                  <th class="has-text-centered">Ações</th>
                </tr>
              </thead>
              <tbody>
                <tr v-if="!products.length && !loadingProducts">
                  <td colspan="8" class="has-text-centered has-text-grey">Nenhum produto cadastrado ainda.</td>
                </tr>
                <tr v-if="loadingProducts">
                  <td colspan="8" class="has-text-centered">
                    <span class="icon has-text-info"><i class="fas fa-spinner fa-spin"></i></span>
                    Carregando catálogo...
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
      </div>
    </section>
  </div>
</template>

<script setup>
import { ref, onMounted, reactive, computed } from 'vue';
import { useRouter } from 'vue-router';
import { authService } from '../services/api';
import { productService } from '../services/productService';

const router = useRouter();
const user = ref(null);
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

onMounted(() => {
  user.value = authService.getUser();
  if (!user.value || user.value.role !== 'admin') {
    router.push('/login');
  }
  loadProducts();
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
    feedback.message = error.response?.data?.error || 'Não foi possível carregar os produtos';
    feedback.type = 'is-danger';
  } finally {
    loadingProducts.value = false;
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
</style>
