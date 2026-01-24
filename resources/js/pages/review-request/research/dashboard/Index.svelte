<script lang="ts">
    import LayoutComposer from '@/layouts/LayoutComposer.svelte';
    import AppLayout from '@/layouts/AppLayout.svelte';
    import { type BreadcrumbItem } from '@/types';
    import Heading from '@/components/Heading.svelte';
    import { Button } from '@/components/ui/button';
    import * as Table from '@/components/ui/table';
    import * as Card from '@/components/ui/card';
    import { Badge } from '@/components/ui/badge';
    import { Link } from '@inertiajs/svelte';

    let { submissions } = $props(); // Paginator object

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

        {#snippet children()}
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
                                            {#if submission.status === 'revision_needed'}
                                                <Button variant="outline" size="sm" href={route('apply.research.proposal.edit', submission.id)}>
                                                    Revisi
                                                </Button>
                                            {:else}
                                                <Button variant="ghost" size="sm" href={route('apply.research.proposal.show', submission.id)}>
                                                    Detail
                                                </Button>
                                            {/if}
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
