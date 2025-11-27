<template>
  <div class="homepage">
    <section class="hero-section">
      <div class="hero-layer"></div>
      <div class="container hero-content">
        <div class="columns is-vcentered">
          <div class="column is-7">
            <p class="eyebrow">
              <span class="icon"><i class="fas fa-sitemap"></i></span>
              Portal de Pedidos do Parceiro
            </p>
            <h1 class="title is-1 has-text-white mb-3">Status em tempo real</h1>
            <p class="subtitle is-5 has-text-white-bis">
              Acompanhe a evolucao do projeto, metas por modulo e o que ja esta pronto para producao.
            </p>
            <div class="cta-buttons">
              <router-link to="/login" class="button is-white is-medium">
                <span class="icon"><i class="fas fa-sign-in-alt"></i></span>
                <span>Entrar</span>
              </router-link>
              <router-link to="/catalog" class="button is-light is-outlined is-medium">
                <span class="icon"><i class="fas fa-box-open"></i></span>
                <span>Ver catalogo</span>
              </router-link>
            </div>
          </div>
          <div class="column is-5">
            <div class="glass-card kpi-card">
              <div class="is-flex is-justify-content-space-between is-align-items-center mb-3">
                <p class="is-size-6 has-text-grey-light">Progresso geral</p>
                <span class="tag is-info is-light">Atualizado</span>
              </div>
              <div class="kpi-number">
                <span class="kpi-value">{{ projectProgress }}%</span>
                <span class="kpi-sub">concluido</span>
              </div>
              <progress class="progress is-info is-large" :value="projectProgress" max="100">{{ projectProgress }}%</progress>
              <p class="is-size-6 has-text-grey-light mt-2">
                {{ completedFeatures }} / {{ totalFeatures }} funcionalidades entregues
              </p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section class="section stats-section">
      <div class="container">
        <div class="columns is-multiline">
          <div v-for="module in moduleStatus" :key="module.name" class="column is-4-desktop is-6-tablet">
            <div class="glass-card module-card">
              <div class="is-flex is-justify-content-space-between is-align-items-center mb-2">
                <h3 class="title is-5 mb-0">{{ module.name }}</h3>
                <span class="tag" :class="module.tagClass">{{ module.label }}</span>
              </div>
              <p class="is-size-6 has-text-grey">{{ module.done }} / {{ module.total }} entregues</p>
              <div class="pill-bar">
                <div class="pill-fill" :style="{ width: module.progress + '%', background: module.color }"></div>
              </div>
              <div class="is-flex is-justify-content-space-between is-align-items-center mt-2">
                <span class="is-size-7 has-text-grey">Progresso</span>
                <span class="is-size-6 has-text-weight-semibold">{{ module.progress }}%</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section class="section highlight-section">
      <div class="container">
        <div class="columns is-multiline">
          <div class="column is-4">
            <div class="feature-tile">
              <div class="feature-icon primary">
                <i class="fas fa-users-cog"></i>
              </div>
              <div>
                <p class="title is-5 mb-1">Admin e Operador</p>
                <p class="is-size-6 has-text-grey">
                  Cadastre produtos, defina prazos e ajuste limites com historico completo.
                </p>
              </div>
            </div>
          </div>
          <div class="column is-4">
            <div class="feature-tile">
              <div class="feature-icon info">
                <i class="fas fa-shopping-cart"></i>
              </div>
              <div>
                <p class="title is-5 mb-1">Lojas no comando</p>
                <p class="is-size-6 has-text-grey">
                  Pedido rapido, repetir compras e acompanhar status via SSE em tempo real.
                </p>
              </div>
            </div>
          </div>
          <div class="column is-4">
            <div class="feature-tile">
              <div class="feature-icon success">
                <i class="fas fa-chart-line"></i>
              </div>
              <div>
                <p class="title is-5 mb-1">Prontos para escala</p>
                <p class="is-size-6 has-text-grey">
                  Catalogo publico, exportacoes, dashboard de pendencias e roadmap de notificacoes.
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section class="section cta-section">
      <div class="container has-text-centered">
        <p class="eyebrow is-italic">Experiencia unificada</p>
        <h2 class="title is-3 mb-3">Pronto para testar?</h2>
        <p class="subtitle is-6 has-text-grey mb-4">
          Acesse com seu perfil ou explore o catalogo publico para ver o estado atual do projeto.
        </p>
        <div class="buttons is-centered">
          <router-link to="/login" class="button is-primary is-medium">
            <span class="icon"><i class="fas fa-user-circle"></i></span>
            <span>Entrar no portal</span>
          </router-link>
          <router-link to="/catalog" class="button is-light is-medium">
            <span class="icon"><i class="fas fa-eye"></i></span>
            <span>Ver catalogo</span>
          </router-link>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup>
import { computed, ref } from 'vue';

const totalFeatures = ref(85);
const completedFeatures = ref(57);
const projectProgress = computed(() => Math.round((completedFeatures.value / totalFeatures.value) * 100));

const moduleStatus = computed(() => [
  {
    name: 'Autenticacao & Autorizacao',
    done: 8,
    total: 8,
    label: 'Completo',
    color: 'linear-gradient(90deg, #22c55e, #16a34a)',
    tagClass: 'is-success'
  },
  {
    name: 'Gestao de Produtos',
    done: 11,
    total: 12,
    label: 'Catalogo + API publica',
    color: 'linear-gradient(90deg, #60a5fa, #3b82f6)',
    tagClass: 'is-info'
  },
  {
    name: 'Gestao de Clientes',
    done: 10,
    total: 10,
    label: 'Completo',
    color: 'linear-gradient(90deg, #22c55e, #16a34a)',
    tagClass: 'is-success'
  },
  {
    name: 'Sistema de Pedidos',
    done: 12,
    total: 15,
    label: 'Historico + aprovacao',
    color: 'linear-gradient(90deg, #06b6d4, #0ea5e9)',
    tagClass: 'is-primary'
  },
  {
    name: 'Total do Projeto',
    done: completedFeatures.value,
    total: totalFeatures.value,
    label: `${projectProgress.value}% concluido`,
    color: 'linear-gradient(90deg, #6366f1, #8b5cf6)',
    tagClass: 'is-link'
  }
].map(module => ({
  ...module,
  progress: Math.round((module.done / module.total) * 100)
})));
</script>

<style scoped>
.homepage {
  background: #0b1221;
  color: #0f172a;
}

.hero-section {
  position: relative;
  padding: 5rem 0 3rem;
  overflow: hidden;
  background: radial-gradient(circle at 20% 20%, rgba(99,102,241,0.2), transparent 25%),
              radial-gradient(circle at 80% 10%, rgba(14,165,233,0.25), transparent 30%),
              linear-gradient(135deg, #0f172a 0%, #0b1221 45%, #0f172a 100%);
}

.hero-layer {
  position: absolute;
  inset: 0;
  background: linear-gradient(180deg, rgba(255,255,255,0.03), rgba(255,255,255,0));
  pointer-events: none;
}

.hero-content {
  position: relative;
  z-index: 2;
}

.eyebrow {
  letter-spacing: 0.08em;
  text-transform: uppercase;
  font-size: 0.8rem;
  color: #93c5fd;
  display: inline-flex;
  align-items: center;
  gap: 0.4rem;
}

.cta-buttons .button {
  margin-right: 0.75rem;
}

.glass-card {
  background: rgba(255, 255, 255, 0.08);
  border: 1px solid rgba(255, 255, 255, 0.08);
  border-radius: 18px;
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.25);
  padding: 1.75rem;
  backdrop-filter: blur(10px);
}

.kpi-card .progress {
  height: 12px;
  border-radius: 6px;
  background: rgba(255, 255, 255, 0.1);
}

.kpi-number {
  display: flex;
  align-items: baseline;
  gap: 0.5rem;
}

.kpi-value {
  font-size: 3rem;
  font-weight: 800;
  color: #e0f2fe;
}

.kpi-sub {
  font-size: 0.9rem;
  color: #cbd5f5;
}

.stats-section {
  padding: 3rem 0;
}

.module-card {
  background: #0f172a;
  color: #e2e8f0;
  border: 1px solid rgba(255, 255, 255, 0.06);
}

.module-card .title {
  color: #e2e8f0;
}

.pill-bar {
  width: 100%;
  height: 10px;
  background: rgba(226, 232, 240, 0.15);
  border-radius: 999px;
  overflow: hidden;
}

.pill-fill {
  height: 100%;
  border-radius: 999px;
  transition: width 0.4s ease;
}

.highlight-section {
  padding: 3rem 0;
}

.feature-tile {
  background: #0f172a;
  border: 1px solid rgba(255, 255, 255, 0.06);
  border-radius: 16px;
  padding: 1.5rem;
  display: flex;
  gap: 1rem;
  align-items: flex-start;
  box-shadow: 0 15px 30px rgba(0, 0, 0, 0.2);
}

.feature-icon {
  width: 48px;
  height: 48px;
  border-radius: 12px;
  display: grid;
  place-items: center;
  color: #fff;
  font-size: 1.2rem;
}

.feature-icon.primary { background: linear-gradient(135deg, #22c55e, #16a34a); }
.feature-icon.info { background: linear-gradient(135deg, #0ea5e9, #22d3ee); }
.feature-icon.success { background: linear-gradient(135deg, #6366f1, #8b5cf6); }

.cta-section {
  padding: 4rem 0 5rem;
  background: linear-gradient(180deg, #0b1221 0%, #0f172a 60%, #0b1221 100%);
}

.cta-section .button {
  margin: 0 0.5rem;
}

.title,
.subtitle,
.box,
.tag,
.button {
  letter-spacing: 0.01em;
}
</style>
