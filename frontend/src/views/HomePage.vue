<template>
  <div class="homepage">
    <!-- Progress Banner -->
    <section class="section has-background-info-light py-4">
      <div class="container">
        <div class="columns is-vcentered">
          <div class="column is-8">
            <h3 class="title is-5 mb-2">
              <span class="icon has-text-info"><i class="fas fa-code-branch"></i></span>
              Status do Desenvolvimento
            </h3>
            <progress class="progress is-info" :value="projectProgress" max="100">{{ projectProgress }}%</progress>
          </div>
          <div class="column is-4 has-text-right">
            <div class="box has-background-info has-text-white p-4">
              <p class="title is-2 has-text-white mb-1">{{ projectProgress }}%</p>
              <p class="subtitle is-6 has-text-white">{{ completedFeatures }}/{{ totalFeatures }} funcionalidades</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section class="section py-5">
      <div class="container">
        <div class="columns is-multiline">
          <div
            v-for="module in moduleStatus"
            :key="module.name"
            class="column is-4"
          >
            <div class="box status-card">
              <div class="is-flex is-justify-content-space-between is-align-items-center mb-2">
                <h4 class="title is-5 mb-0">{{ module.name }}</h4>
                <span class="tag" :class="module.tagClass">{{ module.label }}</span>
              </div>
              <p class="is-size-6 has-text-grey">{{ module.done }} / {{ module.total }} entregues</p>
              <progress class="progress is-small" :class="module.progressClass" :value="module.progress" max="100">
                {{ module.progress }}%
              </progress>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section class="hero is-primary is-fullheight-with-navbar">
      <div class="hero-body">
        <div class="container has-text-centered">
          <h1 class="title is-1 mb-6">
            Portal de Pedidos do Parceiro
          </h1>
          <p class="subtitle is-4 mb-6">
            Plataforma completa para gestão de pedidos entre distribuidores e lojas parceiras
          </p>
          <div class="buttons is-centered">
            <router-link to="/login" class="button is-light is-large">
              <span class="icon">
                <i class="fas fa-sign-in-alt"></i>
              </span>
              <span>Entrar</span>
            </router-link>
          </div>
        </div>
      </div>
    </section>

    <section class="section">
      <div class="container">
        <h2 class="title is-2 has-text-centered mb-6">
          <span class="icon has-text-primary"><i class="fas fa-rocket"></i></span>
          Como Funciona
        </h2>
        
        <div class="columns is-multiline">
          <div class="column is-4">
            <div class="card has-text-centered p-5 feature-card">
              <div class="card-content">
                <div class="icon-wrapper mb-4">
                  <span class="icon is-large has-text-white">
                    <i class="fas fa-users fa-3x"></i>
                  </span>
                </div>
                <h3 class="title is-4">Administração Completa</h3>
                <p>
                  Cadastre produtos com fotos, defina prazos de pagamento e gerencie clientes
                  com limites personalizados. Acesse relatórios completos e curva ABC.
                </p>
              </div>
            </div>
          </div>

          <div class="column is-4">
            <div class="card has-text-centered p-5 feature-card">
              <div class="card-content">
                <div class="icon-wrapper icon-wrapper-info mb-4">
                  <span class="icon is-large has-text-white">
                    <i class="fas fa-cogs fa-3x"></i>
                  </span>
                </div>
                <h3 class="title is-4">Operação Eficiente</h3>
                <p>
                  Operadores gerenciam limites comerciais, mantêm o catálogo atualizado
                  e garantem sincronização com o sistema Winthor.
                </p>
              </div>
            </div>
          </div>

          <div class="column is-4">
            <div class="card has-text-centered p-5 feature-card">
              <div class="card-content">
                <div class="icon-wrapper icon-wrapper-success mb-4">
                  <span class="icon is-large has-text-white">
                    <i class="fas fa-store fa-3x"></i>
                  </span>
                </div>
                <h3 class="title is-4">Pedidos Simplificados</h3>
                <p>
                  Lojas parceiras visualizam catálogo, fazem pedidos, acompanham status
                  e exportam histórico para análise. Interface mobile-friendly.
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section class="section has-background-light">
      <div class="container">
        <h2 class="title is-2 has-text-centered mb-6">Perfis de Usuário</h2>
        
        <div class="columns">
          <div class="column is-4">
            <div class="box">
              <h4 class="title is-5 has-text-primary">
                <span class="icon"><i class="fas fa-shield-alt"></i></span>
                Administrador
              </h4>
              <ul class="content">
                <li>Cadastro completo de produtos</li>
                <li>Gestão de clientes e limites</li>
                <li>Definição de prazos de pagamento</li>
                <li>Relatórios e análises</li>
              </ul>
            </div>
          </div>

          <div class="column is-4">
            <div class="box">
              <h4 class="title is-5 has-text-info">
                <span class="icon"><i class="fas fa-user-cog"></i></span>
                Operador
              </h4>
              <ul class="content">
                <li>Gerenciamento de limites</li>
                <li>Atualização de catálogo</li>
                <li>Validação de cadastros</li>
                <li>Suporte à sincronização</li>
              </ul>
            </div>
          </div>

          <div class="column is-4">
            <div class="box">
              <h4 class="title is-5 has-text-success">
                <span class="icon"><i class="fas fa-shopping-cart"></i></span>
                Loja Parceira
              </h4>
              <ul class="content">
                <li>Navegação no catálogo</li>
                <li>Realização de pedidos</li>
                <li>Acompanhamento de status</li>
                <li>Histórico e exportações</li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section class="section">
      <div class="container has-text-centered">
        <h2 class="title is-3 mb-4">Pronto para começar?</h2>
        <router-link to="/login" class="button is-primary is-large">
          Acessar o Portal
        </router-link>
      </div>
    </section>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';

// Project progress tracking alinhado ao plano atual (PLANO_DESENVOLVIMENTO.md)
const totalFeatures = ref(85);
const completedFeatures = ref(48);
const projectProgress = computed(() => Math.round((completedFeatures.value / totalFeatures.value) * 100));

const moduleStatus = computed(() => [
  {
    name: 'Autenticação & Autorização',
    done: 8,
    total: 8,
    label: 'Completo',
    progressClass: 'is-success',
    tagClass: 'is-success'
  },
  {
    name: 'Gestão de Produtos',
    done: 11,
    total: 12,
    label: 'Catálogo robusto + API pública',
    progressClass: 'is-link',
    tagClass: 'is-info'
  },
  {
    name: 'Gestão de Clientes',
    done: 5,
    total: 10,
    label: 'Em andamento',
    progressClass: 'is-warning',
    tagClass: 'is-warning'
  },
  {
    name: 'Sistema de Pedidos',
    done: 8,
    total: 15,
    label: 'Aprovação e cancelamento ativos',
    progressClass: 'is-primary',
    tagClass: 'is-primary'
  },
  {
    name: 'Total do Projeto',
    done: completedFeatures.value,
    total: totalFeatures.value,
    label: `${projectProgress.value}% concluído`,
    progressClass: 'is-info',
    tagClass: 'is-info'
  }
].map(module => ({
  ...module,
  progress: Math.round((module.done / module.total) * 100)
})));
</script>

<style scoped>
.homepage {
  background: linear-gradient(180deg, #f8f9fa 0%, #ffffff 100%);
}

.card {
  height: 100%;
  transition: all 0.3s ease;
  border: none;
  box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}

.card:hover {
  transform: translateY(-8px);
  box-shadow: 0 12px 24px rgba(0,0,0,0.15);
}

.feature-card {
  background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
}

.icon-wrapper {
  width: 80px;
  height: 80px;
  margin: 0 auto;
  border-radius: 50%;
  background: linear-gradient(135deg, #1e3a5f 0%, #2c5282 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 4px 15px rgba(30, 58, 95, 0.4);
}

.icon-wrapper-info {
  background: linear-gradient(135deg, #0891b2 0%, #06b6d4 100%);
  box-shadow: 0 4px 15px rgba(8, 145, 178, 0.4);
}

.icon-wrapper-success {
  background: linear-gradient(135deg, #059669 0%, #10b981 100%);
  box-shadow: 0 4px 15px rgba(5, 150, 105, 0.4);
}

.box {
  transition: all 0.3s ease;
  border-left: 4px solid transparent;
}

.box:hover {
  border-left-color: #1e3a5f;
  box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.hero.is-primary {
  background: linear-gradient(135deg, #1e3a5f 0%, #2c5282 100%);
}

.progress {
  height: 1.5rem;
  border-radius: 8px;
}

.has-background-info-light {
  background-color: #f0f9ff !important;
  border-bottom: 3px solid #0891b2;
}

.status-card {
  border: none;
  box-shadow: 0 8px 20px rgba(0, 0, 0, 0.06);
}
</style>
