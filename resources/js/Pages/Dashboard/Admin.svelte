<script>
    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.svelte';
    import Card from '@/Components/Card.svelte';
    import Badge from '@/Components/Badge.svelte';
    import { Link } from '@inertiajs/svelte';
    
    export let stats = {};
    export let recentOrders = [];
    export let activeUsers = [];
    
    function formatCurrency(value) {
        return new Intl.NumberFormat('pt-BR', {
            style: 'currency',
            currency: 'BRL'
        }).format(value || 0);
    }
    
    function formatDate(date) {
        return new Date(date).toLocaleDateString('pt-BR');
    }
    
    function getStatusBadgeType(status) {
        const types = {
            'pendente': 'warning',
            'aprovado': 'success',
            'cancelado': 'danger',
            'finalizado': 'info'
        };
        return types[status] || 'info';
    }
</script>

<svelte:head>
    <title>Dashboard Administrativo</title>
</svelte:head>

<AuthenticatedLayout>
    <div slot="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Dashboard Administrativo
        </h2>
    </div>

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
                            <dt class="text-sm font-medium text-gray-500 truncate">
                                Total de Pedidos
                            </dt>
                            <dd class="flex items-baseline">
                                <div class="text-2xl font-semibold text-gray-900">
                                    {stats.total_orders || 0}
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
                            <dt class="text-sm font-medium text-gray-500 truncate">
                                Pedidos Pendentes
                            </dt>
                            <dd class="flex items-baseline">
                                <div class="text-2xl font-semibold text-gray-900">
                                    {stats.pending_orders || 0}
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
                            <dt class="text-sm font-medium text-gray-500 truncate">
                                Pedidos Aprovados
                            </dt>
                            <dd class="flex items-baseline">
                                <div class="text-2xl font-semibold text-gray-900">
                                    {stats.approved_orders || 0}
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
                            <dt class="text-sm font-medium text-gray-500 truncate">
                                Valor Total
                            </dt>
                            <dd class="flex items-baseline">
                                <div class="text-2xl font-semibold text-gray-900">
                                    {formatCurrency(stats.total_value)}
                                </div>
                            </dd>
                        </div>
                    </div>
                </Card>
            </div>

            <!-- Últimos Pedidos -->
            <Card class="mb-6">
                <h3 slot="header" class="text-lg font-medium text-gray-900">
                    Últimos Pedidos
                </h3>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Loja</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Valor</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            {#each recentOrders as order}
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">#{order.id}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{order.loja?.name || '-'}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{formatDate(order.created_at)}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{formatCurrency(order.total)}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <Badge type={getStatusBadgeType(order.status)}>
                                            {order.status}
                                        </Badge>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <Link href="/orders/{order.id}" class="text-indigo-600 hover:text-indigo-900">
                                            Ver detalhes
                                        </Link>
                                    </td>
                                </tr>
                            {/each}
                        </tbody>
                    </table>
                </div>
            </Card>

            <!-- Usuários Ativos -->
            <Card>
                <h3 slot="header" class="text-lg font-medium text-gray-900">
                    Usuários Recentes
                </h3>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nome</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Função</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cadastrado em</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            {#each activeUsers as user}
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{user.name}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{user.email}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{user.role}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{formatDate(user.created_at)}</td>
                                </tr>
                            {/each}
                        </tbody>
                    </table>
                </div>
            </Card>
        </div>
    </div>
</AuthenticatedLayout>
