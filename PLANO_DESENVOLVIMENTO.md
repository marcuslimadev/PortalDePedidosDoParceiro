# Plano Completo de Desenvolvimento - Portal de Pedidos do Parceiro

## 📊 Status do Projeto: 20% concluído (17/85 funcionalidades)

---

## 1. Infraestrutura & Base (5/5 - 100% ✅)
- [x] Repositório Git mono-repo estruturado
- [x] Backend Node.js + Express + PostgreSQL
- [x] Frontend Vue 3 + Vite + Bulma
- [x] Deploy configurado no Render (render.yaml)
- [x] Variáveis de ambiente e segurança (JWT_SECRET)

## 2. Autenticação & Autorização (6/8 - 75% ✅)
- [x] Tabela de usuários com roles (admin, operador, loja)
- [x] Endpoints de login e registro
- [x] Middleware JWT e proteção de rotas
- [x] Telas de login e dashboards por perfil
- [x] Router com guards de autenticação
- [x] Serviço API com interceptors
- [ ] Recuperação de senha
- [ ] Autenticação 2FA (opcional)

## 3. Gestão de Produtos (6/12 - 50%)
- [x] Modelo de dados: produtos (código, descrição, preço, unidade, tributação)
- [x] CRUD de produtos (backend)
- [ ] Upload e galeria de fotos por produto
- [x] Validação de campos obrigatórios Winthor
- [x] Interface de cadastro de produtos (Admin)
- [x] Interface de edição de produtos (Admin/Operador)
- [x] Listagem de produtos com filtros e busca
- [ ] Importação em massa (CSV/Excel)
- [ ] Histórico de alterações de preços
- [ ] Controle de estoque básico
- [ ] Categorização de produtos
- [ ] API pública de catálogo para lojas

## 4. Gestão de Clientes (0/10 - 0%)
- [ ] Modelo de dados: clientes (CNPJ, IE, rota, segmentação Winthor)
- [ ] CRUD de clientes (backend)
- [ ] Campos padrão Winthor obrigatórios
- [ ] Interface de cadastro de clientes (Admin)
- [ ] Gestão de limites de crédito por cliente
- [ ] Prazos de pagamento personalizados (30/60/90)
- [ ] Associação cliente-usuário portal
- [ ] Histórico de limites e alterações
- [ ] Validação de CNPJ/IE
- [ ] Status do cliente (ativo/inativo/bloqueado)

## 5. Sistema de Pedidos (0/15 - 0%)
- [ ] Modelo de dados: pedidos, itens_pedido
- [ ] API de criação de pedido
- [ ] Carrinho de compras (frontend)
- [ ] Validação de limite de crédito ao fazer pedido
- [ ] Cálculo de totais e impostos
- [ ] Aplicação de condições de pagamento
- [ ] Interface de pedidos para lojas
- [ ] Funcionalidade "Repetir pedido anterior"
- [ ] Acompanhamento de status do pedido
- [ ] Histórico completo de pedidos
- [ ] Cancelamento de pedidos
- [ ] Aprovação de pedidos (workflow)
- [ ] Exportação de pedidos para Excel
- [ ] Notificações de status (email/sistema)
- [ ] Dashboard de pedidos em aberto

## 6. Integração Winthor (0/8 - 0%)
- [ ] API de sincronização bidirecional
- [ ] Importação de produtos do Winthor
- [ ] Importação de clientes do Winthor
- [ ] Exportação de pedidos para Winthor
- [ ] Sincronização de status de pedidos
- [ ] Sincronização de limites de crédito
- [ ] Logs de sincronização
- [ ] Resolução de conflitos de dados

## 7. Relatórios & Analytics (0/9 - 0%)
- [ ] Curva ABC de produtos
- [ ] Curva ABC de clientes
- [ ] Relatório de vendas por loja
- [ ] Relatório de vendas por período
- [ ] Dashboard administrativo com KPIs
- [ ] Exportação de relatórios (PDF/Excel)
- [ ] Gráficos de tendências
- [ ] Análise de pedidos cancelados
- [ ] Relatório de limites utilizados

## 8. UX & Mobile (0/6 - 0%)
- [ ] Design responsivo completo (mobile-first)
- [ ] Menu de navegação adaptativo
- [ ] Otimização de performance
- [ ] PWA (Progressive Web App)
- [ ] Modo offline básico
- [ ] Testes de usabilidade

## 9. Segurança & Compliance (0/7 - 0%)
- [ ] Auditoria de ações (logs de sistema)
- [ ] Política de senhas fortes
- [ ] Rate limiting nas APIs
- [ ] Proteção contra SQL injection
- [ ] Proteção CSRF
- [ ] HTTPS obrigatório em produção
- [ ] Backup automático do banco

## 10. DevOps & Qualidade (0/5 - 0%)
- [ ] Testes unitários (backend)
- [ ] Testes de integração
- [ ] Testes E2E (frontend)
- [ ] CI/CD pipeline (GitHub Actions)
- [ ] Monitoramento e alertas (Render/Sentry)

---

## 📈 Resumo por Módulo

| Módulo | Progresso | Status |
|--------|-----------|--------|
| Infraestrutura & Base | 5/5 (100%) | ✅ Completo |
| Autenticação & Autorização | 6/8 (75%) | 🟡 Em andamento |
| Gestão de Produtos | 6/12 (50%) | 🟡 Em andamento |
| Gestão de Clientes | 0/10 (0%) | ⚪ Não iniciado |
| Sistema de Pedidos | 0/15 (0%) | ⚪ Não iniciado |
| Integração Winthor | 0/8 (0%) | ⚪ Não iniciado |
| Relatórios & Analytics | 0/9 (0%) | ⚪ Não iniciado |
| UX & Mobile | 0/6 (0%) | ⚪ Não iniciado |
| Segurança & Compliance | 0/7 (0%) | ⚪ Não iniciado |
| DevOps & Qualidade | 0/5 (0%) | ⚪ Não iniciado |

**Total Geral: 17/85 funcionalidades (20%)**

---

## 🎯 Próximas Prioridades (Sprint 1)

1. **Gestão de Produtos** - CRUD completo + fotos
2. **Gestão de Clientes** - CRUD + limites de crédito
3. **Sistema de Pedidos MVP** - Carrinho + criação de pedido
4. **Responsividade Mobile** - Otimizar para tablets/smartphones

## 📅 Roadmap Estimado

- **Sprint 1 (Semanas 1-2)**: Produtos + Clientes
- **Sprint 2 (Semanas 3-4)**: Pedidos + Carrinho
- **Sprint 3 (Semanas 5-6)**: Integração Winthor
- **Sprint 4 (Semanas 7-8)**: Relatórios + Mobile
- **Sprint 5 (Semanas 9-10)**: Testes + Refinamentos
