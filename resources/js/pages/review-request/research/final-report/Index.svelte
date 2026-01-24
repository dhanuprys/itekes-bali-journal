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

    let { submissions } = $props();

    const breadcrumbs: BreadcrumbItem[] = [
        { title: 'Permintaan Review', href: '#' },
        { title: 'Penelitian', href: '#' },
        { title: 'Laporan Akhir', href: '#' },
    ];

    function getStatusVariant(status: string) {
        switch (status) {
            case 'approved':
                return 'default';
            default:
                return 'outline';
        }
    }
</script>

<svelte:head>
    <title>Laporan Akhir Penelitian</title>
</svelte:head>

<AppLayout {breadcrumbs}>
    <LayoutComposer>
        {#snippet header()}
            <Heading title="Laporan Akhir" description="Daftar laporan akhir penelitian yang telah disetujui." />
        {/snippet}

        {#snippet children()}
            <Card.Root>
                <Card.Header>
                    <Card.Title>Daftar Laporan</Card.Title>
                    <Card.Description>Semua penelitian yang telah selesai dan disetujui laporannya.</Card.Description>
                </Card.Header>
                <Card.Content>
                    {#if submissions.data.length === 0}
                        <div class="text-center py-10 text-muted-foreground">Belum ada laporan akhir yang disetujui.</div>
                    {:else}
                        <Table.Root>
                            <Table.Header>
                                <Table.Row>
                                    <Table.Head>Judul</Table.Head>
                                    <Table.Head>Status</Table.Head>
                                    <Table.Head>Tanggal Selesai</Table.Head>
                                    <Table.Head class="text-right">Aksi</Table.Head>
                                </Table.Row>
                            </Table.Header>
                            <Table.Body>
                                {#each submissions.data as submission}
                                    <Table.Row>
                                        <Table.Cell class="font-medium">
                                            {submission.latest_detail?.final_title || submission.latest_detail?.title || 'Judul Tidak Tersedia'}
                                        </Table.Cell>
                                        <Table.Cell>
                                            <Badge variant={getStatusVariant(submission.status)}>
                                                {submission.status.replace('_', ' ').toUpperCase()}
                                            </Badge>
                                        </Table.Cell>
                                        <Table.Cell>{new Date(submission.updated_at).toLocaleDateString('id-ID')}</Table.Cell>
                                        <Table.Cell class="text-right">
                                            <Button variant="ghost" size="sm" href={route('apply.research.final_report.show', submission.id)}>
                                                Detail
                                            </Button>
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
