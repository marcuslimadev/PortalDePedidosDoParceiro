# 🚀 Guia Rápido - Portal de Pedidos Laravel

## 📦 Instalação Inicial

```powershell
# Navegar para o projeto
cd C:\xampp\htdocs\PortalDePedidosDoParceiro\laravel

# Executar setup automático
.\setup.ps1

# Instalar dependências Node
npm install --force

# Compilar frontend
npm run build

# Iniciar servidor
php artisan serve --port=8000
```

## 🌐 URLs

- **Aplicação:** http://localhost:8000
- **phpMyAdmin:** http://localhost/phpmyadmin
- **Virtual Host:** http://portal-pedidos.local (após configurar)

## 👤 Credenciais de Teste

```
Admin:    admin@portalpedidos.com      / admin123
Operador: operador@portalpedidos.com   / operador123
Loja 1:   loja1@cliente.com            / cliente123
Loja 2:   loja2@cliente.com            / cliente123
Loja 3:   loja3@cliente.com            / cliente123
```

## 🔧 Comandos Artisan Essenciais

### Database
```powershell
# Rodar migrations
php artisan migrate

# Resetar banco (CUIDADO: apaga tudo!)
php artisan migrate:fresh

# Resetar e popular
php artisan migrate:fresh --seed

# Rodar apenas seeders
php artisan db:seed

# Rollback última migration
php artisan migrate:rollback

# Ver status das migrations
php artisan migrate:status
```

### Cache
```powershell
# Limpar todo cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Otimizar para produção
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Desenvolvimento
```powershell
# Listar rotas
php artisan route:list

# Ver rotas de um controller
php artisan route:list --name=orders

# Tinker (console interativo)
php artisan tinker
>>> User::count()
>>> Order::where('status', 'pendente')->get()

# Ver logs em tempo real
Get-Content storage\logs\laravel.log -Tail 50 -Wait

# Gerar key
php artisan key:generate
```

### Generators
```powershell
# Criar migration
php artisan make:migration create_exemplo_table

# Criar model
php artisan make:model Exemplo

# Criar model + migration
php artisan make:model Exemplo -m

# Criar controller
php artisan make:controller ExemploController

# Criar middleware
php artisan make:middleware ExemploMiddleware

# Criar seeder
php artisan make:seeder ExemploSeeder

# Criar service (manual)
# Criar em: app/Services/ExemploService.php
```

## 🎨 Comandos NPM

```powershell
# Instalar dependências
npm install --force

# Desenvolvimento (watch mode)
npm run dev

# Build para produção
npm run build

# Ver pacotes instalados
npm list --depth=0

# Atualizar pacote
npm update bulma
```

## 🗄️ MySQL (via PowerShell)

```powershell
# Conectar ao MySQL
C:\xampp\mysql\bin\mysql.exe -u root

# Criar banco
C:\xampp\mysql\bin\mysql.exe -u root -e "CREATE DATABASE portal_pedidos;"

# Dropar banco (CUIDADO!)
C:\xampp\mysql\bin\mysql.exe -u root -e "DROP DATABASE portal_pedidos;"

# Backup banco
C:\xampp\mysql\bin\mysqldump.exe -u root portal_pedidos > backup.sql

# Restaurar banco
C:\xampp\mysql\bin\mysql.exe -u root portal_pedidos < backup.sql
```

## 🐛 Debug & Troubleshooting

### Ver últimos logs
```powershell
Get-Content storage\logs\laravel.log -Tail 50
```

### Erro "Class not found"
```powershell
composer dump-autoload
```

### Erro "Mixed Content" (HTTP/HTTPS)
```powershell
# Adicionar ao .env
ASSET_URL=http://localhost:8000
```

### Erro "CSRF token mismatch"
```powershell
php artisan config:clear
# Limpar cookies do navegador
```

### Erro "Too many connections"
```powershell
# No MySQL: SET GLOBAL max_connections = 200;
```

### Frontend não atualiza
```powershell
npm run build
php artisan view:clear
# Ctrl+Shift+R no navegador (hard refresh)
```

## 📊 Queries Úteis (Tinker)

```php
// Entrar no Tinker
php artisan tinker

// Contar usuários por role
User::selectRaw('role, COUNT(*) as total')->groupBy('role')->get();

// Ver pedidos de uma loja
Order::where('loja_id', 4)->with('items.product')->get();

// Ver crédito disponível de todas lojas
User::where('role', 'loja')->get(['name', 'credit_limit', 'credit_used']);

// Criar notificação manualmente
Notification::create([
    'user_id' => 1,
    'type' => 'test',
    'title' => 'Teste',
    'body' => 'Mensagem de teste'
]);

// Ver últimas ações de auditoria
AuditLog::orderBy('created_at', 'desc')->take(10)->get();

// Total de pedidos por status
Order::selectRaw('status, COUNT(*) as total, SUM(total) as soma')
    ->groupBy('status')
    ->get();
```

## 🔐 Permissões (Spatie)

```php
// Criar role
use Spatie\Permission\Models\Role;
Role::create(['name' => 'gerente']);

// Atribuir role
$user->assignRole('admin');

// Verificar role
$user->hasRole('admin'); // true/false
$user->getRoleNames(); // ['admin']

// Remover role
$user->removeRole('admin');
```

## 📁 Estrutura de Arquivos

```
laravel/
├── app/
│   ├── Http/Controllers/     # Controllers
│   ├── Models/                # Eloquent Models
│   └── Services/              # Business Logic
├── database/
│   ├── migrations/            # Schema migrations
│   └── seeders/               # Data seeders
├── resources/
│   ├── js/Pages/              # Inertia pages (Vue)
│   └── css/app.css            # Estilos (Bulma)
├── routes/web.php             # Rotas da aplicação
├── .env                       # Configurações
└── storage/logs/              # Logs
```

## 🌐 Virtual Host (Opcional)

### 1. Editar httpd-vhosts.conf
```
C:\xampp\apache\conf\extra\httpd-vhosts.conf
```

Adicionar:
```apache
<VirtualHost *:80>
    DocumentRoot "C:/xampp/htdocs/PortalDePedidosDoParceiro/laravel/public"
    ServerName portal-pedidos.local
    <Directory "C:/xampp/htdocs/PortalDePedidosDoParceiro/laravel/public">
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

### 2. Editar hosts (como Admin)
```
C:\Windows\System32\drivers\etc\hosts
```

Adicionar:
```
127.0.0.1 portal-pedidos.local
```

### 3. Reiniciar Apache
No XAMPP Control Panel: Stop → Start Apache

### 4. Acessar
```
http://portal-pedidos.local
```

## 🚦 Checklist Diário

### Antes de começar
- [ ] XAMPP rodando (Apache + MySQL)
- [ ] Banco `portal_pedidos` existe
- [ ] Migrations rodadas (`php artisan migrate:status`)

### Para desenvolver
- [ ] Servidor Laravel rodando (`php artisan serve`)
- [ ] NPM watch mode (`npm run dev`) OU build feito (`npm run build`)
- [ ] Logs abertos (`Get-Content storage\logs\laravel.log -Tail 50 -Wait`)

### Antes de commitar
- [ ] Build de produção (`npm run build`)
- [ ] Cache limpo (`php artisan cache:clear`)
- [ ] Sem erros (`php artisan route:list`)
- [ ] Tests passando (quando tiver)

## 💡 Dicas

### Performance
- Use eager loading: `Order::with('items.product')->get()`
- Cache queries longas: `Cache::remember('key', 3600, fn() => ...)`
- Otimize índices no banco

### Segurança
- NUNCA commitar `.env`
- Sempre validar input: `$request->validate([...])`
- Use transações para operações críticas
- Sanitize user input no frontend

### Boas Práticas
- Um controller, uma responsabilidade
- Services para lógica de negócio
- Policies para autorização complexa
- Events para ações assíncronas

---

**Desenvolvido com ❤️ usando Laravel 11**
