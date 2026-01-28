<script lang="ts">
    import LayoutComposer from '@/layouts/LayoutComposer.svelte';
    import AppLayout from '@/layouts/AppLayout.svelte';
    import { type BreadcrumbItem } from '@/types';
    import Heading from '@/components/Heading.svelte';
    import * as Table from '@/components/ui/table';
    import * as Card from '@/components/ui/card';
    import { Badge } from '@/components/ui/badge';
    import { Button } from '@/components/ui/button';

    let { submissions } = $props();

    const breadcrumbs: BreadcrumbItem[] = [
        { title: 'Reviewer Area', href: '#' },
        { title: 'Dashboard', href: '#' },
    ];

    function getStageLabel(stage: string) {
        switch (stage) {
            case 'proposal':
                return 'Proposal';
            case 'progress_report':
                return 'Laporan Kemajuan';
            case 'final_report':
                return 'Laporan Akhir';
            default:
                return stage;
        }
    }

    function getReviewRoute(submission: any) {
        if (submission.stage === 'proposal') return route('review.research.proposal.show', submission.id);
        if (submission.stage === 'progress_report') return route('review.research.progress_report.show', submission.id);
        if (submission.stage === 'final_report') return route('review.research.final_report.show', submission.id);
        return '#';
    }

    function getStatusVariant(status: string) {
        switch (status) {
            case 'need_review':
                return 'default'; // Highlight actionable
            case 'approved':
                return 'secondary';
            case 'rejected':
                return 'destructive';
            case 'revision_needed':
                return 'destructive';
            default:
                return 'outline';
        }
    }
</script>

<svelte:head>
    <title>Reviewer Dashboard - Penelitian</title>
</svelte:head>

<AppLayout {breadcrumbs}>
    <LayoutComposer>
        {#snippet header()}
            <Heading title="Dashboard Reviewer" description="Kelola tugas review penelitian Anda." />
        {/snippet}

        <Card.Root>
            <Card.Header>
                <Card.Title>Daftar Tugas Review (Penelitian)</Card.Title>
                <Card.Description>Review proposal dan laporan yang ditugaskan kepada Anda.</Card.Description>
            </Card.Header>
            <Card.Content>
                {#if submissions.data.length === 0}
                    <div class="text-center py-10 text-muted-foreground">Tidak ada tugas review saat ini.</div>
                {:else}
                    <Table.Root>
                        <Table.Header>
                            <Table.Row>
                                <Table.Head>Judul</Table.Head>
                                <Table.Head>Pengusul</Table.Head>
                                <Table.Head>Tahap</Table.Head>
                                <Table.Head>Status</Table.Head>
                                <Table.Head>Tanggal Masuk</Table.Head>
                                <Table.Head class="text-right">Aksi</Table.Head>
                            </Table.Row>
                        </Table.Header>
                        <Table.Body>
                            {#each submissions.data as submission (submission.id)}
                                <Table.Row>
                                    <Table.Cell class="font-medium max-w-xs truncate">
                                        {submission.latest_detail?.title || submission.latest_detail?.final_title || 'Judul Tidak Tersedia'}
                                    </Table.Cell>
                                    <Table.Cell>{submission.user?.name}</Table.Cell>
                                    <Table.Cell>
                                        <Badge variant="outline">{getStageLabel(submission.stage)}</Badge>
                                    </Table.Cell>
                                    <Table.Cell>
                                        <Badge variant={getStatusVariant(submission.status)}>
                                            {submission.status.replace('_', ' ').toUpperCase()}
                                        </Badge>
                                    </Table.Cell>
                                    <Table.Cell>{new Date(submission.updated_at).toLocaleDateString('id-ID')}</Table.Cell>
                                    <Table.Cell class="text-right">
                                        <Button
                                            size="sm"
                                            variant={submission.status === 'need_review' ? 'default' : 'outline'}
                                            href={getReviewRoute(submission)}
                                        >
                                            {submission.status === 'need_review' ? 'Review' : 'Detail'}
                                        </Button>
                                    </Table.Cell>
                                </Table.Row>
                            {/each}
                        </Table.Body>
                    </Table.Root>
                {/if}
            </Card.Content>
        </Card.Root>
    </LayoutComposer>
</AppLayout>
