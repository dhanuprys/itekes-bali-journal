<script lang="ts">
    import LayoutComposer from '@/layouts/LayoutComposer.svelte';
    import AppLayout from '@/layouts/AppLayout.svelte';
    import { type BreadcrumbItem } from '@/types';
    import Heading from '@/components/Heading.svelte';

    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: 'Reviewer',
            href: '#',
        },
        {
            title: 'Pengabdian Masyarakat',
            href: '#',
        },
        {
            title: 'Laporan Akhir',
            href: '#',
        },
    ];
</script>

<svelte:head>
    <title>Laporan Akhir</title>
</svelte:head>

<AppLayout {breadcrumbs}>
    <LayoutComposer>
        {#snippet header()}
            <Heading title="Laporan Akhir" description="Kelola laporan akhir pengabdian masyarakat" />
        {/snippet}

        {#snippet children()}
            <div class="flex h-96 items-center justify-center rounded-lg border border-dashed shadow-sm">
                <div class="flex flex-col items-center gap-1 text-center">
                    <h3 class="text-2xl font-bold tracking-tight">Konten Placeholder</h3>
                    <p class="text-sm text-muted-foreground">Bagian ini sedang dalam pengembangan.</p>
                </div>
            </div>
        {/snippet}
    </LayoutComposer>
</AppLayout>
