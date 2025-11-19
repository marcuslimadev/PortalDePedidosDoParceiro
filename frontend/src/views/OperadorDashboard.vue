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
        <h1 class="title">Dashboard do Operador</h1>
        <p class="subtitle">Bem-vindo, {{ user?.nome }}!</p>

        <div class="columns is-multiline mt-5">
          <div class="column is-6">
            <div class="box">
              <h3 class="title is-5">
                <span class="icon has-text-info"><i class="fas fa-tags"></i></span>
                Catálogo de Produtos
              </h3>
              <p class="mb-4">Atualize preços, estoque e informações dos produtos</p>
              <button class="button is-info">Atualizar Catálogo</button>
            </div>
          </div>

          <div class="column is-6">
            <div class="box">
              <h3 class="title is-5">
                <span class="icon has-text-warning"><i class="fas fa-credit-card"></i></span>
                Gerenciar Limites
              </h3>
              <p class="mb-4">Ajuste limites de crédito dos clientes</p>
              <button class="button is-warning">Gerenciar Limites</button>
            </div>
          </div>

          <div class="column is-12">
            <div class="box">
              <h3 class="title is-5">
                <span class="icon has-text-success"><i class="fas fa-sync"></i></span>
                Sincronização Winthor
              </h3>
              <p class="mb-4">Sincronize dados com o sistema Winthor</p>
              <button class="button is-success">Sincronizar Agora</button>
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
  if (!user.value || user.value.role !== 'operador') {
    router.push('/login');
  }
});

const handleLogout = () => {
  authService.logout();
  router.push('/');
};
</script>
