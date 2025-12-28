<template>
    <div :class="[
        'bg-white dark:bg-gray-800 shadow-sm rounded-lg',
        padding ? 'p-6' : '',
        className
    ]">
        <div v-if="$slots.header" class="mb-4 border-b border-gray-200 dark:border-gray-700 pb-4">
            <slot name="header" />
        </div>
        
        <slot />
        
        <div v-if="$slots.footer" class="mt-4 border-t border-gray-200 dark:border-gray-700 pt-4">
            <slot name="footer" />
        </div>
    </div>
</template>

<script setup>
defineProps({
    padding: {
        type: Boolean,
        default: true
    },
    className: {
        type: String,
        default: ''
    }
});
</script>
