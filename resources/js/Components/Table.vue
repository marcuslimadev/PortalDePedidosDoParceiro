<template>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-900">
                <tr>
                    <th v-for="(column, index) in columns" 
                        :key="index"
                        scope="col" 
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        {{ column.label }}
                    </th>
                    <th v-if="$slots.actions" scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        Ações
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                <tr v-if="loading">
                    <td :colspan="columns.length + ($slots.actions ? 1 : 0)" class="px-6 py-4 text-center">
                        <div class="flex justify-center">
                            <svg class="animate-spin h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </div>
                    </td>
                </tr>
                <tr v-else-if="!data || data.length === 0">
                    <td :colspan="columns.length + ($slots.actions ? 1 : 0)" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                        {{ emptyMessage }}
                    </td>
                </tr>
                <tr v-else v-for="(item, index) in data" :key="index" class="hover:bg-gray-50 dark:hover:bg-gray-700">
                    <td v-for="(column, colIndex) in columns" 
                        :key="colIndex"
                        class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                        <slot :name="'cell-' + column.key" :item="item" :value="getNestedValue(item, column.key)">
                            {{ getNestedValue(item, column.key) }}
                        </slot>
                    </td>
                    <td v-if="$slots.actions" class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <slot name="actions" :item="item" />
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<script setup>
defineProps({
    columns: {
        type: Array,
        required: true
    },
    data: {
        type: Array,
        default: () => []
    },
    loading: {
        type: Boolean,
        default: false
    },
    emptyMessage: {
        type: String,
        default: 'Nenhum registro encontrado'
    }
});

const getNestedValue = (obj, path) => {
    return path.split('.').reduce((current, key) => current?.[key], obj);
};
</script>
