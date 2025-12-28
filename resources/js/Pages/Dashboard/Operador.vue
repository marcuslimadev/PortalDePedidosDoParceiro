<template>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Dashboard - Aprovação de Pedidos
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Estatísticas -->
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-4 mb-6">
                    <Card>
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-yellow-500 rounded-md p-3">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                                    Aguardando Aprovação
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
                                    Aprovados Hoje
                                </dt>
                                <dd class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
                                    {{ stats.approved_today }}
                                </dd>
                            </div>
                        </div>
                    </Card>

                    <Card>
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-red-500 rounded-md p-3">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                                    Cancelados Hoje
                                </dt>
                                <dd class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
                                    {{ stats.cancelled_today }}
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
                                    Valor Pendente
                                </dt>
                                <dd class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
                                    {{ formatCurrency(stats.pending_value) }}
                                </dd>
                            </div>
                        </div>
                    </Card>
                </div>

                <!-- Pedidos Pendentes de Aprovação -->
                <Card>
                    <template #header>
                        <div class="flex justify-between items-center">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                Pedidos Aguardando Aprovação
                            </h3>
                            <Badge variant="warning">{{ pendingOrders.length }}</Badge>
                        </div>
                    </template>

                    <Table v-if="pendingOrders.length > 0">
                        <template #header>
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Pedido
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Loja
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Data
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Valor
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Crédito Disponível
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Ações
                                </th>
                            </tr>
                        </template>
                        <tr v-for="order in pendingOrders" :key="order.id" class="hover:bg-gray-50 dark:hover:bg-gray-800">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                #{{ order.id }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">
                                {{ order.loja.name }}
                                <span class="text-gray-500 dark:text-gray-400 block text-xs">
                                    {{ order.loja.cnpj }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                {{ formatDate(order.created_at) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                {{ formatCurrency(order.total) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <span :class="[
                                    order.loja.available_credit >= order.total
                                        ? 'text-green-600 dark:text-green-400'
                                        : 'text-red-600 dark:text-red-400'
                                ]">
                                    {{ formatCurrency(order.loja.available_credit) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex gap-2">
                                    <button
                                        @click="$inertia.visit(route('orders.show', order.id))"
                                        class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300"
                                    >
                                        Ver
                                    </button>
                                    <button
                                        @click="approveOrder(order)"
                                        class="text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300"
                                        :disabled="order.loja.available_credit < order.total"
                                    >
                                        Aprovar
                                    </button>
                                    <button
                                        @click="cancelOrder(order)"
                                        class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300"
                                    >
                                        Cancelar
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </Table>

                    <EmptyState
                        v-else
                        title="Nenhum pedido pendente"
                        description="Não há pedidos aguardando aprovação no momento."
                    />
                </Card>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Card from '@/Components/Card.vue';
import Table from '@/Components/Table.vue';
import Badge from '@/Components/Badge.vue';
import EmptyState from '@/Components/EmptyState.vue';

defineProps({
    stats: Object,
    pendingOrders: Array
});

const formatCurrency = (value) => {
    return new Intl.NumberFormat('pt-BR', {
        style: 'currency',
        currency: 'BRL'
    }).format(value || 0);
};

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('pt-BR', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};

const approveOrder = (order) => {
    if (confirm(`Aprovar pedido #${order.id} no valor de ${formatCurrency(order.total)}?`)) {
        router.post(route('orders.approve', order.id));
    }
};

const cancelOrder = (order) => {
    const reason = prompt('Motivo do cancelamento:');
    if (reason) {
        router.post(route('orders.cancel', order.id), {
            cancellation_reason: reason
        });
    }
};
</script>
