<template>
  <button 
    class="theme-toggle" 
    @click="toggleTheme" 
    :title="isDark ? 'Modo claro' : 'Modo escuro'"
  >
    <span class="icon">
      <i :class="isDark ? 'fas fa-sun' : 'fas fa-moon'"></i>
    </span>
  </button>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue';

const isDark = ref(false);

const toggleTheme = () => {
  isDark.value = !isDark.value;
  applyTheme();
  localStorage.setItem('theme', isDark.value ? 'dark' : 'light');
};

const applyTheme = () => {
  if (isDark.value) {
    document.documentElement.setAttribute('data-theme', 'dark');
  } else {
    document.documentElement.removeAttribute('data-theme');
  }
};

onMounted(() => {
  const saved = localStorage.getItem('theme');
  if (saved === 'dark') {
    isDark.value = true;
  } else if (saved === 'light') {
    isDark.value = false;
  } else {
    // Auto-detect system preference
    isDark.value = window.matchMedia('(prefers-color-scheme: dark)').matches;
  }
  applyTheme();
});

// Listen for system theme changes
if (typeof window !== 'undefined') {
  window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
    if (!localStorage.getItem('theme')) {
      isDark.value = e.matches;
      applyTheme();
    }
  });
}
</script>

<style scoped>
.theme-toggle {
  background: transparent;
  border: 2px solid currentColor;
  border-radius: 50%;
  width: 40px;
  height: 40px;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.3s ease;
  color: inherit;
}

.theme-toggle:hover {
  transform: scale(1.1);
  background: rgba(128, 128, 128, 0.1);
}

.theme-toggle .icon {
  font-size: 1.1rem;
}
</style>
