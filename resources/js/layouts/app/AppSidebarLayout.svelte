<script lang="ts">
    import AppContent from '@/components/AppContent.svelte';
    import AppShell from '@/components/AppShell.svelte';
    import AppSidebar from '@/components/AppSidebar.svelte';
    import AppSidebarHeader from '@/components/AppSidebarHeader.svelte';
    import type { BreadcrumbItemType } from '@/types';
    import type { Snippet } from 'svelte';
    import { page, router } from '@inertiajs/svelte';
    import { Button } from '@/components/ui/button';
    import { LogOut, UserIcon } from 'lucide-svelte';

    interface Props {
        breadcrumbs?: BreadcrumbItemType[];
        children?: Snippet;
    }

    let { breadcrumbs = [], children }: Props = $props();

    let isImpersonating = $derived($page.props.auth?.is_impersonating);
    let currentUser = $derived($page.props.auth?.user);
</script>

<AppShell variant="sidebar">
    <AppSidebar />
    <AppContent variant="sidebar" class="overflow-x-hidden flex flex-col">
        {#if isImpersonating}
            <div class="bg-red-600 text-white px-4 py-2 flex items-center justify-between shadow-md z-50 shrink-0">
                <div class="flex items-center gap-2 text-sm font-medium">
                    <UserIcon class="h-4 w-4" />
                    <span class="hidden sm:inline">Anda sedang impersonasi sebagai:</span>
                    <span class="sm:hidden">Impersonasi:</span>
                    {currentUser?.name}
                </div>
                <Button
                    variant="secondary"
                    size="sm"
                    class="h-7 text-xs gap-1.5 bg-white text-red-600 hover:bg-red-50 hover:text-red-700 font-semibold"
                    onclick={() => router.post(route('users.users.impersonate.leave'))}
                >
                    <LogOut class="h-3 w-3" />
                    <span class="hidden sm:inline">Kembali ke Admin</span>
                    <span class="sm:hidden">Kembali</span>
                </Button>
            </div>
        {/if}
        <AppSidebarHeader {breadcrumbs} />
        <div class="w-full mx-auto max-w-400">
            {@render children?.()}
        </div>
    </AppContent>
</AppShell>
