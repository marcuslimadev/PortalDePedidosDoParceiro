<template>
  <div>
    <OfflineIndicator />
    <router-view />
  </div>
</template>

<script>
import OfflineIndicator from './components/OfflineIndicator.vue';

export default {
  name: 'App',
  components: {
    OfflineIndicator
  }
};
</script>
