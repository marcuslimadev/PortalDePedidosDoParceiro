<template>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Criar Novo Pedido
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <form @submit.prevent="submit">
                    <Card class="mb-6">
                        <template #header>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                Informações do Pedido
                            </h3>
                        </template>

                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <div>
                                <InputLabel for="payment_terms" value="Condição de Pagamento *" />
                                <select
                                    id="payment_terms"
                                    v-model="form.payment_terms"
                                    class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                    required
                                >
                                    <option value="">Selecione...</option>
                                    <option v-for="term in paymentTerms" :key="term" :value="term">
                                        {{ term }}
                                    </option>
                                </select>
                                <InputError :message="form.errors.payment_terms" class="mt-2" />
                            </div>

                            <div class="sm:col-span-2">
                                <InputLabel for="observations" value="Observações" />
                                <textarea
                                    id="observations"
                                    v-model="form.observations"
                                    rows="3"
                                    class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                />
                                <InputError :message="form.errors.observations" class="mt-2" />
                            </div>
                        </div>
                    </Card>

                    <Card class="mb-6">
                        <template #header>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                Produtos
                            </h3>
                        </template>

                        <div class="mb-4">
                            <InputLabel for="product_search" value="Adicionar Produto" />
                            <div class="flex gap-2">
                                <select
                                    id="product_search"
                                    v-model="selectedProduct"
                                    class="flex-1 border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                >
                                    <option value="">Selecione um produto...</option>
                                    <option v-for="product in products" :key="product.id" :value="product">
                                        {{ product.codigo }} - {{ product.descricao }} - {{ formatCurrency(product.preco) }}
                                    </option>
                                </select>
                                <PrimaryButton type="button" @click="addProduct">
                                    Adicionar
                                </PrimaryButton>
                            </div>
                        </div>

                        <Table v-if="form.items.length > 0">
                            <template #header>
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Produto
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Quantidade
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Preço Unit.
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Subtotal
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Ações
                                    </th>
                                </tr>
                            </template>
                            <tr v-for="(item, index) in form.items" :key="index">
                                <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">
                                    {{ item.product.descricao }}
                                    <span class="text-gray-500 dark:text-gray-400 block text-xs">
                                        Código: {{ item.product.codigo }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <input
                                        v-model.number="item.quantidade"
                                        type="number"
                                        min="1"
                                        class="w-24 border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm text-sm"
                                    />
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                    {{ formatCurrency(item.product.preco) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                    {{ formatCurrency(item.product.preco * item.quantidade) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <button
                                        type="button"
                                        @click="removeItem(index)"
                                        class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300"
                                    >
                                        Remover
                                    </button>
                                </td>
                            </tr>
                        </Table>

                        <EmptyState
                            v-else
                            title="Nenhum produto adicionado"
                            description="Selecione produtos acima para adicionar ao pedido."
                        />

                        <template #footer v-if="form.items.length > 0">
                            <div class="px-6 py-4 space-y-2">
                                <div class="flex justify-between text-lg font-bold border-t border-gray-200 dark:border-gray-700 pt-2">
                                    <span class="text-gray-900 dark:text-gray-100">Total:</span>
                                    <span class="text-gray-900 dark:text-gray-100">{{ formatCurrency(total) }}</span>
                                </div>
                            </div>
                        </template>
                    </Card>

                    <div class="flex justify-end gap-4">
                        <SecondaryButton type="button" @click="$inertia.visit(route('orders.index'))">
                            Cancelar
                        </SecondaryButton>
                        <PrimaryButton type="submit" :disabled="form.processing || form.items.length === 0">
                            Criar Pedido
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Card from '@/Components/Card.vue';
import Table from '@/Components/Table.vue';
import EmptyState from '@/Components/EmptyState.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';

const props = defineProps({
    products: Array,
    paymentTerms: Array
});

const form = useForm({
    payment_terms: '',
    observations: '',
    items: []
});

const selectedProduct = ref('');

const total = computed(() => {
    return form.items.reduce((sum, item) => {
        return sum + (item.product.preco * item.quantidade);
    }, 0);
});

const formatCurrency = (value) => {
    return new Intl.NumberFormat('pt-BR', {
        style: 'currency',
        currency: 'BRL'
    }).format(value || 0);
};

const addProduct = () => {
    if (!selectedProduct.value) return;

    const existingItem = form.items.find(
        item => item.product.id === selectedProduct.value.id
    );

    if (existingItem) {
        existingItem.quantidade++;
    } else {
        form.items.push({
            product_id: selectedProduct.value.id,
            product: selectedProduct.value,
            quantidade: 1
        });
    }

    selectedProduct.value = '';
};

const removeItem = (index) => {
    form.items.splice(index, 1);
};

const submit = () => {
    form.post(route('orders.store'));
};
</script>
