<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name', 'Portal de Pedidos'))</title>
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            width: 260px;
            background: linear-gradient(180deg, #1e3a8a 0%, #1e40af 100%);
            padding: 1.5rem 0;
            z-index: 1000;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
            overflow-y: auto;
        }
        
        .sidebar::-webkit-scrollbar {
            width: 6px;
        }
        
        .sidebar::-webkit-scrollbar-track {
            background: rgba(255,255,255,0.1);
        }
        
        .sidebar::-webkit-scrollbar-thumb {
            background: rgba(255,255,255,0.3);
            border-radius: 3px;
        }
        
        .sidebar-brand {
            color: white;
            font-size: 1.25rem;
            font-weight: 700;
            padding: 0 1.5rem 1.5rem;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            margin-bottom: 1rem;
            text-decoration: none;
            display: block;
        }
        
        .sidebar-brand:hover {
            color: white;
        }
        
        .sidebar-nav {
            padding: 0;
            margin: 0;
            list-style: none;
        }
        
        .sidebar-nav-item {
            padding: 0.5rem 1.5rem;
        }
        
        .sidebar-nav-link {
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            display: flex;
            align-items: center;
            padding: 0.75rem 1rem;
            border-radius: 0.5rem;
            transition: all 0.2s;
            font-size: 0.95rem;
        }
        
        .sidebar-nav-link:hover {
            background: rgba(255,255,255,0.1);
            color: white;
        }
        
        .sidebar-nav-link.active {
            background: rgba(255,255,255,0.15);
            color: white;
            font-weight: 600;
        }
        
        .sidebar-nav-link i {
            margin-right: 0.75rem;
            font-size: 1.1rem;
            width: 20px;
            text-align: center;
        }
        
        .sidebar-divider {
            height: 1px;
            background: rgba(255,255,255,0.1);
            margin: 1rem 1.5rem;
        }
        
        .sidebar-heading {
            color: rgba(255,255,255,0.5);
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 0.5rem 1.5rem;
            margin-top: 1rem;
        }
        
        .main-content {
            margin-left: 260px;
            min-height: 100vh;
            background: #f8f9fa;
            padding: 2rem;
        }
    </style>
</head>
<body>
    @auth
        <!-- Sidebar -->
        <div class="sidebar">
            <a href="{{ route('dashboard') }}" class="sidebar-brand">
                <i class="bi bi-box-seam me-2"></i>Portal de Pedidos
            </a>
            
            <ul class="sidebar-nav">
                <li class="sidebar-nav-item">
                    <a href="{{ route('dashboard') }}" class="sidebar-nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <i class="bi bi-speedometer2"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                
                <li class="sidebar-nav-item">
                    <a href="{{ route('products.index') }}" class="sidebar-nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}">
                        <i class="bi bi-box"></i>
                        <span>Produtos</span>
                    </a>
                </li>
                
                <li class="sidebar-nav-item">
                    <a href="{{ route('orders.index') }}" class="sidebar-nav-link {{ request()->routeIs('orders.*') ? 'active' : '' }}">
                        <i class="bi bi-cart3"></i>
                        <span>Pedidos</span>
                    </a>
                </li>
                
                @if(Auth::user()->role === 'admin')
                    <div class="sidebar-divider"></div>
                    
                    <div class="sidebar-heading">Administração</div>
                    
                    <li class="sidebar-nav-item">
                        <a href="#" class="sidebar-nav-link" onclick="showComingSoon('Usuários'); return false;"
                           data-bs-toggle="tooltip" title="Gerenciar usuários do sistema">
                            <i class="bi bi-people"></i>
                            <span>Usuários</span>
                        </a>
                    </li>
                    
                    <li class="sidebar-nav-item">
                        <a href="#" class="sidebar-nav-link" onclick="showComingSoon('Clientes'); return false;"
                           data-bs-toggle="tooltip" title="Cadastro e gestão de clientes">
                            <i class="bi bi-building"></i>
                            <span>Clientes</span>
                        </a>
                    </li>
                    
                    <li class="sidebar-nav-item">
                        <a href="#" class="sidebar-nav-link" onclick="showComingSoon('Relatórios'); return false;"
                           data-bs-toggle="tooltip" title="Relatórios gerenciais e estatísticas">
                            <i class="bi bi-graph-up"></i>
                            <span>Relatórios</span>
                        </a>
                    </li>
                @endif
                
                <div class="sidebar-divider"></div>
                
                <div class="sidebar-heading">Configurações</div>
                
                <li class="sidebar-nav-item">
                    <a href="{{ route('profile.edit') }}" class="sidebar-nav-link {{ request()->routeIs('profile.*') ? 'active' : '' }}"
                       data-bs-toggle="tooltip" title="Editar dados do perfil">
                        <i class="bi bi-person"></i>
                        <span>Meu Perfil</span>
                    </a>
                </li>
                
                <li class="sidebar-nav-item">
                    <a href="#" class="sidebar-nav-link" onclick="showComingSoon('Configurações'); return false;"
                       data-bs-toggle="tooltip" title="Configurações do sistema">
                        <i class="bi bi-gear"></i>
                        <span>Configurações</span>
                    </a>
                </li>
                
                <li class="sidebar-nav-item">
                    <form method="POST" action="{{ route('logout') }}" id="logoutForm">
                        @csrf
                        <button type="submit" class="sidebar-nav-link w-100 border-0 bg-transparent text-start"
                                data-bs-toggle="tooltip" title="Sair do sistema">
                            <i class="bi bi-box-arrow-right"></i>
                            <span>Sair</span>
                        </button>
                    </form>
                </li>
            </ul>
        </div>
        
        <!-- Main Content -->
        <div class="main-content">
            @yield('content')
        </div>
    @else
        @yield('content')
    @endauth
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Inicializar tooltips globalmente
        document.addEventListener('DOMContentLoaded', function() {
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });
        
        // Toast global para funcionalidades em desenvolvimento
        function showComingSoon(feature) {
            const toastHtml = `
                <div class="position-fixed top-0 end-0 p-3" style="z-index: 9999">
                    <div class="toast show" role="alert" aria-live="assertive" aria-atomic="true">
                        <div class="toast-header bg-info text-white">
                            <i class="bi bi-info-circle me-2"></i>
                            <strong class="me-auto">Em Breve</strong>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"></button>
                        </div>
                        <div class="toast-body">
                            A funcionalidade <strong>${feature}</strong> está em desenvolvimento e estará disponível em breve.
                        </div>
                    </div>
                </div>
            `;
            
            document.body.insertAdjacentHTML('beforeend', toastHtml);
            
            setTimeout(() => {
                const toasts = document.querySelectorAll('.toast');
                toasts.forEach(toast => {
                    toast.classList.remove('show');
                    setTimeout(() => toast.parentElement.remove(), 300);
                });
            }, 3500);
        }
    </script>
</body>
</html>
