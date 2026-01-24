<script lang="ts">
    import { type Snippet } from 'svelte';

    let {
        children,
        header,
        actions,
        filters,
        backButton,
    }: {
        children: Snippet;
        header: Snippet;
        actions?: Snippet;
        filters?: Snippet;
        backButton?: Snippet;
    } = $props();
</script>

<div class="px-4 py-6">
    {#if backButton}
        <div class="mb-4">
            {@render backButton()}
        </div>
    {/if}

    <div class="flex items-center justify-between mb-6">
        <div>
            {@render header()}
        </div>
        {#if actions}
            <div class="flex items-center gap-2">
                {@render actions()}
            </div>
        {/if}
    </div>

    {#if filters}
        <div class="flex items-center space-x-2 mb-4">
            {@render filters()}
        </div>
    {/if}

    {@render children()}
</div>
