<script lang="ts">
    import LayoutComposer from '@/layouts/LayoutComposer.svelte';
    import AppLayout from '@/layouts/AppLayout.svelte';
    import { type BreadcrumbItem } from '@/types';
    import Heading from '@/components/Heading.svelte';
    import { Button } from '@/components/ui/button';
    import * as Table from '@/components/ui/table';
    import * as Card from '@/components/ui/card';
    import { Badge } from '@/components/ui/badge';
    import * as DropdownMenu from '@/components/ui/dropdown-menu';
    import { router } from '@inertiajs/svelte';
    import { MoreHorizontalIcon, FileTextIcon, ActivityIcon, CheckCircleIcon } from 'lucide-svelte';
    import Pagination from '@/components/Pagination.svelte';

    let { submissions, counts } = $props(); // Paginator object

    const breadcrumbs: BreadcrumbItem[] = [
        { title: 'Permintaan Review', href: '#' },
        { title: 'Penelitian', href: '#' },
        { title: 'Dashboard', href: '#' },
    ];

    function getStatusVariant(status: string) {
        switch (status) {
            case 'approved':
                return 'default'; // or success if available
            case 'rejected':
                return 'destructive';
            case 'revision_needed':
                return 'destructive'; // orange/warning usually better but using standard variants
            case 'need_review':
                return 'secondary';
            default:
                return 'outline';
        }
    }
</script>

<svelte:head>
    <title>Dashboard Penelitian</title>
</svelte:head>

<AppLayout {breadcrumbs}>
    <LayoutComposer>
        {#snippet header()}
            <Heading title="Dashboard Penelitian" description="Daftar pengajuan proposal penelitian Anda." />
        {/snippet}

        {#snippet actions()}
            <Button href={route('apply.research.proposal.create')}>Buat Proposal Baru</Button>
        {/snippet}

        <div class="grid gap-4 md:grid-cols-3 mb-8">
            <Card.Root>
                <Card.Header class="flex flex-row items-center justify-between space-y-0 pb-2">
                    <Card.Title class="text-sm font-medium">Proposal</Card.Title>
                    <FileTextIcon class="h-4 w-4 text-muted-foreground" />
                </Card.Header>
                <Card.Content>
                    <div class="text-2xl font-bold">{counts.proposal}</div>
                    <p class="text-xs text-muted-foreground">Pengajuan Proposal</p>
                </Card.Content>
            </Card.Root>
            <Card.Root>
                <Card.Header class="flex flex-row items-center justify-between space-y-0 pb-2">
                    <Card.Title class="text-sm font-medium">Laporan Kemajuan</Card.Title>
                    <ActivityIcon class="h-4 w-4 text-muted-foreground" />
                </Card.Header>
                <Card.Content>
                    <div class="text-2xl font-bold">{counts.progress_report}</div>
                    <p class="text-xs text-muted-foreground">Laporan Berjalan</p>
                </Card.Content>
            </Card.Root>
            <Card.Root>
                <Card.Header class="flex flex-row items-center justify-between space-y-0 pb-2">
                    <Card.Title class="text-sm font-medium">Laporan Akhir</Card.Title>
                    <CheckCircleIcon class="h-4 w-4 text-muted-foreground" />
                </Card.Header>
                <Card.Content>
                    <div class="text-2xl font-bold">{counts.final_report}</div>
                    <p class="text-xs text-muted-foreground">Laporan Selesai</p>
                </Card.Content>
            </Card.Root>
        </div>
        <div class="space-y-4">
            <Card.Root>
                <Card.Header>
                    <Card.Title>Riwayat Pengajuan</Card.Title>
                    <Card.Description>Kelola proposal dan laporan penelitian Anda here.</Card.Description>
                </Card.Header>
                <Card.Content>
                    {#if submissions.data.length === 0}
                        <div class="text-center py-10 text-muted-foreground">Belum ada pengajuan. Silakan buat proposal baru.</div>
                    {:else}
                        <Table.Root>
                            <Table.Header>
                                <Table.Row>
                                    <Table.Head>Judul</Table.Head>
                                    <Table.Head>Tahap</Table.Head>
                                    <Table.Head>Status</Table.Head>
                                    <Table.Head>Tanggal</Table.Head>
                                    <Table.Head class="text-right">Aksi</Table.Head>
                                </Table.Row>
                            </Table.Header>
                            <Table.Body>
                                {#each submissions.data as submission (submission.id)}
                                    <Table.Row>
                                        <Table.Cell class="font-medium">
                                            {submission.latest_detail?.title || 'Judul Tidak Tersedia'}
                                        </Table.Cell>
                                        <Table.Cell class="uppercase">{submission.stage}</Table.Cell>
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
                                                    <DropdownMenu.Item onclick={() => router.visit(route('apply.research.revisions', submission.id))}>
                                                        Riwayat Revisi
                                                    </DropdownMenu.Item>
                                                    {#if submission.status === 'revision_needed'}
                                                        <DropdownMenu.Item
                                                            onclick={() => router.visit(route('apply.research.proposal.edit', submission.id))}
                                                        >
                                                            Revisi
                                                        </DropdownMenu.Item>
                                                    {/if}
                                                    <DropdownMenu.Item
                                                        onclick={() => router.visit(route('apply.research.proposal.show', submission.id))}
                                                    >
                                                        Detail
                                                    </DropdownMenu.Item>
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
