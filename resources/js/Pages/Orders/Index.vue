<template>
    <AuthenticatedLayout>
        <Head title="Pedidos" />
        
        <div class="container is-fluid">
            <div class="level">
                <div class="level-left">
                    <div class="level-item">
                        <h1 class="title">Pedidos</h1>
                    </div>
                </div>
                <div class="level-right">
                    <div class="level-item" v-if="$page.props.auth.user.role === 'loja'">
                        <Link :href="route('orders.create')" class="button is-primary">
                            <span class="icon"><i class="fas fa-plus"></i></span>
                            <span>Novo Pedido</span>
                        </Link>
                    </div>
                </div>
            </div>

            <div class="box">
                <div class="table-container">
                    <table class="table is-fullwidth is-striped is-hoverable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Loja</th>
                                <th>Data</th>
                                <th>Status</th>
                                <th>Total</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="order in orders.data" :key="order.id">
                                <td>{{ order.id }}</td>
                                <td>{{ order.loja.name }}</td>
                                <td>{{ formatDate(order.created_at) }}</td>
                                <td>
                                    <span class="tag" :class="statusClass(order.status)">
                                        {{ order.status }}
                                    </span>
                                </td>
                                <td>R$ {{ formatMoney(order.total) }}</td>
                                <td>
                                    <Link :href="route('orders.show', order.id)" class="button is-small is-info">
                                        Ver
                                    </Link>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

defineProps({
    orders: Object
});

const formatMoney = (value) => {
    return new Intl.NumberFormat('pt-BR', { minimumFractionDigits: 2 }).format(value);
};

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('pt-BR');
};

const statusClass = (status) => {
    return {
        'is-warning': status === 'pendente',
        'is-success': status === 'aprovado',
        'is-danger': status === 'cancelado',
    };
};
</script>
