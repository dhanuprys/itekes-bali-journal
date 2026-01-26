<script lang="ts">
    import LayoutComposer from '@/layouts/LayoutComposer.svelte';
    import AppLayout from '@/layouts/AppLayout.svelte';
    import { type BreadcrumbItem } from '@/types';
    import Heading from '@/components/Heading.svelte';
    import * as Card from '@/components/ui/card';
    import { Badge } from '@/components/ui/badge';
    import { Button } from '@/components/ui/button';
    import { Separator } from '@/components/ui/separator';
    import { router } from '@inertiajs/svelte';

    let { submission, detail } = $props();

    const breadcrumbs: BreadcrumbItem[] = [
        { title: 'Permintaan Review', href: '#' },
        { title: 'Pengabdian Masyarakat', href: '#' },
        { title: 'Riwayat Revisi', href: route('apply.community_service.revisions', submission.id) },
        { title: 'Detail Revisi', href: '#' },
    ];

    function formatDate(dateString: string) {
        return new Date(dateString).toLocaleDateString('id-ID', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
        });
    }
</script>

<svelte:head>
    <title>Detail Revisi - {detail.title || 'Submission'}</title>
</svelte:head>

<AppLayout {breadcrumbs}>
    <LayoutComposer>
        {#snippet header()}
            <Heading title="Detail Revisi" description="Informasi lengkap revisi submission." />
        {/snippet}

        {#snippet actions()}
            <Button variant="outline" onclick={() => router.visit(route('apply.community_service.revisions', submission.id))}>
                Kembali ke Riwayat
            </Button>
        {/snippet}

        {#snippet children()}
            <div class="space-y-6">
                <Card.Root>
                    <Card.Header>
                        <div class="flex items-center justify-between">
                            <div class="space-y-1">
                                <Card.Title>{detail.final_title || detail.title}</Card.Title>
                                <Card.Description>Dibuat pada {formatDate(detail.created_at)}</Card.Description>
                            </div>
                        </div>
                    </Card.Header>
                    <Card.Content class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <h4 class="text-sm font-semibold text-muted-foreground">Ketua Pelaksana</h4>
                                <p>{detail.final_leader_name || detail.leader_name} ({detail.leader_nidn})</p>
                            </div>
                            <div>
                                <h4 class="text-sm font-semibold text-muted-foreground">Program Studi</h4>
                                <p>{detail.study_program?.name || '-'}</p>
                            </div>
                            <div>
                                <h4 class="text-sm font-semibold text-muted-foreground">Skema</h4>
                                <p>{detail.schema?.name || '-'}</p>
                            </div>
                            <div>
                                <h4 class="text-sm font-semibold text-muted-foreground">Target</h4>
                                <p>{detail.target?.name || '-'}</p>
                            </div>
                            <div>
                                <h4 class="text-sm font-semibold text-muted-foreground">Usulan Biaya</h4>
                                <p>Rp {new Intl.NumberFormat('id-ID').format(detail.budget)}</p>
                            </div>
                        </div>

                        <Separator />

                        <div>
                            <h4 class="text-sm font-medium mb-2">Dokumen</h4>
                            <div class="space-y-2">
                                {#if detail.proposal_path}
                                    <div class="flex items-center gap-4">
                                        <div class="p-3 border rounded bg-muted/50 flex-1">
                                            <span class="text-sm font-mono">Proposal</span>
                                        </div>
                                        <Button href={`/storage/${detail.proposal_path}`} target="_blank" variant="outline" size="sm">Unduh</Button>
                                    </div>
                                {/if}
                                {#if detail.final_report_path}
                                    <div class="flex items-center gap-4">
                                        <div class="p-3 border rounded bg-muted/50 flex-1">
                                            <span class="text-sm font-mono">Laporan Akhir</span>
                                        </div>
                                        <Button href={`/storage/${detail.final_report_path}`} target="_blank" variant="outline" size="sm">
                                            Unduh
                                        </Button>
                                    </div>
                                {/if}
                                {#if detail.manuscript_path}
                                    <div class="flex items-center gap-4">
                                        <div class="p-3 border rounded bg-muted/50 flex-1">
                                            <span class="text-sm font-mono">Naskah Publikasi</span>
                                        </div>
                                        <Button href={`/storage/${detail.manuscript_path}`} target="_blank" variant="outline" size="sm">Unduh</Button>
                                    </div>
                                {/if}
                            </div>
                        </div>
                    </Card.Content>
                </Card.Root>

                {#if detail.members && detail.members.length > 0}
                    <Card.Root>
                        <Card.Header>
                            <Card.Title>Anggota Tim</Card.Title>
                        </Card.Header>
                        <Card.Content>
                            <div class="space-y-2">
                                {#each detail.members as member}
                                    <div class="p-3 border rounded bg-muted/20">
                                        <p class="font-medium">{member.name}</p>
                                    </div>
                                {/each}
                            </div>
                        </Card.Content>
                    </Card.Root>
                {/if}

                {#if detail.comments && detail.comments.length > 0}
                    <Card.Root>
                        <Card.Header>
                            <Card.Title>Komentar Reviewer</Card.Title>
                            <Card.Description>Komentar dan umpan balik dari reviewer</Card.Description>
                        </Card.Header>
                        <Card.Content>
                            <div class="space-y-4">
                                {#each detail.comments as comment}
                                    <div class="border rounded-lg p-4 bg-muted/30">
                                        <div class="flex items-start justify-between mb-2">
                                            <div>
                                                <p class="font-medium">{comment.user?.name || 'Reviewer'}</p>
                                                <p class="text-xs text-muted-foreground">
                                                    {formatDate(comment.created_at)}
                                                </p>
                                            </div>
                                        </div>
                                        <p class="text-sm whitespace-pre-wrap">{comment.content}</p>
                                    </div>
                                {/each}
                            </div>
                        </Card.Content>
                    </Card.Root>
                {/if}
            </div>
        {/snippet}
    </LayoutComposer>
</AppLayout>
