<script>
    export let type = 'info'; // info, success, warning, danger
    
    const colors = {
        info: 'bg-blue-100 text-blue-800',
        success: 'bg-green-100 text-green-800',
        warning: 'bg-yellow-100 text-yellow-800',
        danger: 'bg-red-100 text-red-800',
    };
    
    $: badgeClass = colors[type] || colors.info;
</script>

<span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {badgeClass}">
    <slot />
</span>
