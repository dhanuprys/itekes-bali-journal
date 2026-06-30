<script lang="ts">
    import LayoutComposer from '@/layouts/layout-composer.svelte';
    import Heading from '@/components/heading.svelte';
    import AppLayout from '@/layouts/app-layout.svelte';
    import { Button } from '@/components/ui/button';
    import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
    import { ArrowLeft } from 'lucide-svelte';
    import { router } from '@inertiajs/svelte';

    let { researchTarget } = $props();

    let breadcrumbs = $derived([
        {
            title: 'Target Riset',
            href: '/master/research-target',
        },
        {
            title: researchTarget?.title || 'Detail Target Riset',
            href: '#',
        },
    ]);
</script>

<svelte:head>
    <title>Detail Target Riset - {researchTarget?.title || 'Loading...'}</title>
</svelte:head>

<AppLayout {breadcrumbs}>
    <LayoutComposer>
        {#snippet backButton()}
            <Button variant="outline" onclick={() => router.visit('/master/research-target')}>
                <ArrowLeft class="mr-2 h-4 w-4" />
                Kembali
            </Button>
        {/snippet}

        {#snippet header()}
            <Heading title={researchTarget?.title || 'Detail Target Riset'} description="Detail informasi target riset" />
        {/snippet}

        <div class="grid gap-6 md:grid-cols-2">
            <Card>
                <CardHeader>
                    <CardTitle>Informasi Dasar</CardTitle>
                </CardHeader>
                <CardContent class="space-y-4">
                    <div class="grid grid-cols-3 gap-4">
                        <div class="text-sm font-medium text-muted-foreground">Judul</div>
                        <div class="col-span-2 text-sm">{researchTarget?.title || '-'}</div>
                    </div>
                    <div class="grid grid-cols-3 gap-4">
                        <div class="text-sm font-medium text-muted-foreground">Deskripsi</div>
                        <div class="col-span-2 text-sm">{researchTarget?.description || '-'}</div>
                    </div>
                    <div class="grid grid-cols-3 gap-4">
                        <div class="text-sm font-medium text-muted-foreground">Dibuat</div>
                        <div class="col-span-2 text-sm">
                            {researchTarget?.created_at ? new Date(researchTarget.created_at).toLocaleDateString() : '-'}
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>
    </LayoutComposer>
</AppLayout>
