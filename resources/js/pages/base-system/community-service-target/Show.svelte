<script lang="ts">
    import LayoutComposer from '@/layouts/LayoutComposer.svelte';
    import Heading from '@/components/Heading.svelte';
    import AppLayout from '@/layouts/AppLayout.svelte';
    import { Button } from '@/components/ui/button';
    import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
    import { ArrowLeft } from 'lucide-svelte';
    import { router } from '@inertiajs/svelte';

    let { communityServiceTarget } = $props();

    let breadcrumbs = $derived([
        {
            title: 'Target PKM',
            href: '/master/community-service-target',
        },
        {
            title: communityServiceTarget?.title || 'Detail Target PKM',
            href: '#',
        },
    ]);
</script>

<svelte:head>
    <title>Detail Target PKM - {communityServiceTarget?.title || 'Loading...'}</title>
</svelte:head>

<AppLayout {breadcrumbs}>
    <LayoutComposer>
        {#snippet backButton()}
            <Button variant="outline" onclick={() => router.visit('/master/community-service-target')}>
                <ArrowLeft class="mr-2 h-4 w-4" />
                Kembali
            </Button>
        {/snippet}

        {#snippet header()}
            <Heading
                title={communityServiceTarget?.title || 'Detail Target PKM'}
                description="Detail informasi target pengabdian kepada masyarakat"
            />
        {/snippet}

        <div class="grid gap-6 md:grid-cols-2">
            <Card>
                <CardHeader>
                    <CardTitle>Informasi Dasar</CardTitle>
                </CardHeader>
                <CardContent class="space-y-4">
                    <div class="grid grid-cols-3 gap-4">
                        <div class="text-sm font-medium text-muted-foreground">Judul</div>
                        <div class="col-span-2 text-sm">{communityServiceTarget?.title || '-'}</div>
                    </div>
                    <div class="grid grid-cols-3 gap-4">
                        <div class="text-sm font-medium text-muted-foreground">Deskripsi</div>
                        <div class="col-span-2 text-sm">{communityServiceTarget?.description || '-'}</div>
                    </div>
                    <div class="grid grid-cols-3 gap-4">
                        <div class="text-sm font-medium text-muted-foreground">Dibuat</div>
                        <div class="col-span-2 text-sm">
                            {communityServiceTarget?.created_at ? new Date(communityServiceTarget.created_at).toLocaleDateString() : '-'}
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>
    </LayoutComposer>
</AppLayout>
