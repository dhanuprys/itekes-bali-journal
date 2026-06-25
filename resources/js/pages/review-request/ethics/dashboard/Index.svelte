<script lang="ts">
    import LayoutComposer from '@/layouts/LayoutComposer.svelte';
    import AppLayout from '@/layouts/AppLayout.svelte';
    import { type BreadcrumbItem } from '@/types';
    import Heading from '@/components/Heading.svelte';
    import * as Card from '@/components/ui/card';
    import { Badge } from '@/components/ui/badge';
    import { Button } from '@/components/ui/button';
    import { router } from '@inertiajs/svelte';
    import Pagination from '@/components/Pagination.svelte';

    let { submissions } = $props();

    const breadcrumbs: BreadcrumbItem[] = [
        { title: 'Permintaan Review', href: '#' },
        { title: 'Etik', href: '#' },
        { title: 'Dashboard', href: '#' },
    ];

    function getStatusVariant(status: string) {
        switch (status) {
            case 'approved':
                return 'default';
            case 'rejected':
                return 'destructive';
            case 'revision_needed':
                return 'destructive';
            case 'need_review':
                return 'secondary';
            default:
                return 'outline';
        }
    }

    function getCategoryLabel(category: string) {
        return category === 'clinical' ? 'Etik Klinik / Uji Coba Hewan' : 'Etik Non Klinis';
    }

    function getStageLabel(stage: string) {
        return stage === 'proposal' ? 'Proposal' : 'Output';
    }
</script>

<svelte:head>
    <title>Dashboard Etik</title>
</svelte:head>

<AppLayout {breadcrumbs}>
    <LayoutComposer>
        {#snippet header()}
            <Heading title="Dashboard Pengajuan Etik" description="Pantau status pengajuan ethical clearance Anda." />
        {/snippet}

        <div class="space-y-4">
            <Card.Root>
                <Card.Header>
                    <Card.Title>Semua Pengajuan</Card.Title>
                    <Card.Description>Riwayat seluruh pengajuan ethical clearance Anda.</Card.Description>
                </Card.Header>
                <Card.Content>
                    {#if submissions.data.length === 0}
                        <div class="text-center py-10 text-muted-foreground">
                            Belum ada pengajuan. Silakan buat pengajuan baru melalui halaman Proposal.
                        </div>
                    {:else}
                        <div class="space-y-3">
                            {#each submissions.data as submission (submission.id)}
                                <div class="flex items-center justify-between border rounded-lg p-4 hover:bg-muted/50 transition-colors">
                                    <div class="space-y-1">
                                        <p class="font-medium">{getCategoryLabel(submission.category)}</p>
                                        <div class="flex items-center gap-2 text-sm text-muted-foreground">
                                            <span>Tahap: {getStageLabel(submission.stage)}</span>
                                            <span>·</span>
                                            <span>{new Date(submission.created_at).toLocaleDateString('id-ID')}</span>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <Badge variant={getStatusVariant(submission.status)}>
                                            {submission.status.replace('_', ' ').toUpperCase()}
                                        </Badge>
                                        <Button
                                            variant="outline"
                                            size="sm"
                                            onclick={() => router.visit(route('apply.ethics.proposal.show', submission.id))}
                                        >
                                            Detail
                                        </Button>
                                    </div>
                                </div>
                            {/each}
                        </div>
                    {/if}
                </Card.Content>
            </Card.Root>

            <div class="mt-4">
                <Pagination meta={submissions} />
            </div>
        </div>
    </LayoutComposer>
</AppLayout>
