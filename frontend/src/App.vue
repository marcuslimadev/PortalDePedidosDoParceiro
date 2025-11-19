<template>
  <section class="section">
    <div class="container">
      <h1 class="title">Portal de Pedidos do Parceiro</h1>
      <p class="subtitle">
        Protótipo inicial em Vue 3 + Bulma para validar o layout do futuro portal multi-perfis.
      </p>

      <div class="columns is-multiline">
        <RoleCard
          v-for="role in roles"
          :key="role.title"
          :title="role.title"
          :actions="role.actions"
        />
      </div>
    </div>
  </section>
</template>

<script setup>
import RoleCard from './components/RoleCard.vue';

const roles = [
  {
    title: 'Administrador do Sistema',
    actions: [
      'Cadastro de produtos com campos obrigatórios, precificação e galeria de fotos',
      'Definição de prazos de pagamento (30/60/90 dias)',
      'Cadastro de clientes com limites e perfis de acesso',
      'Responsividade mobile e relatórios (ABC, lojas, histórico completo)'
    ]
  },
  {
    title: 'Operador do Sistema',
    actions: [
      'Gerenciar limites e produtos seguindo as regras do Winthor',
      'Administrar cadastro de clientes reutilizando campos padrão',
      'Preparar dados para sincronização futura com ERP'
    ]
  },
  {
    title: 'Loja Parceira',
    actions: [
      'Visualizar catálogo, histórico e status dos pedidos',
      'Repetir pedidos com os mesmos produtos e abrir novos pedidos',
      'Exportar compras para análise local (ex.: Excel)'
    ]
  }
];
</script>
