<script>
    import { onMount, onDestroy } from 'svelte';
    
    export let align = 'right';
    export let width = '48';
    export let contentClasses = 'py-1 bg-white';
    
    let open = false;
    
    $: widthClass = {
        '48': 'w-48',
        '64': 'w-64'
    }[width] || 'w-48';
    
    $: alignmentClasses = align === 'left' 
        ? 'ltr:origin-top-left rtl:origin-top-right start-0'
        : align === 'right'
        ? 'ltr:origin-top-right rtl:origin-top-left end-0'
        : 'origin-top';
    
    function closeOnEscape(e) {
        if (open && e.key === 'Escape') {
            open = false;
        }
    }
    
    onMount(() => {
        document.addEventListener('keydown', closeOnEscape);
    });
    
    onDestroy(() => {
        document.removeEventListener('keydown', closeOnEscape);
    });
</script>

<div class="relative">
    <div on:click={() => open = !open}>
        <slot name="trigger" />
    </div>

    {#if open}
        <div class="fixed inset-0 z-40" on:click={() => open = false}></div>
        
        <div
            class="absolute z-50 mt-2 rounded-md shadow-lg {widthClass} {alignmentClasses}"
            on:click={() => open = false}
        >
            <div class="rounded-md ring-1 ring-black ring-opacity-5 {contentClasses}">
                <slot name="content" />
            </div>
        </div>
    {/if}
</div>
