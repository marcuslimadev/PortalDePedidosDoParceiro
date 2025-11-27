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
pm install
cp .env.example .env
# ajuste DATABASE_URL antes de seguir
npm run dev
```

Endpoints disponíveis:
- `GET /` → teste rápido
- `GET /api/health` → verifica conexão com o Postgres (necessita banco configurado)
- `GET /api/catalog` → catálogo público de produtos com busca opcional

### Frontend

```powershell
cd "c:\Projetos\Portal de Pedidos do Parceiro\frontend"
pm install
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

## Próximos passos sugeridos

1. Modelar esquemas do banco (clientes, produtos, pedidos, limites, histórico ABC).
2. Definir autenticação (SSO Winthor ou OAuth interno) e perfis de acesso finos.
3. Implementar fluxo completo de pedidos (carrinho, aprovação, acompanhamento do status no Winthor).
4. Acrescentar testes automatizados (unitários e e2e) e pipeline CI.

Consulte `plano.md` para o detalhamento funcional acordado até o momento.
