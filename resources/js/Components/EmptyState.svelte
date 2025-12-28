<script>
    export let title = '';
    export let icon = '';
</script>

<div class="text-center py-12">
    {#if icon}
        <div class="mx-auto h-12 w-12 text-gray-400">
            {@html icon}
        </div>
    {/if}
    
    {#if title}
        <h3 class="mt-2 text-sm font-medium text-gray-900">{title}</h3>
    {/if}
    
    <div class="mt-1 text-sm text-gray-500">
        <slot />
    </div>
</div>
