<script lang="ts">
    import LayoutComposer from '@/layouts/layout-composer.svelte';
    import AppLayout from '@/layouts/app-layout.svelte';
    import { type BreadcrumbItem } from '@/types';
    import Heading from '@/components/heading.svelte';
    import * as Card from '@/components/ui/card';
    import { Badge } from '@/components/ui/badge';
    import { FileTextIcon, ClockIcon } from 'lucide-svelte';

    let { submission } = $props();
    let detail = $derived(submission.latest_detail);
    let files = $derived(detail?.files ?? []);

    const breadcrumbs: BreadcrumbItem[] = [
        { title: 'Permintaan Review', href: '#' },
        { title: 'Etik', href: '#' },
        { title: 'Menunggu Output', href: route('apply.ethics.wait_for_output.index') },
        { title: 'Detail', href: '#' },
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
            <Heading title="Menunggu Ethical Clearance" description="Pengajuan Anda telah disetujui. Dokumen EC sedang diproses." />
        {/snippet}

        {#snippet actions()}
            {#if submission.stage === 'verification'}
                <Badge
                    variant="secondary"
                    class="px-3 py-1 gap-2 flex items-center bg-orange-100 text-orange-700 hover:bg-orange-100 border-orange-200"
                >
                    <ClockIcon class="h-4 w-4" />
                    Sedang Diverifikasi
                </Badge>
            {:else}
                <Badge variant="secondary" class="px-3 py-1 gap-2 flex items-center">
                    <ClockIcon class="h-4 w-4" />
                    Menunggu Penerbitan EC
                </Badge>
            {/if}
        {/snippet}

        <div class="space-y-6">
            <Card.Root>
                <Card.Header>
                    <Card.Title>Informasi Pengajuan</Card.Title>
                </Card.Header>
                <Card.Content>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-muted-foreground">Kategori</p>
                            <p class="font-medium">{getCategoryLabel(submission.category)}</p>
                        </div>
                        <div>
                            <p class="text-sm text-muted-foreground">Tanggal Pengajuan</p>
                            <p class="font-medium">
                                {new Date(submission.created_at).toLocaleDateString('id-ID', { year: 'numeric', month: 'long', day: 'numeric' })}
                            </p>
                        </div>
                    </div>
                </Card.Content>
            </Card.Root>

            <Card.Root>
                <Card.Header>
                    <Card.Title>Dokumen yang Diunggah</Card.Title>
                </Card.Header>
                <Card.Content>
                    <div class="space-y-2">
                        {#each files as file (file.id)}
                            <div class="flex items-center gap-3 border rounded-lg p-3">
                                <FileTextIcon class="h-5 w-5 text-muted-foreground shrink-0" />
                                <div class="min-w-0">
                                    <p class="font-medium text-sm truncate">{file.original_name || file.template_key}</p>
                                    <p class="text-xs text-muted-foreground">{file.template_key.replace(/_/g, ' ')}</p>
                                </div>
                            </div>
                        {/each}
                    </div>
                </Card.Content>
            </Card.Root>
        </div>
    </LayoutComposer>
</AppLayout>
