<script>
    import { Link, router } from '@inertiajs/svelte';
    
    export let href = '';
    export let method = 'get';
    export let as = 'a';
</script>

{#if as === 'button'}
    <button
        type="submit"
        class="block w-full px-4 py-2 text-start text-sm leading-5 text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out"
        on:click={() => router.visit(href, { method })}
    >
        <slot />
    </button>
{:else}
    <Link
        {href}
        class="block px-4 py-2 text-sm leading-5 text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out"
    >
        <slot />
    </Link>
{/if}
