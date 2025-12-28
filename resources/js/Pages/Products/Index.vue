<template>
    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    Produtos
                </h2>
                <PrimaryButton v-if="can.create" @click="$inertia.visit(route('products.create'))">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Novo Produto
                </PrimaryButton>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <Card>
                    <!-- Filtros -->
                    <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-3">
                        <div>
                            <InputLabel for="search" value="Buscar" />
                            <TextInput
                                id="search"
                                v-model="filters.search"
                                type="text"
                                class="mt-1 block w-full"
                                placeholder="Código ou descrição..."
                                @input="debounceSearch"
                            />
                        </div>
                        <div>
                            <InputLabel for="category" value="Categoria" />
                            <select
                                id="category"
                                v-model="filters.categoria"
                                class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                @change="search"
                            >
                                <option value="">Todas</option>
                                <option value="Alimentos">Alimentos</option>
                                <option value="Bebidas">Bebidas</option>
                                <option value="Limpeza">Limpeza</option>
                                <option value="Higiene">Higiene</option>
                                <option value="Diversos">Diversos</option>
                            </select>
                        </div>
                        <div class="flex items-end">
                            <SecondaryButton @click="clearFilters" class="w-full">
                                Limpar Filtros
                            </SecondaryButton>
                        </div>
                    </div>

                    <!-- Tabela -->
                    <Table v-if="products.data.length > 0">
                        <template #header>
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Código
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Descrição
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Categoria
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Preço
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Estoque
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Ações
                                </th>
                            </tr>
                        </template>
                        <tr v-for="product in products.data" :key="product.id" class="hover:bg-gray-50 dark:hover:bg-gray-800">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                {{ product.codigo }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">
                                {{ product.descricao }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                <Badge :variant="getCategoryVariant(product.categoria)">
                                    {{ product.categoria }}
                                </Badge>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                {{ formatCurrency(product.preco) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                <Badge :variant="product.estoque > 0 ? 'success' : 'danger'">
                                    {{ product.estoque }} {{ product.unidade }}
                                </Badge>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex gap-2">
                                    <button
                                        v-if="can.edit"
                                        @click="$inertia.visit(route('products.edit', product.id))"
                                        class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300"
                                    >
                                        Editar
                                    </button>
                                    <button
                                        v-if="can.delete"
                                        @click="deleteProduct(product)"
                                        class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300"
                                    >
                                        Excluir
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </Table>

                    <EmptyState
                        v-else
                        title="Nenhum produto encontrado"
                        description="Comece criando seu primeiro produto."
                    >
                        <PrimaryButton v-if="can.create" @click="$inertia.visit(route('products.create'))">
                            Criar Produto
                        </PrimaryButton>
                    </EmptyState>

                    <Pagination
                        v-if="products.data.length > 0"
                        :links="products.links"
                        :meta="products.meta"
                        class="mt-6"
                    />
                </Card>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, reactive } from 'vue';
import { router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Card from '@/Components/Card.vue';
import Table from '@/Components/Table.vue';
import Badge from '@/Components/Badge.vue';
import EmptyState from '@/Components/EmptyState.vue';
import Pagination from '@/Components/Pagination.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';

const props = defineProps({
    products: Object,
    filters: Object,
    can: Object
});

const filters = reactive({
    search: props.filters?.search || '',
    categoria: props.filters?.categoria || ''
});

let searchTimeout = null;

const debounceSearch = () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        search();
    }, 300);
};

const search = () => {
    router.get(route('products.index'), filters, {
        preserveState: true,
        preserveScroll: true
    });
};

const clearFilters = () => {
    filters.search = '';
    filters.categoria = '';
    search();
};

const formatCurrency = (value) => {
    return new Intl.NumberFormat('pt-BR', {
        style: 'currency',
        currency: 'BRL'
    }).format(value);
};

const getCategoryVariant = (category) => {
    const variants = {
        'Alimentos': 'success',
        'Bebidas': 'info',
        'Limpeza': 'warning',
        'Higiene': 'primary',
        'Diversos': 'default'
    };
    return variants[category] || 'default';
};

const deleteProduct = (product) => {
    if (confirm(`Tem certeza que deseja excluir o produto ${product.codigo}?`)) {
        router.delete(route('products.destroy', product.id));
    }
};
</script>
