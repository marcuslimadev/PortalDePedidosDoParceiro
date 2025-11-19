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
        <h2 class="title is-2 has-text-centered mb-6">Como Funciona</h2>
        
        <div class="columns is-multiline">
          <div class="column is-4">
            <div class="card has-text-centered p-5">
              <div class="card-content">
                <span class="icon is-large has-text-primary mb-4">
                  <i class="fas fa-users fa-3x"></i>
                </span>
                <h3 class="title is-4">Administração Completa</h3>
                <p>
                  Cadastre produtos com fotos, defina prazos de pagamento e gerencie clientes
                  com limites personalizados. Acesse relatórios completos e curva ABC.
                </p>
              </div>
            </div>
          </div>

          <div class="column is-4">
            <div class="card has-text-centered p-5">
              <div class="card-content">
                <span class="icon is-large has-text-info mb-4">
                  <i class="fas fa-cogs fa-3x"></i>
                </span>
                <h3 class="title is-4">Operação Eficiente</h3>
                <p>
                  Operadores gerenciam limites comerciais, mantêm o catálogo atualizado
                  e garantem sincronização com o sistema Winthor.
                </p>
              </div>
            </div>
          </div>

          <div class="column is-4">
            <div class="card has-text-centered p-5">
              <div class="card-content">
                <span class="icon is-large has-text-success mb-4">
                  <i class="fas fa-store fa-3x"></i>
                </span>
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
import { ref } from 'vue';

// Project progress tracking
const totalFeatures = ref(85);
const completedFeatures = ref(11);
const projectProgress = ref(Math.round((completedFeatures.value / totalFeatures.value) * 100));
</script>

<style scoped>
.card {
  height: 100%;
  transition: transform 0.2s;
}

.card:hover {
  transform: translateY(-5px);
  box-shadow: 0 8px 16px rgba(0,0,0,0.1);
}
</style>
