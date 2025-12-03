<template>
  <div class="homepage">
    <!-- Hero Section -->
    <section class="hero-section">
      <div class="container hero-content">
        <div class="columns is-vcentered">
          <div class="column is-7">
            <h1 class="title is-1 has-text-white mb-3">Portal de Pedidos</h1>
            <p class="subtitle is-5 has-text-white-bis">
              Faça seus pedidos de forma rápida e acompanhe o status em tempo real.
            </p>
            <div class="cta-buttons mt-5">
              <router-link to="/login" class="button is-white is-medium">
                <span class="icon"><i class="fas fa-sign-in-alt"></i></span>
                <span>Entrar</span>
              </router-link>
              <router-link to="/catalog" class="button is-light is-outlined is-medium">
                <span class="icon"><i class="fas fa-box-open"></i></span>
                <span>Ver Catálogo</span>
              </router-link>
            </div>
          </div>
          <div class="column is-5 has-text-centered">
            <div class="hero-icon">
              <i class="fas fa-boxes"></i>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Features Section -->
    <section class="section features-section">
      <div class="container">
        <div class="has-text-centered mb-6">
          <h2 class="title is-3">Como funciona</h2>
          <p class="subtitle is-6 has-text-grey">Simples, rápido e eficiente</p>
        </div>
        
        <div class="columns is-multiline">
          <div class="column is-4">
            <div class="feature-card">
              <div class="feature-icon">
                <i class="fas fa-search"></i>
              </div>
              <h3 class="title is-5">Explore o Catálogo</h3>
              <p class="has-text-grey">
                Navegue pelos produtos disponíveis, veja preços e estoque em tempo real.
              </p>
            </div>
          </div>
          <div class="column is-4">
            <div class="feature-card">
              <div class="feature-icon">
                <i class="fas fa-cart-plus"></i>
              </div>
              <h3 class="title is-5">Monte seu Pedido</h3>
              <p class="has-text-grey">
                Adicione itens ao carrinho e escolha a condição de pagamento ideal.
              </p>
            </div>
          </div>
          <div class="column is-4">
            <div class="feature-card">
              <div class="feature-icon">
                <i class="fas fa-truck"></i>
              </div>
              <h3 class="title is-5">Acompanhe a Entrega</h3>
              <p class="has-text-grey">
                Receba notificações e acompanhe cada etapa do seu pedido.
              </p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Benefits Section -->
    <section class="section benefits-section">
      <div class="container">
        <div class="columns is-vcentered">
          <div class="column is-6">
            <h2 class="title is-3">Vantagens do Portal</h2>
            <div class="benefit-list">
              <div class="benefit-item">
                <span class="icon has-text-success"><i class="fas fa-check-circle"></i></span>
                <span>Pedidos 24 horas por dia, 7 dias por semana</span>
              </div>
              <div class="benefit-item">
                <span class="icon has-text-success"><i class="fas fa-check-circle"></i></span>
                <span>Histórico completo de compras</span>
              </div>
              <div class="benefit-item">
                <span class="icon has-text-success"><i class="fas fa-check-circle"></i></span>
                <span>Repetir pedidos anteriores com um clique</span>
              </div>
              <div class="benefit-item">
                <span class="icon has-text-success"><i class="fas fa-check-circle"></i></span>
                <span>Limite de crédito disponível em tempo real</span>
              </div>
              <div class="benefit-item">
                <span class="icon has-text-success"><i class="fas fa-check-circle"></i></span>
                <span>Condições de pagamento flexíveis</span>
              </div>
            </div>
          </div>
          <div class="column is-6 has-text-centered">
            <div class="benefits-illustration">
              <i class="fas fa-chart-line"></i>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Demo Logins Section -->
    <section class="section demo-section">
      <div class="container">
        <div class="has-text-centered mb-5">
          <span class="tag is-info is-light mb-3">Ambiente de Demonstração</span>
          <h2 class="title is-3">Teste o Sistema</h2>
          <p class="subtitle is-6 has-text-grey">
            Use as credenciais abaixo para explorar cada perfil
          </p>
        </div>
        
        <div class="columns is-centered">
          <div class="column is-10">
            <div class="box demo-box">
              <div class="columns is-multiline">
                <!-- Admin -->
                <div class="column is-4">
                  <div class="demo-card">
                    <div class="demo-icon admin">
                      <i class="fas fa-user-shield"></i>
                    </div>
                    <h4 class="title is-6 mb-2">Administrador</h4>
                    <p class="is-size-7 has-text-grey mb-3">Acesso completo ao sistema</p>
                    <div class="credentials">
                      <div class="field">
                        <label class="label is-small">Email</label>
                        <div class="control">
                          <input class="input is-small" readonly value="admin@portalpedidos.com" @click="copyToClipboard('admin@portalpedidos.com', $event)">
                        </div>
                      </div>
                      <div class="field">
                        <label class="label is-small">Senha</label>
                        <div class="control">
                          <input class="input is-small" readonly value="admin123" @click="copyToClipboard('admin123', $event)">
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                
                <!-- Operador -->
                <div class="column is-4">
                  <div class="demo-card">
                    <div class="demo-icon operador">
                      <i class="fas fa-headset"></i>
                    </div>
                    <h4 class="title is-6 mb-2">Operador</h4>
                    <p class="is-size-7 has-text-grey mb-3">Gestão de pedidos e clientes</p>
                    <div class="credentials">
                      <div class="field">
                        <label class="label is-small">Email</label>
                        <div class="control">
                          <input class="input is-small" readonly value="operador@portalpedidos.com" @click="copyToClipboard('operador@portalpedidos.com', $event)">
                        </div>
                      </div>
                      <div class="field">
                        <label class="label is-small">Senha</label>
                        <div class="control">
                          <input class="input is-small" readonly value="operador123" @click="copyToClipboard('operador123', $event)">
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                
                <!-- Loja -->
                <div class="column is-4">
                  <div class="demo-card">
                    <div class="demo-icon loja">
                      <i class="fas fa-store"></i>
                    </div>
                    <h4 class="title is-6 mb-2">Loja / Cliente</h4>
                    <p class="is-size-7 has-text-grey mb-3">Fazer e acompanhar pedidos</p>
                    <div class="credentials">
                      <div class="field">
                        <label class="label is-small">Email</label>
                        <div class="control">
                          <input class="input is-small" readonly value="loja1@cliente.com" @click="copyToClipboard('loja1@cliente.com', $event)">
                        </div>
                      </div>
                      <div class="field">
                        <label class="label is-small">Senha</label>
                        <div class="control">
                          <input class="input is-small" readonly value="cliente123" @click="copyToClipboard('cliente123', $event)">
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              
              <div class="has-text-centered mt-4">
                <p class="is-size-7 has-text-grey">
                  <i class="fas fa-info-circle"></i>
                  Clique nos campos para copiar • Dados de demonstração
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- CTA Section -->
    <section class="section cta-section">
      <div class="container has-text-centered">
        <h2 class="title is-3 has-text-white">Pronto para começar?</h2>
        <p class="subtitle is-5 has-text-white-bis mb-5">
          Acesse sua conta ou explore o catálogo de produtos
        </p>
        <div class="buttons is-centered">
          <router-link to="/login" class="button is-white is-medium">
            <span class="icon"><i class="fas fa-sign-in-alt"></i></span>
            <span>Fazer Login</span>
          </router-link>
          <router-link to="/catalog" class="button is-light is-outlined is-medium">
            <span class="icon"><i class="fas fa-box-open"></i></span>
            <span>Ver Catálogo</span>
          </router-link>
        </div>
      </div>
    </section>

    <!-- Footer -->
    <footer class="footer-section">
      <div class="container has-text-centered">
        <p class="has-text-grey is-size-7">
          Portal de Pedidos do Parceiro © {{ new Date().getFullYear() }}
        </p>
      </div>
    </footer>
  </div>
</template>

<script setup>
const copyToClipboard = async (text, event) => {
  try {
    await navigator.clipboard.writeText(text);
    const input = event.target;
    const originalValue = input.value;
    input.value = '✓ Copiado!';
    input.style.background = 'var(--success, #5a7d6a)';
    input.style.color = '#fff';
    setTimeout(() => {
      input.value = originalValue;
      input.style.background = '';
      input.style.color = '';
    }, 1000);
  } catch {
    console.log('Clipboard não disponível');
  }
};
</script>

<style scoped>
.homepage {
  background: var(--bg-primary);
  color: var(--text-primary);
  min-height: 100vh;
}

/* Hero Section */
.hero-section {
  background: linear-gradient(135deg, var(--navbar-bg, #2b3a4a) 0%, #1e2d3d 100%);
  padding: 6rem 1.5rem 5rem;
}

.hero-content .title.is-1 {
  font-size: 3rem;
  font-weight: 700;
}

.hero-icon {
  font-size: 8rem;
  color: rgba(255, 255, 255, 0.15);
}

.cta-buttons .button {
  margin-right: 1rem;
  border-radius: 10px;
}

/* Features Section */
.features-section {
  background: var(--bg-secondary);
  padding: 4rem 1.5rem;
}

.feature-card {
  background: var(--card-bg);
  border: 1px solid var(--border-color);
  border-radius: 14px;
  padding: 2rem;
  text-align: center;
  height: 100%;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.feature-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 8px 24px var(--shadow-color);
}

.feature-card .feature-icon {
  width: 64px;
  height: 64px;
  border-radius: 50%;
  background: linear-gradient(135deg, var(--accent-color, #3d5a73), var(--accent-dark, #1e2d3d));
  color: #fff;
  font-size: 1.5rem;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 1.5rem;
}

/* Benefits Section */
.benefits-section {
  background: var(--bg-primary);
  padding: 4rem 1.5rem;
}

.benefit-list {
  margin-top: 1.5rem;
}

.benefit-item {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 0.75rem 0;
  font-size: 1rem;
  border-bottom: 1px solid var(--border-color);
}

.benefit-item:last-child {
  border-bottom: none;
}

.benefits-illustration {
  font-size: 10rem;
  color: var(--accent-color, #3d5a73);
  opacity: 0.2;
}

/* Demo Section */
.demo-section {
  background: var(--bg-secondary);
  padding: 4rem 1.5rem;
}

.demo-box {
  background: var(--card-bg);
  border: 1px solid var(--border-color);
  border-radius: 14px;
  padding: 2rem;
}

.demo-card {
  text-align: center;
  padding: 1rem;
}

.demo-icon {
  width: 56px;
  height: 56px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 1rem;
  font-size: 1.25rem;
  color: #fff;
}

.demo-icon.admin {
  background: linear-gradient(135deg, #7d5a5a, #6d4a4a);
}

.demo-icon.operador {
  background: linear-gradient(135deg, #5a6d7d, #4a5d6d);
}

.demo-icon.loja {
  background: linear-gradient(135deg, #8a7d5a, #7a6d4a);
}

.credentials {
  text-align: left;
}

.credentials .input {
  cursor: pointer;
  font-family: monospace;
  font-size: 0.8rem;
}

.credentials .input:hover {
  background: var(--bg-tertiary);
}

/* CTA Section */
.cta-section {
  background: linear-gradient(135deg, var(--navbar-bg, #2b3a4a) 0%, #1e2d3d 100%);
  padding: 4rem 1.5rem;
}

.cta-section .button {
  border-radius: 10px;
}

/* Footer */
.footer-section {
  background: var(--bg-tertiary);
  padding: 2rem 1.5rem;
}

/* Responsive */
@media (max-width: 768px) {
  .hero-content .title.is-1 {
    font-size: 2rem;
  }
  
  .hero-icon {
    font-size: 5rem;
    margin-top: 2rem;
  }
  
  .cta-buttons .button {
    margin-bottom: 0.5rem;
    width: 100%;
  }
}
</style>
