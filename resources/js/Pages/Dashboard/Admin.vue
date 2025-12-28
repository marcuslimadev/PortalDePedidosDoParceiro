<template>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Dashboard Administrativo
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Estatísticas -->
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4 mb-6">
                    <Card>
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-indigo-500 rounded-md p-3">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                                    Total de Pedidos
                                </dt>
                                <dd class="flex items-baseline">
                                    <div class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
                                        {{ stats.total_orders }}
                                    </div>
                                </dd>
                            </div>
                        </div>
                    </Card>

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
                                <dd class="flex items-baseline">
                                    <div class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
                                        {{ stats.pending_orders }}
                                    </div>
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
                                <dd class="flex items-baseline">
                                    <div class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
                                        {{ stats.approved_orders }}
                                    </div>
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
                                    Valor Total
                                </dt>
                                <dd class="flex items-baseline">
                                    <div class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
                                        {{ formatCurrency(stats.total_value) }}
                                    </div>
                                </dd>
                            </div>
                        </div>
                    </Card>
                </div>

                <!-- Últimos Pedidos -->
                <Card class="mb-6">
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
                                    Loja
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Valor
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Status
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Data
                                </th>
                            </tr>
                        </template>
                        <tr v-for="order in recentOrders" :key="order.id" class="hover:bg-gray-50 dark:hover:bg-gray-800 cursor-pointer" @click="$inertia.visit(route('orders.show', order.id))">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                #{{ order.id }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">
                                {{ order.loja?.name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                {{ formatCurrency(order.total) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <Badge :variant="getStatusVariant(order.status)">
                                    {{ getStatusLabel(order.status) }}
                                </Badge>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                {{ formatDate(order.created_at) }}
                            </td>
                        </tr>
                    </Table>

                    <template #footer>
                        <div class="px-6 py-3">
                            <SecondaryButton @click="$inertia.visit(route('orders.index'))" class="w-full">
                                Ver Todos os Pedidos
                            </SecondaryButton>
                        </div>
                    </template>
                </Card>

                <!-- Usuários Ativos -->
                <Card>
                    <template #header>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                            Usuários Ativos
                        </h3>
                    </template>

                    <Table v-if="activeUsers.length > 0">
                        <template #header>
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Nome
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Email
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Função
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Último Acesso
                                </th>
                            </tr>
                        </template>
                        <tr v-for="user in activeUsers" :key="user.id" class="hover:bg-gray-50 dark:hover:bg-gray-800">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                {{ user.name }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                {{ user.email }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <Badge>{{ user.role }}</Badge>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                {{ formatDate(user.last_login_at) }}
                            </td>
                        </tr>
                    </Table>
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
import SecondaryButton from '@/Components/SecondaryButton.vue';

defineProps({
    stats: Object,
    recentOrders: Array,
    activeUsers: Array
});

const formatCurrency = (value) => {
    return new Intl.NumberFormat('pt-BR', {
        style: 'currency',
        currency: 'BRL'
    }).format(value || 0);
};

const formatDate = (date) => {
    if (!date) return 'N/A';
    return new Date(date).toLocaleDateString('pt-BR', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
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
