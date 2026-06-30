<script lang="ts">
    import { SidebarInset } from '@/components/ui/sidebar';
    import type { Snippet } from 'svelte';

    type Props = {
        variant?: 'header' | 'sidebar';
        class?: string;
        children: Snippet;
    };

    let { variant, class: className, children }: Props = $props();
</script>

{#if variant === 'sidebar'}
    <SidebarInset class={className}>
        {@render children?.()}
    </SidebarInset>
{:else}
    <main class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl {className}">
        {@render children?.()}
    </main>
{/if}
