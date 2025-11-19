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
        <h1 class="title">Dashboard do Administrador</h1>
        <p class="subtitle">Bem-vindo, {{ user?.nome }}!</p>

        <div class="columns is-multiline mt-5">
          <div class="column is-4">
            <div class="box has-background-primary-light">
              <h3 class="title is-5">
                <span class="icon"><i class="fas fa-box"></i></span>
                Produtos
              </h3>
              <p class="subtitle is-6">Cadastre e gerencie o catálogo completo</p>
              <button class="button is-primary">Gerenciar Produtos</button>
            </div>
          </div>

          <div class="column is-4">
            <div class="box has-background-info-light">
              <h3 class="title is-5">
                <span class="icon"><i class="fas fa-users"></i></span>
                Clientes
              </h3>
              <p class="subtitle is-6">Administre clientes e seus limites</p>
              <button class="button is-info">Gerenciar Clientes</button>
            </div>
          </div>

          <div class="column is-4">
            <div class="box has-background-success-light">
              <h3 class="title is-5">
                <span class="icon"><i class="fas fa-chart-line"></i></span>
                Relatórios
              </h3>
              <p class="subtitle is-6">Visualize curva ABC e análises</p>
              <button class="button is-success">Ver Relatórios</button>
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
  if (!user.value || user.value.role !== 'admin') {
    router.push('/login');
  }
});

const handleLogout = () => {
  authService.logout();
  router.push('/');
};
</script>
