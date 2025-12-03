# 🐳 Portal de Pedidos - Docker Setup

## Requisitos

- [Docker Desktop](https://www.docker.com/products/docker-desktop) instalado
- Windows 10/11 com WSL2 habilitado (para Docker Desktop)

## Início Rápido

### Opção 1: Usar o Script PowerShell (Recomendado)

```powershell
.\start-docker.ps1
```

O script vai:
1. ✅ Verificar se Docker está instalado
2. 🛑 Parar containers antigos
3. 🔨 Construir as imagens
4. 🚀 Iniciar todos os serviços
5. ⏳ Aguardar 30 segundos
6. 🌐 Abrir o navegador automaticamente

### Opção 2: Comandos Manuais

```powershell
# Iniciar todos os serviços
docker-compose up -d --build

# Ver logs
docker-compose logs -f

# Parar tudo
docker-compose down

# Reiniciar um serviço específico
docker-compose restart backend
```

## Serviços

| Serviço | URL | Descrição |
|---------|-----|-----------|
| Frontend | http://localhost:5173 | Interface Vue.js |
| Backend API | http://localhost:3000/api | REST API |
| PostgreSQL | localhost:5432 | Banco de dados |

## Credenciais Padrão

**Admin:**
- Email: `admin@portalpedidos.com`
- Senha: `admin123`

**Operador:**
- Email: `operador@portalpedidos.com`
- Senha: `operador123`

**Lojas (3 clientes de teste):**
- Email: `loja1@cliente.com`, `loja2@cliente.com`, `loja3@cliente.com`
- Senha: `cliente123`

## Dados Mock

O backend cria automaticamente:
- ✅ 15 produtos no catálogo
- ✅ 3 clientes (lojas)
- ✅ 3 pedidos de exemplo
- ✅ 1 admin + 1 operador

## Troubleshooting

### Porta já em uso

Se a porta 3000, 5173 ou 5432 já estiver em uso:

```powershell
# Parar containers
docker-compose down

# Verificar o que está usando a porta
netstat -ano | findstr :3000
netstat -ano | findstr :5173
netstat -ano | findstr :5432

# Matar o processo (substitua <PID> pelo número encontrado)
Stop-Process -Id <PID> -Force
```

### Limpar tudo e recomeçar

```powershell
# Para e remove containers + volumes
docker-compose down -v

# Remove imagens
docker-compose down --rmi all

# Reconstrói tudo do zero
docker-compose up -d --build
```

### Backend não conecta no banco

```powershell
# Verificar logs do PostgreSQL
docker-compose logs postgres

# Verificar logs do backend
docker-compose logs backend

# Restart do backend
docker-compose restart backend
```

### Ver logs em tempo real

```powershell
# Todos os serviços
docker-compose logs -f

# Apenas backend
docker-compose logs -f backend

# Apenas frontend
docker-compose logs -f frontend
```

## Desenvolvimento

Os volumes estão mapeados para hot-reload:

- `./backend/src` → `/app/src` (backend)
- `./frontend/src` → `/app/src` (frontend)

Qualquer alteração no código é refletida automaticamente!

## Banco de Dados

### Acessar PostgreSQL

```powershell
docker-compose exec postgres psql -U postgres -d portal_pedidos
```

### Backup do banco

```powershell
docker-compose exec postgres pg_dump -U postgres portal_pedidos > backup.sql
```

### Restaurar backup

```powershell
docker-compose exec -T postgres psql -U postgres portal_pedidos < backup.sql
```

## Parar os Serviços

```powershell
# Parar mas manter dados
docker-compose stop

# Parar e remover containers (mantém volumes)
docker-compose down

# Parar e remover TUDO (incluindo banco de dados)
docker-compose down -v
```

## Produção vs Desenvolvimento

Este setup é para **desenvolvimento local**. Para produção, use:
- Render.com (já configurado)
- Frontend: https://portaldepedidosdoparceiro.onrender.com
- Backend: https://portal-pedidos-api.onrender.com/api
