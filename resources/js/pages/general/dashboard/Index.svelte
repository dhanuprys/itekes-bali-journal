<script lang="ts">
    import AppLayout from '@/layouts/AppLayout.svelte';
    import { type BreadcrumbItem } from '@/types';
    import { Construction } from 'lucide-svelte';

    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: 'Dashboard',
            href: '/dashboard',
        },
    ];
</script>

<svelte:head>
    <title>Dashboard</title>
</svelte:head>

<AppLayout {breadcrumbs}>
    <div class="flex h-[calc(100vh-7rem)] items-center justify-center p-4">
        <div class="flex flex-col items-center justify-center space-y-4 text-center">
            <div class="flex h-20 w-20 items-center justify-center rounded-full bg-neutral-100 dark:bg-neutral-800">
                <Construction class="h-10 w-10 text-neutral-500 dark:text-neutral-400" />
            </div>
            <div class="space-y-2">
                <h3 class="text-2xl font-semibold tracking-tight text-neutral-900 dark:text-neutral-100">Coming Soon ✨</h3>
                <p class="max-w-[500px] text-neutral-500 dark:text-neutral-400">
                    Mohon maaf, halaman dashboard saat ini sedang dikembangkan dan akan tersedia beberapa saat lagi.
                </p>
            </div>
        </div>
    </div>
</AppLayout>
