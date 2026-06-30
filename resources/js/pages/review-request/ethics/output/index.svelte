<script lang="ts">
    import LayoutComposer from '@/layouts/layout-composer.svelte';
    import AppLayout from '@/layouts/app-layout.svelte';
    import { type BreadcrumbItem } from '@/types';
    import Heading from '@/components/heading.svelte';
    import * as Card from '@/components/ui/card';
    import { Badge } from '@/components/ui/badge';
    import { Button } from '@/components/ui/button';
    import { router } from '@inertiajs/svelte';
    import { CheckCircleIcon } from 'lucide-svelte';
    import Pagination from '@/components/pagination.svelte';

    let { submissions } = $props();

    const breadcrumbs: BreadcrumbItem[] = [
        { title: 'Permintaan Review', href: '#' },
        { title: 'Etik', href: '#' },
        { title: 'Output', href: '#' },
    ];

    function getCategoryLabel(category: string) {
        return category === 'clinical' ? 'Etik Klinik / Uji Coba Hewan' : 'Etik Non Klinis';
    }
</script>

<svelte:head>
    <title>Ethical Clearance - Output</title>
</svelte:head>

<AppLayout {breadcrumbs}>
    <LayoutComposer>
        {#snippet header()}
            <Heading title="Ethical Clearance" description="Dokumen Ethical Clearance yang telah diterbitkan." />
        {/snippet}

        <div class="space-y-4">
            <Card.Root>
                <Card.Header>
                    <Card.Title>Dokumen Lembar Etik Sah</Card.Title>
                    <Card.Description>Daftar dokumen lembar etik yang sudah diterbitkan.</Card.Description>
                </Card.Header>
                <Card.Content class="pt-6">
                    {#if submissions.data.length === 0}
                        <div class="text-center py-10 text-muted-foreground">Belum ada dokumen EC yang diterbitkan.</div>
                    {:else}
                        <div class="space-y-3">
                            {#each submissions.data as submission (submission.id)}
                                <div class="flex items-center justify-between border rounded-lg p-4 hover:bg-muted/50 transition-colors">
                                    <div class="flex items-center gap-3">
                                        <CheckCircleIcon class="h-5 w-5 text-green-500" />
                                        <div>
                                            <p class="font-medium">{getCategoryLabel(submission.category)}</p>
                                            <p class="text-sm text-muted-foreground">{new Date(submission.created_at).toLocaleDateString('id-ID')}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <Badge>EC Diterbitkan</Badge>
                                        <Button
                                            variant="outline"
                                            size="sm"
                                            onclick={() => router.visit(route('apply.ethics.output.show', submission.id))}
                                        >
                                            Lihat & Unduh
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
