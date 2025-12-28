<template>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Novo Produto
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <Card>
                    <form @submit.prevent="submit">
                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <!-- Código -->
                            <div>
                                <InputLabel for="codigo" value="Código *" />
                                <TextInput
                                    id="codigo"
                                    v-model="form.codigo"
                                    type="text"
                                    class="mt-1 block w-full"
                                    required
                                />
                                <InputError :message="form.errors.codigo" class="mt-2" />
                            </div>

                            <!-- Unidade -->
                            <div>
                                <InputLabel for="unidade" value="Unidade *" />
                                <select
                                    id="unidade"
                                    v-model="form.unidade"
                                    class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                    required
                                >
                                    <option value="">Selecione...</option>
                                    <option value="UN">UN - Unidade</option>
                                    <option value="CX">CX - Caixa</option>
                                    <option value="KG">KG - Kilograma</option>
                                    <option value="LT">LT - Litro</option>
                                    <option value="PC">PC - Peça</option>
                                </select>
                                <InputError :message="form.errors.unidade" class="mt-2" />
                            </div>

                            <!-- Descrição -->
                            <div class="sm:col-span-2">
                                <InputLabel for="descricao" value="Descrição *" />
                                <textarea
                                    id="descricao"
                                    v-model="form.descricao"
                                    rows="3"
                                    class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                    required
                                />
                                <InputError :message="form.errors.descricao" class="mt-2" />
                            </div>

                            <!-- Preço -->
                            <div>
                                <InputLabel for="preco" value="Preço (R$) *" />
                                <TextInput
                                    id="preco"
                                    v-model="form.preco"
                                    type="number"
                                    step="0.01"
                                    class="mt-1 block w-full"
                                    required
                                />
                                <InputError :message="form.errors.preco" class="mt-2" />
                            </div>

                            <!-- Tributação -->
                            <div>
                                <InputLabel for="tributacao" value="Tributação *" />
                                <select
                                    id="tributacao"
                                    v-model="form.tributacao"
                                    class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                    required
                                >
                                    <option value="">Selecione...</option>
                                    <option value="T">T - Tributado</option>
                                    <option value="F">F - Isento</option>
                                    <option value="I">I - Imune</option>
                                </select>
                                <InputError :message="form.errors.tributacao" class="mt-2" />
                            </div>

                            <!-- Estoque -->
                            <div>
                                <InputLabel for="estoque" value="Estoque" />
                                <TextInput
                                    id="estoque"
                                    v-model="form.estoque"
                                    type="number"
                                    class="mt-1 block w-full"
                                />
                                <InputError :message="form.errors.estoque" class="mt-2" />
                            </div>

                            <!-- Categoria -->
                            <div>
                                <InputLabel for="categoria" value="Categoria" />
                                <select
                                    id="categoria"
                                    v-model="form.categoria"
                                    class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                >
                                    <option value="">Selecione...</option>
                                    <option value="Alimentos">Alimentos</option>
                                    <option value="Bebidas">Bebidas</option>
                                    <option value="Limpeza">Limpeza</option>
                                    <option value="Higiene">Higiene</option>
                                    <option value="Diversos">Diversos</option>
                                </select>
                                <InputError :message="form.errors.categoria" class="mt-2" />
                            </div>

                            <!-- Campos Winthor (Opcionais) -->
                            <div class="sm:col-span-2 border-t border-gray-200 dark:border-gray-700 pt-6 mt-4">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                                    Informações Winthor (Opcional)
                                </h3>
                                
                                <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
                                    <div>
                                        <InputLabel for="codprod_winthor" value="Código Winthor" />
                                        <TextInput
                                            id="codprod_winthor"
                                            v-model="form.codprod_winthor"
                                            type="text"
                                            class="mt-1 block w-full"
                                        />
                                    </div>

                                    <div>
                                        <InputLabel for="embalagem" value="Embalagem" />
                                        <TextInput
                                            id="embalagem"
                                            v-model="form.embalagem"
                                            type="text"
                                            class="mt-1 block w-full"
                                        />
                                    </div>

                                    <div>
                                        <InputLabel for="marca" value="Marca" />
                                        <TextInput
                                            id="marca"
                                            v-model="form.marca"
                                            type="text"
                                            class="mt-1 block w-full"
                                        />
                                    </div>

                                    <div>
                                        <InputLabel for="peso_liquido" value="Peso Líquido (kg)" />
                                        <TextInput
                                            id="peso_liquido"
                                            v-model="form.peso_liquido"
                                            type="number"
                                            step="0.001"
                                            class="mt-1 block w-full"
                                        />
                                    </div>

                                    <div>
                                        <InputLabel for="peso_bruto" value="Peso Bruto (kg)" />
                                        <TextInput
                                            id="peso_bruto"
                                            v-model="form.peso_bruto"
                                            type="number"
                                            step="0.001"
                                            class="mt-1 block w-full"
                                        />
                                    </div>

                                    <div>
                                        <InputLabel for="ncm" value="NCM" />
                                        <TextInput
                                            id="ncm"
                                            v-model="form.ncm"
                                            type="text"
                                            class="mt-1 block w-full"
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-4 mt-6">
                            <SecondaryButton @click="$inertia.visit(route('products.index'))">
                                Cancelar
                            </SecondaryButton>
                            <PrimaryButton :disabled="form.processing">
                                Salvar Produto
                            </PrimaryButton>
                        </div>
                    </form>
                </Card>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Card from '@/Components/Card.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';

const form = useForm({
    codigo: '',
    descricao: '',
    preco: '',
    unidade: '',
    tributacao: '',
    estoque: 0,
    categoria: '',
    codprod_winthor: '',
    embalagem: '',
    marca: '',
    peso_liquido: '',
    peso_bruto: '',
    ncm: ''
});

const submit = () => {
    form.post(route('products.store'));
};
</script>
