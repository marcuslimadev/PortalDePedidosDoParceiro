<template>
    <AuthenticatedLayout>
        <Head title="Dashboard Operador" />
        
        <div class="container is-fluid">
            <section class="hero is-link">
                <div class="hero-body">
                    <p class="title">Dashboard do Operador</p>
                    <p class="subtitle">Bem-vindo, {{ $page.props.auth.user.name }}</p>
                </div>
            </section>

            <div class="columns mt-5">
                <div class="column">
                    <div class="box has-text-centered">
                        <p class="heading">Pedidos Hoje</p>
                        <p class="title">0</p>
                    </div>
                </div>
                <div class="column">
                    <div class="box has-text-centered">
                        <p class="heading">Pedidos Pendentes</p>
                        <p class="title">0</p>
                    </div>
                </div>
            </div>

            <div class="box mt-5">
                <h2 class="title is-4">Ações Rápidas</h2>
                <div class="buttons">
                    <Link :href="route('orders.index')" class="button is-primary">
                        <span class="icon"><i class="fas fa-shopping-cart"></i></span>
                        <span>Gerenciar Pedidos</span>
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
</script>
