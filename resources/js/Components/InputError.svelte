<script>
    export let message = '';
</script>

{#if message}
    <p class="text-sm text-red-600">
        {message}
    </p>
{/if}
