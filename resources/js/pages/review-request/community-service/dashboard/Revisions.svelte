<script lang="ts">
    import LayoutComposer from '@/layouts/LayoutComposer.svelte';
    import AppLayout from '@/layouts/AppLayout.svelte';
    import { type BreadcrumbItem } from '@/types';
    import Heading from '@/components/Heading.svelte';
    import { Button } from '@/components/ui/button';
    import * as Table from '@/components/ui/table';
    import * as Card from '@/components/ui/card';
    import { Badge } from '@/components/ui/badge';
    import { router } from '@inertiajs/svelte';

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

        {#snippet children()}
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

                <Card.Root>
                    <Card.Header>
                        <Card.Title>Detail Revisi</Card.Title>
                        <Card.Description>Daftar semua versi submission.</Card.Description>
                    </Card.Header>
                    <Card.Content>
                        {#if submission.details && submission.details.length > 0}
                            <div class="space-y-4">
                                {#each submission.details as detail, index}
                                    <Card.Root class="border-2">
                                        <Card.Header>
                                            <div class="flex items-start justify-between">
                                                <div class="space-y-1 flex-1">
                                                    <div class="flex items-center gap-2">
                                                        <Badge variant="outline" class="font-mono">
                                                            v{submission.details.length - index}
                                                        </Badge>
                                                        <Card.Title class="text-base">
                                                            {detail.final_title || detail.title || 'Judul Tidak Tersedia'}
                                                        </Card.Title>
                                                    </div>
                                                    <Card.Description>
                                                        Dibuat pada {formatDate(detail.created_at)}
                                                    </Card.Description>
                                                </div>
                                                <Button
                                                    variant="outline"
                                                    size="sm"
                                                    onclick={() =>
                                                        router.visit(
                                                            route('apply.community_service.revision', {
                                                                id: submission.id,
                                                                revision_id: detail.id,
                                                            }),
                                                        )}
                                                >
                                                    Lihat Detail
                                                </Button>
                                            </div>
                                        </Card.Header>
                                        <Card.Content>
                                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                                                <div>
                                                    <p class="text-muted-foreground font-medium">Ketua Pelaksana</p>
                                                    <p class="mt-1">
                                                        {detail.final_leader_name || detail.leader_name || '-'}
                                                        {#if detail.leader_nidn}
                                                            <span class="text-muted-foreground">({detail.leader_nidn})</span>
                                                        {/if}
                                                    </p>
                                                </div>
                                                <div>
                                                    <p class="text-muted-foreground font-medium">Skema</p>
                                                    <p class="mt-1">{detail.community_service_schema?.name || '-'}</p>
                                                </div>
                                                <div>
                                                    <p class="text-muted-foreground font-medium">Usulan Biaya</p>
                                                    <p class="mt-1">
                                                        {#if detail.budget}
                                                            Rp {new Intl.NumberFormat('id-ID').format(detail.budget)}
                                                        {:else}
                                                            -
                                                        {/if}
                                                    </p>
                                                </div>
                                                <div>
                                                    <p class="text-muted-foreground font-medium">Program Studi</p>
                                                    <p class="mt-1">{detail.study_program?.name || '-'}</p>
                                                </div>
                                                <div>
                                                    <p class="text-muted-foreground font-medium">Target</p>
                                                    <p class="mt-1">{detail.community_service_target?.name || '-'}</p>
                                                </div>
                                                <div>
                                                    <p class="text-muted-foreground font-medium">Dokumen</p>
                                                    <div class="mt-1 flex flex-wrap gap-1">
                                                        {#if detail.proposal_path}
                                                            <Badge variant="secondary" class="text-xs">Proposal</Badge>
                                                        {/if}
                                                        {#if detail.final_report_path}
                                                            <Badge variant="secondary" class="text-xs">Laporan</Badge>
                                                        {/if}
                                                        {#if detail.manuscript_path}
                                                            <Badge variant="secondary" class="text-xs">Naskah</Badge>
                                                        {/if}
                                                        {#if !detail.proposal_path && !detail.final_report_path && !detail.manuscript_path}
                                                            <span class="text-muted-foreground">-</span>
                                                        {/if}
                                                    </div>
                                                </div>
                                            </div>
                                        </Card.Content>
                                    </Card.Root>
                                {/each}
                            </div>
                        {:else}
                            <div class="text-center py-10 text-muted-foreground">Belum ada detail revisi.</div>
                        {/if}
                    </Card.Content>
                </Card.Root>
            </div>
        {/snippet}
    </LayoutComposer>
</AppLayout>
