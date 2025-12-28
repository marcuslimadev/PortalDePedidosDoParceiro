<script>
    import { Link } from '@inertiajs/svelte';
    
    export let href = '';
    export let active = false;
    
    $: classes = active
        ? 'block w-full px-4 py-2 text-start text-sm leading-5 text-gray-700 bg-gray-100 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out border-l-4 border-indigo-400'
        : 'block w-full px-4 py-2 text-start text-sm leading-5 text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out border-l-4 border-transparent';
</script>

<Link {href} class={classes}>
    <slot />
</Link>
