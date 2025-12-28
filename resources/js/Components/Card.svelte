<div class="overflow-hidden bg-white shadow-sm sm:rounded-lg {$$props.class || ''}">
    {#if $$slots.header}
        <div class="border-b border-gray-200 bg-white px-6 py-4">
            <slot name="header" />
        </div>
    {/if}
    
    <div class="p-6 text-gray-900">
        <slot />
    </div>
</div>
