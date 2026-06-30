<script lang="ts">
    import LayoutComposer from '@/layouts/layout-composer.svelte';
    import AppLayout from '@/layouts/app-layout.svelte';
    import { type BreadcrumbItem } from '@/types';
    import Heading from '@/components/heading.svelte';
    import * as Table from '@/components/ui/table';
    import * as Card from '@/components/ui/card';
    import { Badge } from '@/components/ui/badge';
    import { Button } from '@/components/ui/button';
    import Pagination from '@/components/pagination.svelte';

    let { submissions } = $props();

    const breadcrumbs: BreadcrumbItem[] = [
        { title: 'Reviewer Area', href: '#' },
        { title: 'Pengabdian Masyarakat', href: route('review.community_service.index') },
        { title: 'Laporan Akhir', href: '#' },
    ];

    function getStatusVariant(status: string) {
        switch (status) {
            case 'need_review':
                return 'default';
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
    <title>Daftar Review Laporan Akhir</title>
</svelte:head>

<AppLayout {breadcrumbs}>
    <LayoutComposer>
        {#snippet header()}
            <Heading title="Review Laporan Akhir" description="Daftar laporan akhir pengabdian yang perlu direview." />
        {/snippet}

        <div class="space-y-4">
            <Card.Root>
                <Card.Header>
                    <Card.Title>Daftar Laporan Akhir</Card.Title>
                    <Card.Description>Laporan yang ditugaskan kepada Anda.</Card.Description>
                </Card.Header>
                <Card.Content>
                    {#if submissions.data.length === 0}
                        <div class="text-center py-10 text-muted-foreground">Tidak ada laporan akhir saat ini.</div>
                    {:else}
                        <Table.Root>
                            <Table.Header>
                                <Table.Row>
                                    <Table.Head>Judul</Table.Head>
                                    <Table.Head>Pengusul</Table.Head>
                                    <Table.Head>Status</Table.Head>
                                    <Table.Head>Tanggal Masuk</Table.Head>
                                    <Table.Head class="text-right">Aksi</Table.Head>
                                </Table.Row>
                            </Table.Header>
                            <Table.Body>
                                {#each submissions.data as submission (submission.id)}
                                    <Table.Row>
                                        <Table.Cell class="font-medium max-w-xs truncate">
                                            {submission.latest_detail?.final_title || submission.latest_detail?.title || 'Judul Tidak Tersedia'}
                                        </Table.Cell>
                                        <Table.Cell>{submission.user?.name}</Table.Cell>
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
                                                href={route('review.community_service.final_report.show', submission.id)}
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

            <div class="mt-4">
                <Pagination meta={submissions} />
            </div>
        </div>
    </LayoutComposer>
</AppLayout>
