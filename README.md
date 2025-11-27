# Portal de Pedidos do Parceiro

Mono-repo para o portal de pedidos solicitado, preparado para execução local e deploy no [Render](https://render.com/). O objetivo é suportar três perfis principais (Administrador do Sistema, Operador do Sistema e Loja Parceira), garantindo catálogo atualizado, gestão de limites e acompanhamento de pedidos.

## Estrutura do repositório

```
/frontend  → SPA em Vue 3 + Vite + Bulma
/backend   → API Node.js + Express + PostgreSQL
render.yaml → Manifesto para provisionar API, frontend e banco no Render
plano.md   → Detalhamento funcional coletado com o cliente
```

## Pré-requisitos

- Node.js 20+
- PostgreSQL 14+ (local ou provisionado pelo Render)
- pnpm/nvm são opcionais; os scripts abaixo utilizam `npm`

## Como executar localmente

### Backend

```powershell
cd "c:\Projetos\Portal de Pedidos do Parceiro\backend"
npm install
cp .env.example .env
# ajuste DATABASE_URL antes de seguir
npm run dev
```

Endpoints disponíveis:
- `GET /` → teste rápido
- `GET /api/health` → verifica conexão com o Postgres (necessita banco configurado)
- `GET /api/catalog` → catálogo público de produtos com busca opcional
- `GET /api/clients` → (admin/operador) lista lojas com limites e status operacionais
- `GET /api/clients/:id` → (admin/operador) detalhes de um cliente específico
- `PUT /api/clients/:id` → (admin/operador) atualiza CNPJ, rota, segmentação, limite e prazos
- `POST /api/orders` → (loja) agora valida limite de crédito e status do cliente antes de registrar
- `POST /api/orders/:id/repeat` → (loja) replica automaticamente os itens de um pedido anterior
- `GET /api/orders/export/csv` → (admin/operador) gera arquivo CSV com os pedidos mais recentes

### Frontend

```powershell
cd "c:\Projetos\Portal de Pedidos do Parceiro\frontend"
npm install
npm run dev
```

O Vite abrirá em `http://localhost:5173` exibindo os perfis e fluxos principais descritos pelo cliente.

## Deploy no Render

✅ **Aplicação publicada:**
- **Frontend**: https://portaldepedidosdoparceiro.onrender.com
- **Backend API**: https://portaldepedidosdoparceiro.onrender.com/api
- **Banco PostgreSQL**: Gerenciado pelo Render

O arquivo `render.yaml` já descreve:
1. **portal-pedidos-api** — serviço Node apontando para o diretório `backend`.
2. **portal-pedidos-frontend** — site estático publicado a partir de `frontend/dist`.
3. **portal-pedidos-db** — banco PostgreSQL compartilhado.

Para futuras atualizações:
- Ajustar secrets adicionais (por exemplo, `JWT_SECRET`, `STORAGE_BUCKET`, etc.) conforme forem surgindo.
- Auto-deploy está habilitado: push para `master` atualiza produção automaticamente.

## Esteira de CI/CD

- Workflow `CI` em `.github/workflows/ci.yml` executa em cada `push`/PR para `master`.
- Job **Backend lint**: Node 20, `npm ci` em `backend/` e `npm run lint` para garantir qualidade do código.
- Job **Frontend build**: depende do backend, roda `npm ci` em `frontend/` seguido de `npm run build` para validar o bundle.
- O cache do npm está configurado para acelerar execuções sucessivas.
- Admins e operadores podem exportar pedidos diretamente nas telas internas (botão “Exportar pedidos (CSV)” no Admin e “Exportar CSV” no Operador, ambos usando o endpoint acima).

## Próximos passos sugeridos

1. Modelar esquemas pendentes do banco (histórico ABC, logs e auditoria).
2. Definir autenticação (SSO Winthor ou OAuth interno) e perfis de acesso finos.
3. Ampliar o fluxo de pedidos (exportação, notificações e integração Winthor).
4. Acrescentar testes automatizados (unitários, integração e E2E) e expandir a esteira.

Consulte `plano.md` para o detalhamento funcional acordado até o momento.
