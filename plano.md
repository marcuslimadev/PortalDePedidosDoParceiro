# Plano funcional — Portal de Pedidos do Parceiro

## Visão geral

O portal será uma plataforma única (mono-repo) conectada ao ERP Winthor para gestão completa do ciclo de pedidos entre distribuidores e lojas parceiras. O sistema precisa garantir cadastro consistente de produtos/clientes, aplicação de limites comerciais, acompanhamento de pedidos e relatórios operacionais.

## Perfis e responsabilidades

### Administrador do Sistema
- **Cadastro de produtos**: validar campos obrigatórios vindos do Winthor (códigos, tributação, unidade), incluir precificação e fotos.
- **Prazos de pagamento**: definir condições padrão (30/60/90 dias) e personalizações por cliente.
- **Cadastro de clientes**:
  - Preencher campos padrão Winthor (CNPJ, IE, rota, segmentação etc.).
  - Determinar limites financeiros dentro do portal (posteriormente replicados no Winthor).
  - Ajustar perfis/permissões de uso da ferramenta para cada loja ou usuário.
- **Experiência mobile**: garantir responsividade completa para tablets e smartphones.
- **Relatórios**: disponibilizar curva ABC e consolidados por loja, com exportação.
- **Histórico de pedidos**: permitir consulta dos pedidos realizados e exportação em Excel.

### Operador do Sistema
- **Gerenciar limites** definidos pelo Administrador e ajustar com base em comportamento de crédito.
- **Cadastro de produtos**: manter catálogo alinhado com os campos obrigatórios do Winthor.
- **Cadastro de clientes**: validar dados e garantir consistência com o ERP.
- **Suporte a sincronização futura**: preparar dados para posterior integração direta.

### Loja Parceira
- **Catálogo**: visualizar produtos, descrição, preço e fotos atualizados.
- **Pedidos**: abrir novos pedidos, repetir pedidos anteriores e acompanhar histórico.
- **Status**: consultar situação dos pedidos (posteriormente alimentada pelo Winthor).
- **Exportação**: baixar histórico em Excel para análises locais.

## Requisitos não funcionais
- **Mono-repo** com `frontend` (Vue + Bulma) e `backend` (Node + Express) apontando para o mesmo controle de versão.
- **Banco de dados** PostgreSQL central, com migrações futuras para entidades (produtos, clientes, pedidos, limites, perfis, histórico).
- **Deploy no Render** com Manifesto `render.yaml` orquestrando API, SPA e banco.
- **Suporte mobile-first** no frontend.
- **Observabilidade inicial**: rota `/api/health` para monitorar API e conexão com Postgres.

## Roadmap inicial
1. **MVP Catálogo/Pedidos**
   - UI responsiva com navegação por perfil e protótipo funcional dos fluxos principais.
   - API com endpoints de produtos e pedidos simples.
2. **Integração Winthor**
   - Sincronização de cadastros (produtos/clientes) e status dos pedidos.
   - Importação automática de limites/prazos.
3. **Relatórios e Exportações**
   - Curva ABC por loja, dashboards e exportação CSV/Excel.
4. **Governança e Segurança**
   - RBAC detalhado, auditoria e logs.

Este documento deve ser atualizado a cada ciclo de discovery com o cliente.
