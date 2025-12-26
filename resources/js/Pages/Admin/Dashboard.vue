<template>
    <AuthenticatedLayout>
        <Head title="Dashboard Admin" />
        
        <div class="container is-fluid">
            <section class="hero is-info">
                <div class="hero-body">
                    <p class="title">Dashboard Administrativo</p>
                    <p class="subtitle">Bem-vindo, {{ $page.props.auth.user.name }}</p>
                </div>
            </section>

            <div class="columns mt-5">
                <div class="column">
                    <div class="box has-text-centered">
                        <p class="heading">Pedidos Pendentes</p>
                        <p class="title">{{ stats.pedidosPendentes || 0 }}</p>
                    </div>
                </div>
                <div class="column">
                    <div class="box has-text-centered">
                        <p class="heading">Total do Mês</p>
                        <p class="title">R$ {{ formatMoney(stats.totalMes || 0) }}</p>
                    </div>
                </div>
                <div class="column">
                    <div class="box has-text-centered">
                        <p class="heading">Lojas Ativas</p>
                        <p class="title">{{ stats.lojasAtivas || 0 }}</p>
                    </div>
                </div>
            </div>

            <div class="box mt-5">
                <h2 class="title is-4">Ações Rápidas</h2>
                <div class="buttons">
                    <Link :href="route('orders.index')" class="button is-primary">
                        <span class="icon"><i class="fas fa-shopping-cart"></i></span>
                        <span>Ver Pedidos</span>
                    </Link>
                    <Link :href="route('products.index')" class="button is-info">
                        <span class="icon"><i class="fas fa-box"></i></span>
                        <span>Produtos</span>
                    </Link>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

defineProps({
    stats: {
        type: Object,
        default: () => ({ pedidosPendentes: 0, totalMes: 0, lojasAtivas: 0 })
    }
});

const formatMoney = (value) => {
    return new Intl.NumberFormat('pt-BR', { minimumFractionDigits: 2 }).format(value);
};
</script>
