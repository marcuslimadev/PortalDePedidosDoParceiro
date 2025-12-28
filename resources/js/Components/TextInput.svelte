<script>
    export let value = '';
    export let type = 'text';
    export let autofocus = false;
    
    let input;
    
    $: if (input && autofocus) {
        input.focus();
    }
    
    export function focus() {
        input?.focus();
    }
</script>

<input
    bind:this={input}
    bind:value
    {type}
    class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
    on:input
    on:change
    on:blur
    {...$$restProps}
/>
