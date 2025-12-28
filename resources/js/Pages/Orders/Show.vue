<template>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Pedido #{{ order.id }}
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <!-- Informações do Pedido -->
                <Card>
                    <template #header>
                        <div class="flex justify-between items-center">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                Informações do Pedido
                            </h3>
                            <Badge :variant="getStatusVariant(order.status)">
                                {{ getStatusLabel(order.status) }}
                            </Badge>
                        </div>
                    </template>

                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Loja</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ order.loja.name }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">CNPJ</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ order.loja.cnpj }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Data do Pedido</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ formatDate(order.created_at) }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Condição de Pagamento</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ order.payment_terms }}</dd>
                        </div>
                        <div v-if="order.observations" class="sm:col-span-2">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Observações</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ order.observations }}</dd>
                        </div>
                        <div v-if="order.cancellation_reason" class="sm:col-span-2">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Motivo do Cancelamento</dt>
                            <dd class="mt-1 text-sm text-red-600 dark:text-red-400">{{ order.cancellation_reason }}</dd>
                        </div>
                    </div>
                </Card>

                <!-- Itens do Pedido -->
                <Card>
                    <template #header>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                            Itens do Pedido
                        </h3>
                    </template>

                    <Table>
                        <template #header>
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Produto
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Qtd
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Preço Unit.
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Subtotal
                                </th>
                            </tr>
                        </template>
                        <tr v-for="item in order.items" :key="item.id">
                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">
                                {{ item.product.descricao }}
                                <span class="text-gray-500 dark:text-gray-400 block text-xs">
                                    Código: {{ item.product.codigo }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                {{ item.quantity }} {{ item.product.unidade }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                {{ formatCurrency(item.price) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                {{ formatCurrency(item.subtotal) }}
                            </td>
                        </tr>
                    </Table>

                    <template #footer>
                        <div class="px-6 py-4 space-y-2">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500 dark:text-gray-400">Subtotal:</span>
                                <span class="text-gray-900 dark:text-gray-100">{{ formatCurrency(order.subtotal) }}</span>
                            </div>
                            <div v-if="order.discount > 0" class="flex justify-between text-sm">
                                <span class="text-gray-500 dark:text-gray-400">
                                    Desconto ({{ order.discount_percentage }}%):
                                </span>
                                <span class="text-red-600 dark:text-red-400">-{{ formatCurrency(order.discount) }}</span>
                            </div>
                            <div class="flex justify-between text-lg font-bold border-t border-gray-200 dark:border-gray-700 pt-2">
                                <span class="text-gray-900 dark:text-gray-100">Total:</span>
                                <span class="text-gray-900 dark:text-gray-100">{{ formatCurrency(order.total) }}</span>
                            </div>
                        </div>
                    </template>
                </Card>

                <!-- Ações -->
                <Card v-if="order.status === 'pendente' && (can.approve || can.cancel)">
                    <div class="flex justify-end gap-4">
                        <DangerButton v-if="can.cancel" @click="cancelOrder">
                            Cancelar Pedido
                        </DangerButton>
                        <PrimaryButton v-if="can.approve" @click="approveOrder">
                            Aprovar Pedido
                        </PrimaryButton>
                    </div>
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
import PrimaryButton from '@/Components/PrimaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';

const props = defineProps({
    order: Object,
    can: Object
});

const formatCurrency = (value) => {
    return new Intl.NumberFormat('pt-BR', {
        style: 'currency',
        currency: 'BRL'
    }).format(value);
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

const approveOrder = () => {
    if (confirm(`Aprovar pedido #${props.order.id}?`)) {
        router.post(route('orders.approve', props.order.id));
    }
};

const cancelOrder = () => {
    const reason = prompt('Motivo do cancelamento:');
    if (reason) {
        router.post(route('orders.cancel', props.order.id), {
            cancellation_reason: reason
        });
    }
};
</script>
