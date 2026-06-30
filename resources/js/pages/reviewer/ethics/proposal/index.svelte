<script lang="ts">
    import LayoutComposer from '@/layouts/layout-composer.svelte';
    import AppLayout from '@/layouts/app-layout.svelte';
    import { type BreadcrumbItem } from '@/types';
    import Heading from '@/components/heading.svelte';
    import * as Table from '@/components/ui/table';
    import * as Card from '@/components/ui/card';
    import { Badge } from '@/components/ui/badge';
    import { Button } from '@/components/ui/button';
    import { router } from '@inertiajs/svelte';
    import Pagination from '@/components/pagination.svelte';

    let { submissions } = $props();

    const breadcrumbs: BreadcrumbItem[] = [
        { title: 'Reviewer Area', href: '#' },
        { title: 'Etik', href: route('review.ethics.index') },
        { title: 'Proposal', href: '#' },
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

    function getCategoryLabel(category: string) {
        return category === 'clinical' ? 'Etik Klinik / Uji Coba Hewan' : 'Etik Non Klinis';
    }
</script>

<svelte:head>
    <title>Review Proposal Etik</title>
</svelte:head>

<AppLayout {breadcrumbs}>
    <LayoutComposer>
        {#snippet header()}
            <Heading title="Proposal Etik" description="Daftar pengajuan ethical clearance yang perlu ditinjau." />
        {/snippet}

        <div class="space-y-4">
            <Card.Root>
                <Card.Content class="pt-6">
                    {#if submissions.data.length === 0}
                        <div class="text-center py-10 text-muted-foreground">Tidak ada proposal yang perlu ditinjau.</div>
                    {:else}
                        <Table.Root>
                            <Table.Header>
                                <Table.Row>
                                    <Table.Head>Pengaju</Table.Head>
                                    <Table.Head>Kategori</Table.Head>
                                    <Table.Head>Status</Table.Head>
                                    <Table.Head>Tanggal</Table.Head>
                                    <Table.Head class="text-right">Aksi</Table.Head>
                                </Table.Row>
                            </Table.Header>
                            <Table.Body>
                                {#each submissions.data as submission (submission.id)}
                                    <Table.Row>
                                        <Table.Cell class="font-medium">{submission.user?.name ?? '-'}</Table.Cell>
                                        <Table.Cell>{getCategoryLabel(submission.category)}</Table.Cell>
                                        <Table.Cell>
                                            <Badge variant={getStatusVariant(submission.status)}>
                                                {submission.status.replace('_', ' ').toUpperCase()}
                                            </Badge>
                                        </Table.Cell>
                                        <Table.Cell>{new Date(submission.created_at).toLocaleDateString('id-ID')}</Table.Cell>
                                        <Table.Cell class="text-right">
                                            <Button
                                                variant="outline"
                                                size="sm"
                                                onclick={() => router.visit(route('review.ethics.proposal.show', submission.id))}
                                            >
                                                Tinjau
                                            </Button>
                                        </Table.Cell>
                                    </Table.Row>
                                {/each}
                            </Table.Body>
                        </Table.Root>
                    {/if}
                </Card.Content>
            </Card.Root>
            <Pagination meta={submissions} />
        </div>
    </LayoutComposer>
</AppLayout>
