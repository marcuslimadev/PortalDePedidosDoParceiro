<template>
  <div class="column is-one-third">
    <div class="card">
      <header class="card-header">
        <p class="card-header-title">{{ title }}</p>
      </header>
      <div class="card-content">
        <ul>
          <li v-for="action in actions" :key="action" class="mb-2">{{ action }}</li>
        </ul>
      </div>
    </div>
  </div>
</template>

<script setup>
const props = defineProps({
  title: {
    type: String,
    required: true
  },
  actions: {
    type: Array,
    default: () => []
  }
});
</script>
