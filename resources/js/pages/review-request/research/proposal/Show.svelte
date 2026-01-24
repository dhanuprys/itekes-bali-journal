<script lang="ts">
    import LayoutComposer from '@/layouts/LayoutComposer.svelte';
    import AppLayout from '@/layouts/AppLayout.svelte';
    import { type BreadcrumbItem } from '@/types';
    import Heading from '@/components/Heading.svelte';
    import * as Card from '@/components/ui/card';
    import { Badge } from '@/components/ui/badge';
    import { Button } from '@/components/ui/button';
    import { Separator } from '@/components/ui/separator';

    let { submission } = $props();

    // Derived from submission.latest_detail
    let detail = $derived(submission.latest_detail);

    const breadcrumbs: BreadcrumbItem[] = [
        { title: 'Permintaan Review', href: '#' },
        { title: 'Penelitian', href: '#' },
        { title: 'Proposal', href: '/apply/research' },
        { title: 'Detail', href: '#' },
    ];
</script>

<svelte:head>
    <title>Detail Proposal</title>
</svelte:head>

<AppLayout {breadcrumbs}>
    <LayoutComposer>
        {#snippet header()}
            <Heading title="Detail Proposal" description="Informasi lengkap proposal penelitian." />
        {/snippet}

        {#snippet children()}
            <div class="space-y-6">
                <Card.Root>
                    <Card.Header>
                        <div class="flex items-center justify-between">
                            <div class="space-y-1">
                                <Card.Title>{detail.title}</Card.Title>
                                <Card.Description>Diajukan pada {new Date(submission.created_at).toLocaleDateString('id-ID')}</Card.Description>
                            </div>
                            <Badge variant="outline" class="text-base px-4 py-1 uppercase">{submission.status.replace('_', ' ')}</Badge>
                        </div>
                    </Card.Header>
                    <Card.Content class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <h4 class="text-sm font-semibold text-muted-foreground">Ketua Peneliti</h4>
                                <p>{detail.leader_name} ({detail.leader_nidn})</p>
                            </div>
                            <div>
                                <h4 class="text-sm font-semibold text-muted-foreground">Program Studi</h4>
                                <p>{detail.study_program?.name || '-'}</p>
                            </div>
                            <div>
                                <h4 class="text-sm font-semibold text-muted-foreground">Skema</h4>
                                <p>{detail.research_schema?.name || '-'}</p>
                            </div>
                            <div>
                                <h4 class="text-sm font-semibold text-muted-foreground">Target Luaran</h4>
                                <p>{detail.research_target?.name || '-'}</p>
                            </div>
                            <div>
                                <h4 class="text-sm font-semibold text-muted-foreground">Usulan Biaya</h4>
                                <p>Rp {new Intl.NumberFormat('id-ID').format(detail.budget)}</p>
                            </div>
                        </div>

                        <Separator />

                        <div>
                            <h4 class="text-sm font-medium mb-2">Dokumen Proposal</h4>
                            <div class="flex items-center gap-4">
                                <div class="p-3 border rounded bg-muted/50">
                                    <span class="text-sm font-mono">File Proposal</span>
                                </div>
                                <Button href={`/storage/${detail.proposal_path}`} target="_blank" variant="outline" size="sm">Unduh File</Button>
                            </div>
                        </div>
                    </Card.Content>
                </Card.Root>

                <!-- Placeholder for Review History or Comments -->
                <Card.Root>
                    <Card.Header>
                        <Card.Title>Riwayat Review</Card.Title>
                    </Card.Header>
                    <Card.Content>
                        <p class="text-muted-foreground text-sm">Belum ada riwayat review.</p>
                    </Card.Content>
                </Card.Root>
            </div>
        {/snippet}
    </LayoutComposer>
</AppLayout>
