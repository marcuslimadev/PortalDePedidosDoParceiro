<template>
  <div class="login-page">
    <section class="hero is-fullheight">
      <div class="hero-body">
        <div class="container">
          <div class="columns is-centered">
            <div class="column is-5-tablet is-4-desktop">
              <div class="box">
                <h1 class="title has-text-centered">Portal de Pedidos</h1>
                <p class="subtitle has-text-centered">Faça login para continuar</p>

                <form @submit.prevent="handleLogin">
                  <div class="field">
                    <label class="label">Email</label>
                    <div class="control has-icons-left">
                      <input 
                        v-model="email" 
                        class="input" 
                        type="email" 
                        placeholder="seu@email.com"
                        required
                      >
                      <span class="icon is-small is-left">
                        <i class="fas fa-envelope"></i>
                      </span>
                    </div>
                  </div>

                  <div class="field">
                    <label class="label">Senha</label>
                    <div class="control has-icons-left">
                      <input 
                        v-model="password" 
                        class="input" 
                        type="password" 
                        placeholder="********"
                        required
                      >
                      <span class="icon is-small is-left">
                        <i class="fas fa-lock"></i>
                      </span>
                    </div>
                  </div>

                  <div v-if="error" class="notification is-danger is-light">
                    {{ error }}
                  </div>

                  <div class="field">
                    <div class="control">
                      <button 
                        type="submit" 
                        class="button is-primary is-fullwidth"
                        :class="{ 'is-loading': loading }"
                        :disabled="loading"
                      >
                        Entrar
                      </button>
                    </div>
                  </div>

                  <div class="has-text-centered mt-4">
                    <router-link to="/" class="has-text-grey">
                      ← Voltar para home
                    </router-link>
                  </div>
                </form>
              </div>

              <div class="notification is-info is-light mt-4">
                <p class="has-text-centered"><strong>Usuário de teste:</strong></p>
                <p class="has-text-centered">Email: admin@portalpedidos.com</p>
                <p class="has-text-centered">Senha: admin123</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { authService } from '../services/api';

const router = useRouter();
const email = ref('');
const password = ref('');
const error = ref('');
const loading = ref(false);

const handleLogin = async () => {
  error.value = '';
  loading.value = true;

  try {
    const response = await authService.login(email.value, password.value);
    
    // Redirect based on role
    const role = response.user.role;
    if (role === 'admin') {
      router.push('/admin');
    } else if (role === 'operador') {
      router.push('/operador');
    } else {
      router.push('/loja');
    }
  } catch (err) {
    error.value = err.response?.data?.error || 'Erro ao fazer login. Tente novamente.';
  } finally {
    loading.value = false;
  }
};
</script>

<style scoped>
.login-page {
  background: var(--bg-primary);
  min-height: 100vh;
}

.hero {
  background: transparent;
}

.box {
  background: var(--card-bg);
  border-radius: 12px;
  box-shadow: 0 10px 30px var(--shadow-color);
  border: 1px solid var(--border-color);
}

.title {
  color: var(--text-primary);
}

.subtitle {
  color: var(--text-muted);
}

.notification.is-info.is-light {
  background: var(--bg-secondary);
  color: var(--text-primary);
  border: 1px solid var(--border-color);
}

.label {
  color: var(--text-primary);
}

.input {
  background: var(--bg-secondary);
  border-color: var(--border-color);
  color: var(--text-primary);
}

.input::placeholder {
  color: var(--text-muted);
}

.input:focus {
  border-color: var(--accent-primary);
  box-shadow: 0 0 0 2px rgba(74, 107, 133, 0.2);
}

.has-text-grey {
  color: var(--text-muted) !important;
}

.has-text-grey:hover {
  color: var(--text-primary) !important;
}
</style>
