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
    import { MoreHorizontalIcon } from 'lucide-svelte';

    let { submissions } = $props();

    const breadcrumbs: BreadcrumbItem[] = [
        { title: 'Permintaan Review', href: '#' },
        { title: 'Pengabdian Masyarakat', href: '#' },
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
</script>

<svelte:head>
    <title>Dashboard Pengabdian</title>
</svelte:head>

<AppLayout {breadcrumbs}>
    <LayoutComposer>
        {#snippet header()}
            <Heading title="Dashboard Pengabdian" description="Daftar pengajuan proposal pengabdian Anda." />
        {/snippet}

        {#snippet actions()}
            <Button href={route('apply.community_service.proposal.create')}>Buat Proposal Baru</Button>
        {/snippet}

        {#snippet children()}
            <Card.Root>
                <Card.Header>
                    <Card.Title>Riwayat Pengajuan</Card.Title>
                    <Card.Description>Kelola proposal dan laporan pengabdian Anda here.</Card.Description>
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
                                {#each submissions.data as submission}
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
                                                    <DropdownMenu.Item
                                                        onclick={() => router.visit(route('apply.community_service.revisions', submission.id))}
                                                    >
                                                        Riwayat Revisi
                                                    </DropdownMenu.Item>
                                                    {#if submission.status === 'revision_needed'}
                                                        <DropdownMenu.Item
                                                            onclick={() =>
                                                                router.visit(route('apply.community_service.proposal.edit', submission.id))}
                                                        >
                                                            Revisi
                                                        </DropdownMenu.Item>
                                                    {/if}
                                                    <DropdownMenu.Item
                                                        onclick={() => router.visit(route('apply.community_service.proposal.show', submission.id))}
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
        {/snippet}
    </LayoutComposer>
</AppLayout>
