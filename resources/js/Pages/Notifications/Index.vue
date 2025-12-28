<template>
    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    Notificações
                </h2>
                <SecondaryButton v-if="hasUnread" @click="markAllAsRead">
                    Marcar todas como lidas
                </SecondaryButton>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <Card v-if="notifications.data.length > 0" :padding="false">
                    <div class="divide-y divide-gray-200 dark:divide-gray-700">
                        <div
                            v-for="notification in notifications.data"
                            :key="notification.id"
                            :class="[
                                'p-6 hover:bg-gray-50 dark:hover:bg-gray-800 transition',
                                !notification.read ? 'bg-blue-50 dark:bg-blue-900/20' : ''
                            ]"
                        >
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center gap-3 mb-2">
                                        <Badge :variant="getTypeVariant(notification.type)">
                                            {{ getTypeLabel(notification.type) }}
                                        </Badge>
                                        <span v-if="!notification.read" class="flex h-2 w-2 relative">
                                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                                            <span class="relative inline-flex rounded-full h-2 w-2 bg-blue-500"></span>
                                        </span>
                                    </div>
                                    
                                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                        {{ notification.title }}
                                    </h3>
                                    
                                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                        {{ notification.message }}
                                    </p>
                                    
                                    <p class="mt-2 text-xs text-gray-500 dark:text-gray-500">
                                        {{ formatDate(notification.created_at) }}
                                    </p>
                                </div>
                                
                                <button
                                    v-if="!notification.read"
                                    @click="markAsRead(notification.id)"
                                    class="ml-4 text-sm text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300"
                                >
                                    Marcar como lida
                                </button>
                            </div>
                        </div>
                    </div>

                    <Pagination
                        :links="notifications.links"
                        :meta="notifications.meta"
                    />
                </Card>

                <EmptyState
                    v-else
                    title="Nenhuma notificação"
                    description="Você não possui notificações no momento."
                />
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { computed } from 'vue';
import { router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Card from '@/Components/Card.vue';
import Badge from '@/Components/Badge.vue';
import EmptyState from '@/Components/EmptyState.vue';
import Pagination from '@/Components/Pagination.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';

const props = defineProps({
    notifications: Object
});

const hasUnread = computed(() => {
    return props.notifications.data.some(n => !n.read);
});

const formatDate = (date) => {
    const now = new Date();
    const notificationDate = new Date(date);
    const diff = now - notificationDate;
    
    const minutes = Math.floor(diff / 60000);
    const hours = Math.floor(diff / 3600000);
    const days = Math.floor(diff / 86400000);
    
    if (minutes < 1) return 'Agora mesmo';
    if (minutes < 60) return `Há ${minutes} minuto${minutes > 1 ? 's' : ''}`;
    if (hours < 24) return `Há ${hours} hora${hours > 1 ? 's' : ''}`;
    if (days < 7) return `Há ${days} dia${days > 1 ? 's' : ''}`;
    
    return notificationDate.toLocaleDateString('pt-BR', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
    });
};

const getTypeVariant = (type) => {
    const variants = {
        'order_created': 'info',
        'order_approved': 'success',
        'order_rejected': 'danger',
        'credit_limit_changed': 'warning',
        'general': 'default'
    };
    return variants[type] || 'default';
};

const getTypeLabel = (type) => {
    const labels = {
        'order_created': 'Pedido Criado',
        'order_approved': 'Pedido Aprovado',
        'order_rejected': 'Pedido Rejeitado',
        'credit_limit_changed': 'Limite de Crédito',
        'general': 'Geral'
    };
    return labels[type] || 'Notificação';
};

const markAsRead = (id) => {
    router.post(route('notifications.read', id), {}, {
        preserveScroll: true
    });
};

const markAllAsRead = () => {
    router.post(route('notifications.read-all'), {}, {
        preserveScroll: true
    });
};
</script>
