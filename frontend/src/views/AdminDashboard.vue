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
              <p class="title is-4">Produtos ativos</p>
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
              <button type="button" class="button is-link is-light" @click="openWizard" :disabled="saving">
                <span class="icon"><i class="fas fa-wand-magic-sparkles"></i></span>
                <span>Ficha Winthor (wizard)</span>
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

    <div class="modal" :class="{ 'is-active': showWizard }">
      <div class="modal-background" @click="closeWizard"></div>
      <div class="modal-card">
        <header class="modal-card-head">
          <p class="modal-card-title">Wizard Winthor</p>
          <button class="delete" aria-label="close" @click="closeWizard"></button>
        </header>
        <section class="modal-card-body">
          <div class="wizard-steps">
            <div
              v-for="(section, index) in wizardSections"
              :key="section.title"
              class="wizard-step"
              :class="{ active: wizardStep === index + 1 }"
            >
              <span class="step-index">{{ index + 1 }}</span>
              <div>
                <p class="is-size-6 has-text-weight-semibold">{{ section.title }}</p>
                <p class="is-size-7 has-text-grey">{{ section.description }}</p>
              </div>
            </div>
          </div>

          <div class="wizard-form">
            <div v-for="(section, index) in wizardSections" :key="section.title" v-show="wizardStep === index + 1">
              <div class="columns is-multiline">
                <div v-for="field in section.fields" :key="field.key" class="column is-6">
                  <label class="label">{{ field.label }}</label>
                  <input
                    class="input"
                    :value="productForm.winthor_data?.[field.key] || ''"
                    @input="updateWinthorField(field.key, $event.target.value)"
                    :placeholder="field.label"
                  >
                </div>
              </div>
            </div>
            <div class="box is-light mt-3">
              <p class="label is-size-7">Adicionar campo livre</p>
              <div class="columns is-mobile">
                <div class="column is-5">
                  <input class="input is-small" v-model="customFieldKey" placeholder="nome_campo">
                </div>
                <div class="column is-5">
                  <input class="input is-small" v-model="customFieldValue" placeholder="valor">
                </div>
                <div class="column is-2">
                  <button class="button is-link is-small is-fullwidth" @click="addCustomField">
                    <span class="icon is-small"><i class="fas fa-plus"></i></span>
                  </button>
                </div>
              </div>
            </div>
          </div>
        </section>
        <footer class="modal-card-foot is-justify-content-space-between">
          <div>
            <button class="button" @click="wizardPrev" :disabled="wizardStep === 1">Voltar</button>
            <button class="button is-link" @click="wizardNext" :disabled="wizardStep === wizardSections.length">Proximo</button>
          </div>
          <div>
            <button class="button is-primary" @click="closeWizard">Concluir</button>
          </div>
        </footer>
      </div>
    </div>
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
const showWizard = ref(false);
const wizardStep = ref(1);
const customFieldKey = ref('');
const customFieldValue = ref('');
const wizardSections = [
  {
    title: 'Identificacao basica',
    description: 'Campos essenciais do produto',
    fields: [
      { key: 'rid', label: 'RID' },
      { key: 'codprod', label: 'Cod. Produto' },
      { key: 'descricao', label: 'Descricao' },
      { key: 'codfab', label: 'Cod. Fabrica' },
      { key: 'dv', label: 'Digito verificador' },
      { key: 'numoriginal', label: 'Num. original' },
      { key: 'codprodprinc', label: 'Cod. prod. principal' },
      { key: 'codprodmaster', label: 'Cod. prod. master' },
      { key: 'registropeca', label: 'Registro peca' },
      { key: 'obs', label: 'Observacao' },
      { key: 'obs2', label: 'Observacao 2' },
      { key: 'descricao1', label: 'Descricao 1' },
      { key: 'descricao2', label: 'Descricao 2' },
      { key: 'descricao3', label: 'Descricao 3' },
      { key: 'descricao4', label: 'Descricao 4' },
      { key: 'descricao5', label: 'Descricao 5' },
      { key: 'descricao6', label: 'Descricao 6' },
      { key: 'descricao7', label: 'Descricao 7' }
    ]
  },
  {
    title: 'Classificacao mercadologica',
    description: 'Fornecedor, departamento, secao, marca, categoria',
    fields: [
      { key: 'codfornec', label: 'Cod. fornecedor' },
      { key: 'fornecedor', label: 'Fornecedor' },
      { key: 'codepto', label: 'Cod. departamento' },
      { key: 'codsec', label: 'Cod. secao' },
      { key: 'codmarca', label: 'Cod. marca' },
      { key: 'codsubmarca', label: 'Cod. submarca' },
      { key: 'codcategoria', label: 'Cod. categoria' },
      { key: 'codsubcategoria', label: 'Cod. subcategoria' },
      { key: 'codlinhaprod', label: 'Cod. linha produto' },
      { key: 'tipomerc', label: 'Tipo mercadoria' },
      { key: 'naturezaproduto', label: 'Natureza produto' },
      { key: 'coddistrb', label: 'Cod. distribuidora' },
      { key: 'tipoprod', label: 'Tipo produto' },
      { key: 'tipocustotransf', label: 'Tipo custo transf' },
      { key: 'status', label: 'Status' }
    ]
  },
  {
    title: 'Unidades e embalagem',
    description: 'Unidades de venda/estoque e volumes',
    fields: [
      { key: 'embalagem', label: 'Embalagem' },
      { key: 'unidade', label: 'Unidade' },
      { key: 'qtunit', label: 'Qtde unidade' },
      { key: 'embalagemmaster', label: 'Embalagem master' },
      { key: 'unidademaster', label: 'Unidade master' },
      { key: 'qtunitcx', label: 'Qtde unid. caixa' },
      { key: 'unidadepadrao', label: 'Unidade padrao' },
      { key: 'idembalagem', label: 'Id embalagem' },
      { key: 'codprodembalagem', label: 'Cod. prod. embalagem' },
      { key: 'codformatopapel', label: 'Formato papel' },
      { key: 'gramatura', label: 'Gramatura' },
      { key: 'descpapel', label: 'Descricao papel' }
    ]
  },
  {
    title: 'Codigos de barras e auxiliares',
    description: 'GTIN e codigos auxiliares',
    fields: [
      { key: 'gtincodauxiliar', label: 'GTIN cod. auxiliar' },
      { key: 'gtincodauxiliar2', label: 'GTIN cod. auxiliar 2' },
      { key: 'gtincodauxiliartrib', label: 'GTIN cod. auxiliar trib.' },
      { key: 'codauxiliar', label: 'Cod. auxiliar' },
      { key: 'codauxiliar2', label: 'Cod. auxiliar 2' },
      { key: 'codauxiliartrib', label: 'Cod. auxiliar trib.' },
      { key: 'codprodfornec', label: 'Cod. produto fornecedor' },
      { key: 'codinterno', label: 'Cod. interno' },
      { key: 'codprodsintegra', label: 'Cod. prod. sintegra' }
    ]
  },
  {
    title: 'Comercial e precos',
    description: 'Margens, precos maximos e comissoes',
    fields: [
      { key: 'revenda', label: 'Revenda' },
      { key: 'seqtabpreco', label: 'Seq. tabela preco' },
      { key: 'margemmin', label: 'Margem minima' },
      { key: 'precofixo', label: 'Preco fixo' },
      { key: 'precomaxconsumtab', label: 'Preco max. consumidor (tabela)' },
      { key: 'precomaxconsum', label: 'Preco max. consumidor' },
      { key: 'precofabrica', label: 'Preco fabrica' },
      { key: 'precicestrangeira', label: 'Preco custo estrangeira' },
      { key: 'percvenda', label: '% venda' },
      { key: 'pcomext1', label: '% comissao externa 1' },
      { key: 'pcomint1', label: '% comissao interna 1' },
      { key: 'pcomrep1', label: '% comissao representante 1' },
      { key: 'tipocomissao', label: 'Tipo comissao' },
      { key: 'classecomissao', label: 'Classe comissao' },
      { key: 'percebonificvenda', label: '% bonificacao venda' },
      { key: 'vlbonific', label: 'Valor bonificacao' },
      { key: 'percbon', label: '% bonificacao' }
    ]
  },
  {
    title: 'Logistica e estoque',
    description: 'Regras de compra, lote e validade',
    fields: [
      { key: 'codprazoent', label: 'Cod. prazo entrega' },
      { key: 'multiplo', label: 'Multiplo venda' },
      { key: 'multiplocompras', label: 'Multiplo compras' },
      { key: 'qtminsugcompra', label: 'Qtde minima compra' },
      { key: 'qtdeMaxSeparPedido', label: 'Qtde max separacao' },
      { key: 'aceitavendafracao', label: 'Aceita fracao' },
      { key: 'checarmultiplovendabnf', label: 'Checar multiplo bonificado' },
      { key: 'conferencocheckout', label: 'Confere checkout' },
      { key: 'prazomaxvalidade', label: 'Prazo max validade' },
      { key: 'prazominvalidade', label: 'Prazo min validade' },
      { key: 'prazoval', label: 'Prazo validade padrao' },
      { key: 'numdiasvalidademin', label: 'Dias validade minima' },
      { key: 'controlavalidadedolote', label: 'Controla validade lote' },
      { key: 'dtinicontlote', label: 'Data inicio controle lote' },
      { key: 'estoqueporlote', label: 'Estoque por lote' },
      { key: 'proxnumlote', label: 'Prox numero lote' },
      { key: 'prefixolote', label: 'Prefixo lote' },
      { key: 'induzlote', label: 'Induz lote' },
      { key: 'numlote', label: 'Numero lote' },
      { key: 'pesobruto', label: 'Peso bruto' },
      { key: 'pesoliq', label: 'Peso liquido' },
      { key: 'pesoliqdi', label: 'Peso liquido DI' },
      { key: 'pesobruToMaster', label: 'Peso bruto master' },
      { key: 'pesoembalagem', label: 'Peso embalagem' },
      { key: 'pesopesa', label: 'Peso peca' },
      { key: 'pesovariavel', label: 'Peso variavel' },
      { key: 'pesominimo', label: 'Peso minimo' },
      { key: 'pesomaximo', label: 'Peso maximo' },
      { key: 'pesobrutofrete', label: 'Peso bruto frete' },
      { key: 'valortaraporpeca', label: 'Valor tara peca' },
      { key: 'taraporpeca', label: 'Tara por peca' },
      { key: 'percperdakw', label: '% perda kg' },
      { key: 'percdiferencakgfrio', label: '% diferenca kg frio' },
      { key: 'fatorconversaokg', label: 'Fator conversao kg' },
      { key: 'tipostoque', label: 'Tipo estoque' },
      { key: 'classeestoque', label: 'Classe estoque' },
      { key: 'sugvenda', label: 'Sugestao venda' },
      { key: 'tipomedicamento', label: 'Tipo medicamento' },
      { key: 'tipodescarga', label: 'Tipo descarga' },
      { key: 'tipovolumedescarga', label: 'Tipo volume descarga' },
      { key: 'freteespecial', label: 'Frete especial' }
    ]
  },
  {
    title: 'Dimensoes e WMS',
    description: 'Dados para armazenagem e palete',
    fields: [
      { key: 'usawms', label: 'Usa WMS' },
      { key: 'modulo', label: 'Modulo WMS' },
      { key: 'volume', label: 'Volume' },
      { key: 'altura', label: 'Altura' },
      { key: 'diametroexterno', label: 'Diametro externo' },
      { key: 'diametrointerno', label: 'Diametro interno' },
      { key: 'litragem', label: 'Litragem' },
      { key: 'numero', label: 'Numero' },
      { key: 'qtmetros', label: 'Qtde metros' },
      { key: 'rua', label: 'Rua' },
      { key: 'codagrupmapasep', label: 'Cod. agrup. mapa separacao' },
      { key: 'codgrade', label: 'Cod. grade' },
      { key: 'colunagrade', label: 'Coluna grade' },
      { key: 'tamanhopeca', label: 'Tamanho peca' },
      { key: 'lastropal', label: 'Lastro palete' },
      { key: 'alturapal', label: 'Altura palete' },
      { key: 'alturatotal', label: 'Altura total' },
      { key: 'tipoalturapalete', label: 'Tipo altura palete' },
      { key: 'qttotpal', label: 'Qtde total palete' }
    ]
  },
  {
    title: 'Informacoes tecnicas e adicionais',
    description: 'Ficha tecnica, literatura e concentracao',
    fields: [
      { key: 'enviainftecnicanfe', label: 'Envia inf. tecnica NFe' },
      { key: 'codtablit', label: 'Cod. tabela literatura' },
      { key: 'informacoestecnicas', label: 'Informacoes tecnicas' },
      { key: 'dadostecnicos', label: 'Dados tecnicos' },
      { key: 'codgrulit', label: 'Cod. grupo literatura' },
      { key: 'destaquefichatecnica', label: 'Destaque ficha tecnica' },
      { key: 'dirfotoprod', label: 'Diretorio fotos' },
      { key: 'seqpagina', label: 'Sequencial pagina' },
      { key: 'numpag', label: 'Numero pagina' },
      { key: 'letrapagina', label: 'Letra pagina' },
      { key: 'usaclassificacao', label: 'Usa classificacao' },
      { key: 'vlmaodeobra', label: 'Valor mao de obra' },
      { key: 'concentracao', label: 'Concentracao' }
    ]
  },
  {
    title: 'Fiscal e tributario',
    description: 'NCM, IPI, PIS/COFINS e observacoes fiscais',
    fields: [
      { key: 'codncmex', label: 'NCM' },
      { key: 'nbm', label: 'NBM' },
      { key: 'extipi', label: 'EXTIPI' },
      { key: 'unidadetrib', label: 'Unidade tributavel' },
      { key: 'unidadetribex', label: 'Unidade trib. exterior' },
      { key: 'fatorconvtrib', label: 'Fator conv. trib.' },
      { key: 'fatorconvtribex', label: 'Fator conv. trib. ext.' },
      { key: 'classificfiscal', label: 'Classificacao fiscal' },
      { key: 'codunidmedidanf', label: 'Cod. unid. medida NF' },
      { key: 'codagregacao', label: 'Cod. agregacao' },
      { key: 'usacodagregacao', label: 'Usa cod. agregacao' },
      { key: 'percaliqext', label: 'Aliquota externa' },
      { key: 'percalqint', label: 'Aliquota interna' },
      { key: 'pericm', label: '% ICMS' },
      { key: 'pericmsantecipado', label: '% ICMS antecipado' },
      { key: 'percicmred', label: '% red. base ICMS' },
      { key: 'percipi', label: '% IPI' },
      { key: 'perciva', label: '% IVA/MVA' },
      { key: 'perpis', label: '% PIS' },
      { key: 'percofins', label: '% COFINS' },
      { key: 'percoutrasdesp', label: '% outras despesas' },
      { key: 'percdespadicional', label: '% desp. adicional' },
      { key: 'percsuframa', label: '% SUFRAMA' },
      { key: 'aliquotacif', label: 'Aliquota CIF' },
      { key: 'imunetrib', label: 'Imune tributacao' },
      { key: 'ob scontxcampo', label: 'Obs contab. campo' },
      { key: 'obsfiscoxcampo', label: 'Obs fisco campo' },
      { key: 'obscontxttexto', label: 'Obs contab. texto' },
      { key: 'obsfiscoxtexto', label: 'Obs fisco texto' },
      { key: 'cestabasicalegis', label: 'Cesta basica legis' }
    ]
  },
  {
    title: 'Importacao e custos',
    description: 'Dados de importacao e frete',
    fields: [
      { key: 'importado', label: 'Importado' },
      { key: 'conciliaimportacao', label: 'Concilia importacao' },
      { key: 'usalicencaimportacao', label: 'Usa licenca importacao' },
      { key: 'moeda', label: 'Moeda' },
      { key: 'dtdolar', label: 'Data dolar' },
      { key: 'custorep', label: 'Custo reposicao' },
      { key: 'custoreptab', label: 'Custo reposicao tabela' },
      { key: 'percfrete', label: '% frete CIF' },
      { key: 'percfretefob', label: '% frete FOB' },
      { key: 'percoutrasdesp', label: '% outras despesas' },
      { key: 'percdespadicional', label: '% desp adicional' },
      { key: 'percsuframa', label: '% SUFRAMA' },
      { key: 'tipoembarqueimp', label: 'Tipo embarque importacao' },
      { key: 'paisorigem', label: 'Pais origem' }
    ]
  },
  {
    title: 'Datas e usuarios',
    description: 'Controle de criacao e alteracao',
    fields: [
      { key: 'dtcadastro', label: 'Data cadastro' },
      { key: 'codfunccadastro', label: 'Func. cadastro' },
      { key: 'dtultalter', label: 'Data ultima alteracao' },
      { key: 'codfuncultalter', label: 'Func. ult. alteracao' },
      { key: 'dtultaltcad', label: 'Data ult. alt. cadastro' },
      { key: 'codfuncultaltcad', label: 'Func. ult. alt. cadastro' },
      { key: 'dtultaltcom', label: 'Data ult. alt. comercial' },
      { key: 'dtexclusao', label: 'Data exclusao' }
    ]
  },
  {
    title: 'Saude e farmacia',
    description: 'Campos ANVISA/SNGPC',
    fields: [
      { key: 'anvisa', label: 'Registro ANVISA' },
      { key: 'simpro', label: 'Codigo SIMPRO' },
      { key: 'pmpfmedicamento', label: 'PMPF' },
      { key: 'registromsmed', label: 'Registro MS' },
      { key: 'codmotisencaoanvisa', label: 'Cod. isencao ANVISA' },
      { key: 'farmaciapopular', label: 'Farmacia popular' },
      { key: 'psicotropico', label: 'Psicotropico' },
      { key: 'retinoico', label: 'Retinoico' },
      { key: 'usoprolongadosngpc', label: 'Uso prolongado SNGPC' },
      { key: 'tipotributmedic', label: 'Tipo tribut. medicamento' },
      { key: 'codsazonalidademed', label: 'Cod. sazonalidade med' },
      { key: 'codlinhaprazo', label: 'Cod. linha prazo' },
      { key: 'codprincipativo', label: 'Cod. principio ativo 1' },
      { key: 'codprincipativo2', label: 'Cod. principio ativo 2' },
      { key: 'formaesterilizacao', label: 'Forma esterilizacao' },
      { key: 'codsalmed', label: 'Cod. sais medicamentosos' }
    ]
  },
  {
    title: 'E-commerce e integracoes',
    description: 'Campos para canais online',
    fields: [
      { key: 'enviaecommerce', label: 'Envia ecommerce' },
      { key: 'nomeecommerce', label: 'Nome ecommerce' },
      { key: 'tituloecommerce', label: 'Titulo ecommerce' },
      { key: 'subtituloecommerce', label: 'Subtitulo ecommerce' },
      { key: 'diretoriofotos', label: 'Diretorio fotos' },
      { key: 'exibesemestoqueecommerce', label: 'Exibe sem estoque' },
      { key: 'codcamplomadee', label: 'Cod. campo loja madee' },
      { key: 'codadwords', label: 'Cod. adwords' },
      { key: 'linkid', label: 'Link ID' },
      { key: 'tipointegracaob2b', label: 'Tipo integracao B2B' },
      { key: 'usaecommerceunilever', label: 'Usa ecommerce Unilever' },
      { key: 'embvendaecommerceunilever', label: 'Emb. venda Unilever' },
      { key: 'utilizaintegracaokibon', label: 'Integracao Kibon' },
      { key: 'fatorconversaobionexo', label: 'Fator conversao Bionexo' }
    ]
  },
  {
    title: 'Outros controles e riscos',
    description: 'Campos diversos de risco e patrimonio',
    fields: [
      { key: 'codcor', label: 'Cod. cor' },
      { key: 'controlapatrimonio', label: 'Controla patrimonio' },
      { key: 'controladoibama', label: 'Controlado IBAMA' },
      { key: 'codrisco', label: 'Cod. risco' },
      { key: 'codonu', label: 'Cod. ONU' },
      { key: 'codacondicionamento', label: 'Cod. acondicionamento' },
      { key: 'apto', label: 'Apto' },
      { key: 'myfrota', label: 'MyFrota' },
      { key: 'fldselecao', label: 'Campo selecao' }
    ]
  }
];
const productForm = reactive({
  id: null,
  codigo: '',
  descricao: '',
  preco: 0,
  unidade: 'UN',
  tributacao: '',
  estoque: 0,
  categoria: '',
  winthor_data: {}
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
  productForm.winthor_data = {};
  feedback.message = '';
};

const handleSubmit = async () => {
  saving.value = true;
  feedback.message = '';

  try {
  const payload = { ...productForm };
  payload.winthor_data = { ...productForm.winthor_data };
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
  productForm.winthor_data = product.winthor_data || {};
  feedback.message = '';
};

const handleSearch = async () => {
  await loadProducts();
};

const openWizard = () => {
  showWizard.value = true;
  wizardStep.value = 1;
};

const closeWizard = () => {
  showWizard.value = false;
};

const wizardNext = () => {
  if (wizardStep.value < wizardSections.length) {
    wizardStep.value += 1;
  }
};

const wizardPrev = () => {
  if (wizardStep.value > 1) {
    wizardStep.value -= 1;
  }
};

const updateWinthorField = (key, value) => {
  productForm.winthor_data = {
    ...productForm.winthor_data,
    [key]: value
  };
};

const addCustomField = () => {
  if (!customFieldKey.value) return;
  updateWinthorField(customFieldKey.value, customFieldValue.value);
  customFieldKey.value = '';
  customFieldValue.value = '';
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

.wizard-steps {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 0.75rem;
  margin-bottom: 1rem;
}

.wizard-step {
  padding: 0.75rem;
  border: 1px solid #e5e7eb;
  border-radius: 10px;
  background: #f9fafb;
  display: grid;
  grid-template-columns: auto 1fr;
  gap: 0.6rem;
  align-items: center;
}

.wizard-step.active {
  border-color: #3b82f6;
  background: #eff6ff;
}

.wizard-step .step-index {
  width: 32px;
  height: 32px;
  border-radius: 999px;
  background: #3b82f6;
  color: #fff;
  display: grid;
  place-items: center;
  font-weight: 700;
}

.wizard-form {
  min-height: 240px;
}

.box.is-light {
  background: #f8fafc;
  border-radius: 10px;
}
</style>
