<script lang="ts">
    import AppLayout from '@/layouts/app/app-sidebar-layout.svelte';
    import type { BreadcrumbItemType } from '@/types';
    import type { Snippet } from 'svelte';
    import { Toaster } from '@/components/ui/sonner';
    import { ModeWatcher } from 'mode-watcher';

    interface Props {
        breadcrumbs?: BreadcrumbItemType[];
        children?: Snippet;
    }

    let { breadcrumbs = [], children }: Props = $props();
</script>

<ModeWatcher />

<AppLayout {breadcrumbs}>
    {@render children?.()}
    <Toaster />
</AppLayout>
