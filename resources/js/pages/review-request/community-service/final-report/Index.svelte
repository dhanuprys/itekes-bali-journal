<script lang="ts">
    import LayoutComposer from '@/layouts/LayoutComposer.svelte';
    import AppLayout from '@/layouts/AppLayout.svelte';
    import { type BreadcrumbItem } from '@/types';
    import Heading from '@/components/Heading.svelte';
    import * as Table from '@/components/ui/table';
    import * as Card from '@/components/ui/card';
    import { Badge } from '@/components/ui/badge';
    import * as DropdownMenu from '@/components/ui/dropdown-menu';
    import { router } from '@inertiajs/svelte';
    import { MoreHorizontalIcon } from 'lucide-svelte';
    import Pagination from '@/components/Pagination.svelte';

    let { submissions } = $props();

    const breadcrumbs: BreadcrumbItem[] = [
        { title: 'Permintaan Review', href: '#' },
        { title: 'Pengabdian Masyarakat', href: '#' },
        { title: 'Laporan Akhir', href: '#' },
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

    function hasSubmittedReport(submission: any) {
        return !!submission.latest_detail?.final_report_path;
    }
</script>

<svelte:head>
    <title>Laporan Akhir Pengabdian</title>
</svelte:head>

<AppLayout {breadcrumbs}>
    <LayoutComposer>
        {#snippet header()}
            <Heading title="Laporan Akhir" description="Daftar laporan akhir pengabdian Anda." />
        {/snippet}

        <div class="space-y-4">
            <Card.Root>
                <Card.Header>
                    <Card.Title>Daftar Laporan</Card.Title>
                    <Card.Description>Laporan kemajuan yang telah disetujui dan menunggu laporan akhir.</Card.Description>
                </Card.Header>
                <Card.Content>
                    {#if submissions.data.length === 0}
                        <div class="text-center py-10 text-muted-foreground">Belum ada pengabdian yang masuk tahap laporan akhir.</div>
                    {:else}
                        <Table.Root>
                            <Table.Header>
                                <Table.Row>
                                    <Table.Head>Judul</Table.Head>
                                    <Table.Head>Status</Table.Head>
                                    <Table.Head>Tanggal</Table.Head>
                                    <Table.Head class="text-right">Aksi</Table.Head>
                                </Table.Row>
                            </Table.Header>
                            <Table.Body>
                                {#each submissions.data as submission (submission.id)}
                                    <Table.Row>
                                        <Table.Cell class="font-medium">
                                            {submission.latest_detail?.final_title || submission.latest_detail?.title || 'Judul Tidak Tersedia'}
                                        </Table.Cell>
                                        <Table.Cell>
                                            <Badge variant={getStatusVariant(submission.status)}>
                                                {submission.status.replace('_', ' ').toUpperCase()}
                                            </Badge>
                                        </Table.Cell>
                                        <Table.Cell>{new Date(submission.created_at).toLocaleDateString('id-ID')}</Table.Cell>
                                        <Table.Cell class="text-right">
                                            <DropdownMenu.Root>
                                                <DropdownMenu.Trigger
                                                    class="inline-flex h-8 w-8 items-center justify-center rounded-md border border-input bg-background text-sm font-medium hover:bg-accent hover:text-accent-foreground"
                                                >
                                                    <span class="sr-only">Open menu</span>
                                                    <MoreHorizontalIcon class="h-4 w-4" />
                                                </DropdownMenu.Trigger>
                                                <DropdownMenu.Content align="end">
                                                    <DropdownMenu.Item onclick={() => router.visit(route('apply.community_service.revisions', submission.id))}>
                                                        Riwayat Revisi
                                                    </DropdownMenu.Item>
                                                    {#if !hasSubmittedReport(submission)}
                                                        <DropdownMenu.Item
                                                            onclick={() =>
                                                                router.visit(
                                                                    route('apply.community_service.final_report.create', { submission_id: submission.id }),
                                                                )}
                                                        >
                                                            Buat Laporan
                                                        </DropdownMenu.Item>
                                                    {:else if submission.status === 'revision_needed'}
                                                        <DropdownMenu.Item
                                                            onclick={() => router.visit(route('apply.community_service.final_report.edit', submission.id))}
                                                        >
                                                            Revisi
                                                        </DropdownMenu.Item>
                                                    {/if}
                                                    {#if hasSubmittedReport(submission)}
                                                        <DropdownMenu.Item
                                                            onclick={() => router.visit(route('apply.community_service.final_report.show', submission.id))}
                                                        >
                                                            Detail
                                                        </DropdownMenu.Item>
                                                    {/if}
                                                </DropdownMenu.Content>
                                            </DropdownMenu.Root>
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
