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
    <div class="flex flex-1 flex-col items-center justify-center p-6 lg:p-12">
        <div class="flex max-w-2xl flex-col items-center justify-center space-y-6 text-center py-12 sm:py-24">
            <div class="flex h-20 w-20 items-center justify-center rounded-full bg-muted shadow-sm ring-1 ring-border">
                <Construction class="h-10 w-10 text-muted-foreground" />
            </div>
            <div class="space-y-4">
                <div class="space-y-2">
                    <h3 class="text-2xl font-bold tracking-tight sm:text-3xl">
                        Sistem Informasi<br /> Penelitian dan Pengabdian Terpadu
                    </h3>
                    <p class="text-base font-medium text-muted-foreground">Institut Teknologi dan Kesehatan Bali</p>
                </div>
                <p class="mx-auto max-w-lg border-t border-border/60 pt-4 text-sm text-muted-foreground">
                    Mohon maaf, halaman dashboard saat ini sedang dikembangkan dan akan tersedia beberapa saat lagi.
                </p>
            </div>
        </div>
    </div>
</AppLayout>
