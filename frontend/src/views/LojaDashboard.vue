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
        <h1 class="title">Loja Parceira</h1>
        <p class="subtitle">Bem-vindo, {{ user?.nome }}!</p>

        <div class="columns is-multiline mt-5">
          <div class="column is-4">
            <div class="box has-text-centered">
              <span class="icon is-large has-text-success">
                <i class="fas fa-shopping-bag fa-3x"></i>
              </span>
              <h3 class="title is-5 mt-3">Catálogo</h3>
              <p class="mb-4">Navegue pelos produtos disponíveis</p>
              <button class="button is-success">Ver Catálogo</button>
            </div>
          </div>

          <div class="column is-4">
            <div class="box has-text-centered">
              <span class="icon is-large has-text-info">
                <i class="fas fa-file-invoice fa-3x"></i>
              </span>
              <h3 class="title is-5 mt-3">Meus Pedidos</h3>
              <p class="mb-4">Acompanhe seus pedidos em andamento</p>
              <button class="button is-info">Ver Pedidos</button>
            </div>
          </div>

          <div class="column is-4">
            <div class="box has-text-centered">
              <span class="icon is-large has-text-warning">
                <i class="fas fa-history fa-3x"></i>
              </span>
              <h3 class="title is-5 mt-3">Histórico</h3>
              <p class="mb-4">Consulte e exporte seu histórico</p>
              <button class="button is-warning">Ver Histórico</button>
            </div>
          </div>

          <div class="column is-12">
            <div class="box">
              <h3 class="title is-5">
                <span class="icon has-text-primary"><i class="fas fa-cart-plus"></i></span>
                Fazer Novo Pedido
              </h3>
              <p class="mb-4">Crie um novo pedido ou repita um pedido anterior</p>
              <div class="buttons">
                <button class="button is-primary">
                  <span class="icon"><i class="fas fa-plus"></i></span>
                  <span>Novo Pedido</span>
                </button>
                <button class="button is-light">
                  <span class="icon"><i class="fas fa-redo"></i></span>
                  <span>Repetir Último Pedido</span>
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { authService } from '../services/api';

const router = useRouter();
const user = ref(null);

onMounted(() => {
  user.value = authService.getUser();
  if (!user.value || user.value.role !== 'loja') {
    router.push('/login');
  }
});

const handleLogout = () => {
  authService.logout();
  router.push('/');
};
</script>
