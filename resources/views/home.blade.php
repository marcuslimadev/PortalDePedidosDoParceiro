@extends('layouts.app')

@section('title', 'Portal de Pedidos do Parceiro')

@section('content')
<!-- Hero Section -->
<section class="bg-gradient-to-r py-5" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 600px;">
    <div class="container">
        <div class="row align-items-center" style="min-height: 500px;">
            <div class="col-lg-7 text-white">
                <h1 class="display-3 fw-bold mb-4 animate__animated animate__fadeInLeft">Portal de Pedidos</h1>
                <p class="lead mb-5 fs-4 animate__animated animate__fadeInLeft animate__delay-1s">
                    Faça seus pedidos de forma rápida e acompanhe o status em tempo real.
                </p>
                <div class="d-flex gap-3 animate__animated animate__fadeInUp animate__delay-2s">
                    <a href="{{ route('login') }}" class="btn btn-light btn-lg px-5 py-3 shadow">
                        <i class="bi bi-box-arrow-in-right me-2"></i>
                        Entrar
                    </a>
                    <a href="/products" class="btn btn-outline-light btn-lg px-5 py-3">
                        <i class="bi bi-box-seam me-2"></i>
                        Ver Catálogo
                    </a>
                </div>
            </div>
            <div class="col-lg-5 text-center d-none d-lg-block animate__animated animate__fadeInRight">
                <i class="bi bi-boxes" style="font-size: 15rem; color: rgba(255,255,255,0.2);"></i>
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="py-5 bg-light">
    <div class="container py-5">
        <div class="text-center mb-5">
            <h2 class="display-5 fw-bold mb-3">Como funciona</h2>
            <p class="lead text-muted">Simples, rápido e eficiente</p>
        </div>
        
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm hover-lift">
                    <div class="card-body text-center p-4">
                        <div class="feature-icon bg-primary bg-gradient text-white rounded-circle d-inline-flex align-items-center justify-center mb-4" style="width: 80px; height: 80px;">
                            <i class="bi bi-search" style="font-size: 2rem;"></i>
                        </div>
                        <h3 class="h5 fw-bold mb-3">Explore o Catálogo</h3>
                        <p class="text-muted">
                            Navegue pelos produtos disponíveis, veja preços e estoque em tempo real.
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm hover-lift">
                    <div class="card-body text-center p-4">
                        <div class="feature-icon bg-success bg-gradient text-white rounded-circle d-inline-flex align-items-center justify-center mb-4" style="width: 80px; height: 80px;">
                            <i class="bi bi-cart-plus" style="font-size: 2rem;"></i>
                        </div>
                        <h3 class="h5 fw-bold mb-3">Monte seu Pedido</h3>
                        <p class="text-muted">
                            Adicione itens ao carrinho e escolha a condição de pagamento ideal.
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm hover-lift">
                    <div class="card-body text-center p-4">
                        <div class="feature-icon bg-info bg-gradient text-white rounded-circle d-inline-flex align-items-center justify-center mb-4" style="width: 80px; height: 80px;">
                            <i class="bi bi-truck" style="font-size: 2rem;"></i>
                        </div>
                        <h3 class="h5 fw-bold mb-3">Acompanhe a Entrega</h3>
                        <p class="text-muted">
                            Receba notificações e acompanhe cada etapa do seu pedido.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Benefits Section -->
<section class="py-5">
    <div class="container py-5">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-4 mb-lg-0">
                <h2 class="display-5 fw-bold mb-4">Vantagens do Portal</h2>
                <div class="list-group list-group-flush">
                    <div class="list-group-item border-0 px-0 py-3">
                        <i class="bi bi-check-circle-fill text-success me-3 fs-4"></i>
                        <span class="fs-5">Pedidos 24 horas por dia, 7 dias por semana</span>
                    </div>
                    <div class="list-group-item border-0 px-0 py-3">
                        <i class="bi bi-check-circle-fill text-success me-3 fs-4"></i>
                        <span class="fs-5">Histórico completo de compras</span>
                    </div>
                    <div class="list-group-item border-0 px-0 py-3">
                        <i class="bi bi-check-circle-fill text-success me-3 fs-4"></i>
                        <span class="fs-5">Repetir pedidos anteriores com um clique</span>
                    </div>
                    <div class="list-group-item border-0 px-0 py-3">
                        <i class="bi bi-check-circle-fill text-success me-3 fs-4"></i>
                        <span class="fs-5">Limite de crédito disponível em tempo real</span>
                    </div>
                    <div class="list-group-item border-0 px-0 py-3">
                        <i class="bi bi-check-circle-fill text-success me-3 fs-4"></i>
                        <span class="fs-5">Notificações em tempo real sobre seus pedidos</span>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <img src="https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?w=800&q=80" alt="Benefits" class="img-fluid rounded-4 shadow-lg">
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-5 text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
    <div class="container py-5 text-center">
        <h2 class="display-4 fw-bold mb-4">Pronto para começar?</h2>
        <p class="lead mb-5 fs-4">Faça login e comece a fazer seus pedidos agora mesmo</p>
        <a href="{{ route('login') }}" class="btn btn-light btn-lg px-5 py-3 shadow">
            Acessar o Portal
            <i class="bi bi-arrow-right ms-2"></i>
        </a>
    </div>
</section>

<style>
.hover-lift {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}
.hover-lift:hover {
    transform: translateY(-10px);
    box-shadow: 0 1rem 3rem rgba(0,0,0,.175)!important;
}
</style>
@endsection
