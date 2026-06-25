<script lang="ts">
    import LayoutComposer from '@/layouts/LayoutComposer.svelte';
    import AppLayout from '@/layouts/AppLayout.svelte';
    import { type BreadcrumbItem } from '@/types';
    import Heading from '@/components/Heading.svelte';
    import * as Card from '@/components/ui/card';
    import { Badge } from '@/components/ui/badge';
    import { Button } from '@/components/ui/button';
    import { router } from '@inertiajs/svelte';
    import { ClockIcon } from 'lucide-svelte';
    import Pagination from '@/components/Pagination.svelte';

    let { submissions } = $props();

    const breadcrumbs: BreadcrumbItem[] = [
        { title: 'Permintaan Review', href: '#' },
        { title: 'Etik', href: '#' },
        { title: 'Menunggu Output', href: '#' },
    ];

    function getCategoryLabel(category: string) {
        return category === 'clinical' ? 'Etik Klinik / Uji Coba Hewan' : 'Etik Non Klinis';
    }
</script>

<svelte:head>
    <title>Menunggu Ethical Clearance</title>
</svelte:head>

<AppLayout {breadcrumbs}>
    <LayoutComposer>
        {#snippet header()}
            <Heading title="Menunggu Ethical Clearance" description="Pengajuan yang telah disetujui dan menunggu penerbitan dokumen EC." />
        {/snippet}

        <div class="space-y-4">
            <Card.Root>
                <Card.Header>
                    <Card.Title>Menunggu Dokumen EC</Card.Title>
                    <Card.Description>Daftar pengajuan ethical clearance yang sedang diproses dan menunggu penerbitan dokumen.</Card.Description>
                </Card.Header>
                <Card.Content>
                    {#if submissions.data.length === 0}
                        <div class="text-center py-10 text-muted-foreground">Tidak ada pengajuan yang menunggu penerbitan EC.</div>
                    {:else}
                        <div class="space-y-3">
                            {#each submissions.data as submission (submission.id)}
                                <div class="flex items-center justify-between border rounded-lg p-4 hover:bg-muted/50 transition-colors">
                                    <div class="flex items-center gap-3">
                                        <ClockIcon class="h-5 w-5 text-amber-500" />
                                        <div>
                                            <p class="font-medium">{getCategoryLabel(submission.category)}</p>
                                            <p class="text-sm text-muted-foreground">{new Date(submission.created_at).toLocaleDateString('id-ID')}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        {#if submission.stage === 'output' && submission.status === 'need_review'}
                                            <Badge variant="secondary" class="bg-red-100 text-red-700 hover:bg-red-100 border-red-200"
                                                >Revisi Dokumen</Badge
                                            >
                                        {:else if submission.stage === 'verification'}
                                            <Badge variant="secondary" class="bg-orange-100 text-orange-700 hover:bg-orange-100 border-orange-200"
                                                >Sedang Diverifikasi</Badge
                                            >
                                        {:else}
                                            <Badge variant="secondary">Menunggu EC</Badge>
                                        {/if}
                                        <Button
                                            variant="outline"
                                            size="sm"
                                            onclick={() => router.visit(route('apply.ethics.wait_for_output.show', submission.id))}
                                        >
                                            Detail
                                        </Button>
                                    </div>
                                </div>
                            {/each}
                        </div>
                    {/if}
                </Card.Content>
            </Card.Root>
            <Pagination meta={submissions} />
        </div>
    </LayoutComposer>
</AppLayout>
