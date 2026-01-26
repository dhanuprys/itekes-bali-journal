<script lang="ts">
    import LayoutComposer from '@/layouts/LayoutComposer.svelte';
    import AppLayout from '@/layouts/AppLayout.svelte';
    import { type BreadcrumbItem } from '@/types';
    import Heading from '@/components/Heading.svelte';
    import * as Card from '@/components/ui/card';
    import { Badge } from '@/components/ui/badge';
    import { Button } from '@/components/ui/button';
    import { Separator } from '@/components/ui/separator';
    import {
        CalendarIcon,
        UserIcon,
        UsersIcon,
        FileTextIcon,
        BanknoteIcon,
        BookOpenIcon,
        TargetIcon,
        CheckCircleIcon,
        ClockIcon,
        AlertCircleIcon,
    } from 'lucide-svelte';

    import { getStatusConfig } from '@/lib/review-status';

    let { submission } = $props();

    let detail = $derived(submission.latest_detail);
    let members = $derived(detail.members || []);

    const breadcrumbs: BreadcrumbItem[] = [
        { title: 'Permintaan Review', href: '#' },
        { title: 'Pengabdian Masyarakat', href: '#' },
        { title: 'Proposal', href: '/apply/community-service/proposal' },
        { title: 'Detail', href: '#' },
    ];

    let statusConfig = $derived(getStatusConfig(submission.status));
</script>

<svelte:head>
    <title>Detail Proposal</title>
</svelte:head>

<AppLayout {breadcrumbs}>
    <LayoutComposer>
        {#snippet header()}
            <Heading title="Detail Proposal" description="Informasi lengkap proposal pengabdian." />
        {/snippet}

        {#snippet actions()}
            <Badge variant="outline" class={'px-3 py-1 gap-2 flex items-center ' + statusConfig.color}>
                <statusConfig.icon class="h-4 w-4" />
                {statusConfig.label}
            </Badge>
        {/snippet}

        {#snippet children()}
            <div class="space-y-6">
                <Card.Root>
                    <Card.Header>
                        <div class="space-y-1">
                            <Card.Title>{detail.title}</Card.Title>
                            <Card.Description>Diajukan pada {new Date(submission.created_at).toLocaleDateString('id-ID')}</Card.Description>
                        </div>
                    </Card.Header>
                    <Card.Content class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <h4 class="text-sm font-semibold text-muted-foreground">Ketua Pengabdi</h4>
                                <p>{detail.leader_name} ({detail.leader_nidn})</p>
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
                                <h4 class="text-sm font-semibold text-muted-foreground">Target Luaran</h4>
                                <p>{detail.target?.name || '-'}</p>
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
