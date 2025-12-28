<template>
    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    Meus Pedidos
                </h2>
                <PrimaryButton @click="$inertia.visit(route('orders.create'))">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Novo Pedido
                </PrimaryButton>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Informações da Loja -->
                <Card class="mb-6">
                    <template #header>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                            Informações da Loja
                        </h3>
                    </template>

                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Nome</dt>
                            <dd class="mt-1 text-lg text-gray-900 dark:text-gray-100">{{ store.name }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">CNPJ</dt>
                            <dd class="mt-1 text-lg text-gray-900 dark:text-gray-100">{{ store.cnpj }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Limite de Crédito</dt>
                            <dd class="mt-1 text-lg font-semibold" :class="[
                                store.available_credit > 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400'
                            ]">
                                {{ formatCurrency(store.available_credit) }}
                            </dd>
                            <dd class="text-sm text-gray-500 dark:text-gray-400">
                                de {{ formatCurrency(store.credit_limit) }}
                            </dd>
                        </div>
                    </div>
                </Card>

                <!-- Estatísticas -->
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-3 mb-6">
                    <Card>
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-yellow-500 rounded-md p-3">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                                    Pedidos Pendentes
                                </dt>
                                <dd class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
                                    {{ stats.pending_orders }}
                                </dd>
                            </div>
                        </div>
                    </Card>

                    <Card>
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-green-500 rounded-md p-3">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                                    Pedidos Aprovados
                                </dt>
                                <dd class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
                                    {{ stats.approved_orders }}
                                </dd>
                            </div>
                        </div>
                    </Card>

                    <Card>
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-blue-500 rounded-md p-3">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                                    Total do Mês
                                </dt>
                                <dd class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
                                    {{ formatCurrency(stats.month_total) }}
                                </dd>
                            </div>
                        </div>
                    </Card>
                </div>

                <!-- Últimos Pedidos -->
                <Card>
                    <template #header>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                            Últimos Pedidos
                        </h3>
                    </template>

                    <Table v-if="recentOrders.length > 0">
                        <template #header>
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Pedido
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Data
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Valor
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Status
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Ações
                                </th>
                            </tr>
                        </template>
                        <tr v-for="order in recentOrders" :key="order.id" class="hover:bg-gray-50 dark:hover:bg-gray-800">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                #{{ order.id }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                {{ formatDate(order.created_at) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                {{ formatCurrency(order.total) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <Badge :variant="getStatusVariant(order.status)">
                                    {{ getStatusLabel(order.status) }}
                                </Badge>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <button
                                    @click="$inertia.visit(route('orders.show', order.id))"
                                    class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300"
                                >
                                    Ver Detalhes
                                </button>
                            </td>
                        </tr>
                    </Table>

                    <EmptyState
                        v-else
                        title="Nenhum pedido realizado"
                        description="Comece criando seu primeiro pedido agora."
                    >
                        <PrimaryButton @click="$inertia.visit(route('orders.create'))">
                            Criar Primeiro Pedido
                        </PrimaryButton>
                    </EmptyState>

                    <template #footer v-if="recentOrders.length > 0">
                        <div class="px-6 py-3">
                            <SecondaryButton @click="$inertia.visit(route('orders.index'))" class="w-full">
                                Ver Todos os Pedidos
                            </SecondaryButton>
                        </div>
                    </template>
                </Card>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Card from '@/Components/Card.vue';
import Table from '@/Components/Table.vue';
import Badge from '@/Components/Badge.vue';
import EmptyState from '@/Components/EmptyState.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';

defineProps({
    store: Object,
    stats: Object,
    recentOrders: Array
});

const formatCurrency = (value) => {
    return new Intl.NumberFormat('pt-BR', {
        style: 'currency',
        currency: 'BRL'
    }).format(value || 0);
};

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('pt-BR');
};

const getStatusVariant = (status) => {
    const variants = {
        'pendente': 'warning',
        'aprovado': 'success',
        'cancelado': 'danger'
    };
    return variants[status] || 'default';
};

const getStatusLabel = (status) => {
    const labels = {
        'pendente': 'Pendente',
        'aprovado': 'Aprovado',
        'cancelado': 'Cancelado'
    };
    return labels[status] || status;
};
</script>
