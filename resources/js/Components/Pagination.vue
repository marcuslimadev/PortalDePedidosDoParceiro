<template>
    <div v-if="links.length > 3" class="flex items-center justify-between border-t border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 px-4 py-3 sm:px-6">
        <div class="flex flex-1 justify-between sm:hidden">
            <Link
                :href="links[0].url"
                :class="[
                    'relative inline-flex items-center rounded-md px-4 py-2 text-sm font-medium',
                    links[0].url
                        ? 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700'
                        : 'bg-gray-100 dark:bg-gray-900 text-gray-400 dark:text-gray-600 cursor-not-allowed'
                ]"
                :disabled="!links[0].url"
            >
                Anterior
            </Link>
            <Link
                :href="links[links.length - 1].url"
                :class="[
                    'relative ml-3 inline-flex items-center rounded-md px-4 py-2 text-sm font-medium',
                    links[links.length - 1].url
                        ? 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700'
                        : 'bg-gray-100 dark:bg-gray-900 text-gray-400 dark:text-gray-600 cursor-not-allowed'
                ]"
                :disabled="!links[links.length - 1].url"
            >
                Próximo
            </Link>
        </div>
        <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
            <div>
                <p class="text-sm text-gray-700 dark:text-gray-300">
                    Mostrando
                    <span class="font-medium">{{ meta.from }}</span>
                    até
                    <span class="font-medium">{{ meta.to }}</span>
                    de
                    <span class="font-medium">{{ meta.total }}</span>
                    resultados
                </p>
            </div>
            <div>
                <nav class="isolate inline-flex -space-x-px rounded-md shadow-sm" aria-label="Pagination">
                    <Link
                        v-for="(link, index) in links"
                        :key="index"
                        :href="link.url"
                        :class="[
                            'relative inline-flex items-center px-4 py-2 text-sm font-medium',
                            index === 0 ? 'rounded-l-md' : '',
                            index === links.length - 1 ? 'rounded-r-md' : '',
                            link.active
                                ? 'z-10 bg-indigo-600 text-white focus:z-20'
                                : link.url
                                    ? 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700'
                                    : 'bg-gray-100 dark:bg-gray-900 text-gray-400 dark:text-gray-600 cursor-not-allowed'
                        ]"
                        :disabled="!link.url"
                        v-html="link.label"
                    />
                </nav>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    links: {
        type: Array,
        required: true
    },
    meta: {
        type: Object,
        required: true
    }
});
</script>
