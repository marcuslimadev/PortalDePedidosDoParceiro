<template>
    <AuthenticatedLayout>
        <Head title="Dashboard Loja" />
        
        <div class="container is-fluid">
            <section class="hero is-primary">
                <div class="hero-body">
                    <p class="title">Meu Portal de Pedidos</p>
                    <p class="subtitle">{{ $page.props.auth.user.name }}</p>
                </div>
            </section>

            <div class="columns mt-5">
                <div class="column">
                    <div class="box">
                        <p class="heading">Limite de Crédito</p>
                        <progress class="progress is-success" :value="creditoUsado" :max="creditoLimite">{{ percentualCredito }}%</progress>
                        <p class="help">R$ {{ formatMoney(creditoDisponivel) }} disponível de R$ {{ formatMoney(creditoLimite) }}</p>
                    </div>
                </div>
                <div class="column">
                    <div class="box has-text-centered">
                        <p class="heading">Meus Pedidos</p>
                        <p class="title">{{ totalPedidos }}</p>
                    </div>
                </div>
            </div>

            <div class="box mt-5">
                <h2 class="title is-4">Novo Pedido</h2>
                <Link :href="route('orders.create')" class="button is-primary is-large">
                    <span class="icon"><i class="fas fa-plus"></i></span>
                    <span>Criar Pedido</span>
                </Link>
            </div>

            <div class="box mt-5">
                <h2 class="title is-4">Últimos Pedidos</h2>
                <Link :href="route('orders.index')" class="button is-info">Ver Todos</Link>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    user: Object
});

const creditoLimite = computed(() => props.user?.credit_limit || 0);
const creditoUsado = computed(() => props.user?.credit_used || 0);
const creditoDisponivel = computed(() => Math.max(0, creditoLimite.value - creditoUsado.value));
const percentualCredito = computed(() => creditoLimite.value > 0 ? Math.round((creditoUsado.value / creditoLimite.value) * 100) : 0);
const totalPedidos = computed(() => 0); // TODO: passar do controller

const formatMoney = (value) => {
    return new Intl.NumberFormat('pt-BR', { minimumFractionDigits: 2 }).format(value);
};
</script>
