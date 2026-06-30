<script lang="ts">
    import LayoutComposer from '@/layouts/layout-composer.svelte';
    import AppLayout from '@/layouts/app-layout.svelte';
    import { type BreadcrumbItem } from '@/types';
    import Heading from '@/components/heading.svelte';
    import { Button } from '@/components/ui/button';
    import * as Card from '@/components/ui/card';
    import { Badge } from '@/components/ui/badge';
    import { router } from '@inertiajs/svelte';
    import { ChevronRightIcon } from 'lucide-svelte';
    import HeadingSmall from '@/components/heading-small.svelte';

    let { submission } = $props();

    const breadcrumbs: BreadcrumbItem[] = [
        { title: 'Permintaan Review', href: '#' },
        { title: 'Pengabdian Masyarakat', href: '#' },
        { title: 'Riwayat Revisi', href: '#' },
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

    function formatDate(dateString: string) {
        return new Date(dateString).toLocaleDateString('id-ID', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric',
        });
    }
</script>

<svelte:head>
    <title>Riwayat Revisi - {submission.latest_detail?.title || 'Submission'}</title>
</svelte:head>

<AppLayout {breadcrumbs}>
    <LayoutComposer>
        {#snippet header()}
            <Heading title="Riwayat Revisi" description="Lihat semua detail revisi untuk submission ini." />
        {/snippet}

        {#snippet actions()}
            <Button variant="outline" onclick={() => router.visit(route('apply.community_service.index'))}>Kembali</Button>
        {/snippet}

        <div class="space-y-6">
            <Card.Root>
                <Card.Header>
                    <Card.Title>Informasi Submission</Card.Title>
                    <Card.Description>{submission.latest_detail?.title || 'Judul Tidak Tersedia'}</Card.Description>
                </Card.Header>
                <Card.Content>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm font-medium text-muted-foreground">Status</p>
                            <Badge variant={getStatusVariant(submission.status)}>
                                {submission.status.replace('_', ' ').toUpperCase()}
                            </Badge>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-muted-foreground">Tahap</p>
                            <p class="text-sm">{submission.stage}</p>
                        </div>
                    </div>
                </Card.Content>
            </Card.Root>

            <div class="space-y-4">
                <HeadingSmall title="Detail Revisi" description="Daftar semua versi submission." />
                {#if submission.details && submission.details.length > 0}
                    <div class="space-y-3">
                        {#each submission.details as detail, index (detail.id)}
                            <div class="flex items-center justify-between p-4 border rounded-lg bg-card hover:bg-accent/5 transition-colors">
                                <div class="flex items-center gap-4">
                                    <Badge variant="outline" class="h-8 w-8 flex items-center justify-center rounded-full font-mono bg-background">
                                        {submission.details.length - index}
                                    </Badge>
                                    <div class="space-y-1">
                                        <p class="font-medium leading-none">
                                            {detail.final_title || detail.title || 'Judul Tidak Tersedia'}
                                        </p>
                                        <p class="text-sm text-muted-foreground">
                                            Dibuat pada {formatDate(detail.created_at)}
                                        </p>
                                    </div>
                                </div>
                                <Button
                                    variant="ghost"
                                    size="sm"
                                    class="gap-2"
                                    onclick={() =>
                                        router.visit(
                                            route('apply.community_service.revision', {
                                                id: submission.id,
                                                revision_id: detail.id,
                                            }),
                                        )}
                                >
                                    Detail <ChevronRightIcon class="h-4 w-4" />
                                </Button>
                            </div>
                        {/each}
                    </div>
                {:else}
                    <div class="text-center py-10 text-muted-foreground">Belum ada detail revisi.</div>
                {/if}
            </div>
        </div>
    </LayoutComposer>
</AppLayout>
