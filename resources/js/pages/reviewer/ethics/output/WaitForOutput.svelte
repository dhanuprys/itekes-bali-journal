<script lang="ts">
    import LayoutComposer from '@/layouts/LayoutComposer.svelte';
    import AppLayout from '@/layouts/AppLayout.svelte';
    import { type BreadcrumbItem } from '@/types';
    import Heading from '@/components/Heading.svelte';
    import * as Table from '@/components/ui/table';
    import * as Card from '@/components/ui/card';
    import { Badge } from '@/components/ui/badge';
    import { Button } from '@/components/ui/button';
    import { router } from '@inertiajs/svelte';
    import Pagination from '@/components/Pagination.svelte';

    let { submissions } = $props();

    const breadcrumbs: BreadcrumbItem[] = [
        { title: 'Reviewer Area', href: '#' },
        { title: 'Etik', href: route('review.ethics.index') },
        { title: 'Dokumen Diproses', href: '#' },
    ];

    function getCategoryLabel(category: string) {
        return category === 'clinical' ? 'Etik Klinik / Uji Coba Hewan' : 'Etik Non Klinis';
    }
</script>

<svelte:head>
    <title>Dokumen Diproses Etik - Reviewer</title>
</svelte:head>

<AppLayout {breadcrumbs}>
    <LayoutComposer>
        {#snippet header()}
            <Heading title="Dokumen Diproses" description="Pengajuan etik yang menunggu penerbitan dokumen Ethical Clearance." />
        {/snippet}

        <div class="space-y-4">
            <Card.Root>
                <Card.Content class="pt-6">
                    {#if submissions.data.length === 0}
                        <div class="text-center py-10 text-muted-foreground">Tidak ada pengajuan yang menunggu penerbitan EC.</div>
                    {:else}
                        <Table.Root>
                            <Table.Header>
                                <Table.Row>
                                    <Table.Head>Pengaju</Table.Head>
                                    <Table.Head>Kategori</Table.Head>
                                    <Table.Head>No. Dokumen</Table.Head>
                                    <Table.Head>Status EC</Table.Head>
                                    <Table.Head>Tanggal</Table.Head>
                                    <Table.Head class="text-right">Aksi</Table.Head>
                                </Table.Row>
                            </Table.Header>
                            <Table.Body>
                                {#each submissions.data as submission (submission.id)}
                                    <Table.Row>
                                        <Table.Cell class="font-medium">{submission.user?.name ?? '-'}</Table.Cell>
                                        <Table.Cell>{getCategoryLabel(submission.category)}</Table.Cell>
                                        <Table.Cell class="font-mono text-sm">{submission.formatted_document_number ?? '-'}</Table.Cell>
                                        <Table.Cell>
                                            {#if submission.latest_output?.document_path}
                                                <Badge>EC Diterbitkan</Badge>
                                            {:else}
                                                <Badge variant="secondary">Belum Diterbitkan</Badge>
                                            {/if}
                                        </Table.Cell>
                                        <Table.Cell>{new Date(submission.created_at).toLocaleDateString('id-ID')}</Table.Cell>
                                        <Table.Cell class="text-right">
                                            <Button variant="outline" size="sm" onclick={() => router.visit(route('review.ethics.wait_for_output.show', submission.id))}>
                                                Upload EC
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
