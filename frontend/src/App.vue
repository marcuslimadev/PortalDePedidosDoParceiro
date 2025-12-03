<template>
  <div id="app-container">
    <OfflineIndicator />
    <ThemeToggle class="global-theme-toggle" />
    <router-view />
  </div>
</template>

<script>
import OfflineIndicator from './components/OfflineIndicator.vue';
import ThemeToggle from './components/ThemeToggle.vue';

export default {
  name: 'App',
  components: {
    OfflineIndicator,
    ThemeToggle
  }
};
</script>

<style>
.global-theme-toggle {
  position: fixed;
  bottom: 20px;
  right: 20px;
  z-index: 1000;
  background: var(--card-bg, #fff) !important;
  box-shadow: 0 2px 10px var(--shadow-color, rgba(0,0,0,0.2));
}
</style>
