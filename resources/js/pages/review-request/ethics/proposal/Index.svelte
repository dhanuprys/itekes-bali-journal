<script lang="ts">
    import LayoutComposer from '@/layouts/LayoutComposer.svelte';
    import AppLayout from '@/layouts/AppLayout.svelte';
    import { type BreadcrumbItem } from '@/types';
    import Heading from '@/components/Heading.svelte';
    import { Button } from '@/components/ui/button';
    import * as Table from '@/components/ui/table';
    import * as Card from '@/components/ui/card';
    import { Badge } from '@/components/ui/badge';
    import * as Alert from '@/components/ui/alert';
    import * as DropdownMenu from '@/components/ui/dropdown-menu';
    import { router } from '@inertiajs/svelte';
    import { InfoIcon, MoreHorizontalIcon } from 'lucide-svelte';
    import Pagination from '@/components/Pagination.svelte';

    let { submissions, hasOngoing = false } = $props();

    const breadcrumbs: BreadcrumbItem[] = [
        { title: 'Permintaan Review', href: '#' },
        { title: 'Etik', href: '#' },
        { title: 'Proposal', href: '#' },
    ];

    function getStatusVariant(status: string) {
        switch (status) {
            case 'approved': return 'default';
            case 'rejected': return 'destructive';
            case 'revision_needed': return 'destructive';
            case 'need_review': return 'secondary';
            default: return 'outline';
        }
    }

    function getCategoryLabel(category: string) {
        return category === 'clinical' ? 'Etik Klinik / Uji Coba Hewan' : 'Etik Non Klinis';
    }
</script>

<svelte:head>
    <title>Daftar Pengajuan Etik</title>
</svelte:head>

<AppLayout {breadcrumbs}>
    <LayoutComposer>
        {#snippet header()}
            <Heading title="Daftar Pengajuan Etik" description="Kelola pengajuan ethical clearance Anda." />
        {/snippet}

        {#snippet actions()}
            {#if !hasOngoing}
                <Button href={route('apply.ethics.proposal.create')}>Buat Pengajuan Baru</Button>
            {/if}
        {/snippet}

        <div class="space-y-4">
            {#if hasOngoing}
                <Alert.Root class="bg-blue-50 border-blue-200 text-blue-800 dark:bg-blue-950 dark:border-blue-900 dark:text-blue-200">
                    <InfoIcon class="h-4 w-4 text-blue-800 dark:text-blue-200" />
                    <Alert.Title>Perhatian</Alert.Title>
                    <Alert.Description>
                        Anda masih memiliki pengajuan etik yang sedang berjalan. Anda baru dapat mengajukan pengajuan baru setelah pengajuan saat ini selesai atau ditolak.
                    </Alert.Description>
                </Alert.Root>
            {/if}

            <Card.Root>
                <Card.Header>
                    <Card.Title>Riwayat Pengajuan</Card.Title>
                    <Card.Description>Daftar semua pengajuan ethical clearance yang telah Anda ajukan.</Card.Description>
                </Card.Header>
                <Card.Content>
                    {#if submissions.data.length === 0}
                        <div class="text-center py-10 text-muted-foreground">Belum ada pengajuan. Silakan buat pengajuan baru.</div>
                    {:else}
                        <Table.Root>
                            <Table.Header>
                                <Table.Row>
                                    <Table.Head>Kategori</Table.Head>
                                    <Table.Head>Status</Table.Head>
                                    <Table.Head>Tanggal</Table.Head>
                                    <Table.Head class="text-right">Aksi</Table.Head>
                                </Table.Row>
                            </Table.Header>
                            <Table.Body>
                                {#each submissions.data as submission (submission.id)}
                                    <Table.Row>
                                        <Table.Cell class="font-medium">
                                            {getCategoryLabel(submission.category)}
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
                                                    {#if submission.status === 'revision_needed'}
                                                        <DropdownMenu.Item
                                                            onclick={() => router.visit(route('apply.ethics.proposal.edit', submission.id))}
                                                        >
                                                            Revisi
                                                        </DropdownMenu.Item>
                                                    {/if}
                                                    <DropdownMenu.Item
                                                        onclick={() => router.visit(route('apply.ethics.proposal.show', submission.id))}
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
