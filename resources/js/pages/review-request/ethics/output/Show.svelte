<script lang="ts">
    import LayoutComposer from '@/layouts/LayoutComposer.svelte';
    import AppLayout from '@/layouts/AppLayout.svelte';
    import { type BreadcrumbItem } from '@/types';
    import Heading from '@/components/Heading.svelte';
    import * as Card from '@/components/ui/card';
    import { Badge } from '@/components/ui/badge';
    import { DownloadIcon, FileTextIcon, CheckCircleIcon } from 'lucide-svelte';

    let { submission } = $props();
    let detail = $derived(submission.latest_detail);
    let files = $derived(detail?.files ?? []);
    let output = $derived(submission.latest_output);

    const breadcrumbs: BreadcrumbItem[] = [
        { title: 'Permintaan Review', href: '#' },
        { title: 'Etik', href: '#' },
        { title: 'Output', href: route('apply.ethics.output.index') },
        { title: 'Detail', href: '#' },
    ];

    function getCategoryLabel(category: string) {
        return category === 'clinical' ? 'Etik Klinik / Uji Coba Hewan' : 'Etik Non Klinis';
    }
</script>

<svelte:head>
    <title>Ethical Clearance - Detail</title>
</svelte:head>

<AppLayout {breadcrumbs}>
    <LayoutComposer>
        {#snippet header()}
            <Heading title="Dokumen Ethical Clearance" description="Unduh dokumen Ethical Clearance yang telah diterbitkan." />
        {/snippet}

        {#snippet actions()}
            <Badge class="px-3 py-1 gap-2 flex items-center bg-green-500/10 text-green-600 border-green-200">
                <CheckCircleIcon class="h-4 w-4" />
                EC Diterbitkan
            </Badge>
        {/snippet}

        <div class="space-y-6">
            <!-- EC Document -->
            {#if output?.document_path}
                <Card.Root class="border-green-500 bg-green-50/50 dark:bg-green-950/20 dark:border-green-900">
                    <Card.Header>
                        <Card.Title class="text-green-800 dark:text-green-200">Dokumen Ethical Clearance</Card.Title>
                        <Card.Description>Dokumen EC resmi yang telah diterbitkan oleh reviewer.</Card.Description>
                    </Card.Header>
                    <Card.Content>
                        <div
                            class="flex items-center justify-between border border-green-200 dark:border-green-800 rounded-lg p-4 bg-white dark:bg-green-950/30"
                        >
                            <div class="flex items-center gap-3">
                                <FileTextIcon class="h-6 w-6 text-green-600" />
                                <div>
                                    <p class="font-semibold">Ethical Clearance</p>
                                    {#if output.notes}
                                        <p class="text-sm text-muted-foreground">{output.notes}</p>
                                    {/if}
                                    <p class="text-xs text-muted-foreground mt-0.5">
                                        Diterbitkan: {new Date(output.created_at).toLocaleDateString('id-ID', {
                                            year: 'numeric',
                                            month: 'long',
                                            day: 'numeric',
                                        })}
                                    </p>
                                </div>
                            </div>
                            <a
                                href={'/storage/' + output.document_path}
                                target="_blank"
                                class="inline-flex items-center gap-2 rounded-md bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-700 transition-colors"
                            >
                                <DownloadIcon class="h-4 w-4" />
                                Unduh EC
                            </a>
                        </div>
                    </Card.Content>
                </Card.Root>
            {/if}

            <!-- Submission Info -->
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

            <!-- Uploaded Documents -->
            <Card.Root>
                <Card.Header>
                    <Card.Title>Dokumen Pengajuan</Card.Title>
                </Card.Header>
                <Card.Content>
                    <div class="space-y-2">
                        {#each files as file (file.id)}
                            <div class="flex items-center justify-between border rounded-lg p-3">
                                <div class="flex items-center gap-3 min-w-0">
                                    <FileTextIcon class="h-5 w-5 text-muted-foreground shrink-0" />
                                    <div class="min-w-0">
                                        <p class="font-medium text-sm truncate">{file.original_name || file.template_key}</p>
                                        <p class="text-xs text-muted-foreground">{file.template_key.replace(/_/g, ' ')}</p>
                                    </div>
                                </div>
                                <a
                                    href={'/storage/' + file.file_path}
                                    target="_blank"
                                    class="inline-flex items-center gap-1.5 rounded-md bg-primary/10 px-3 py-1.5 text-xs font-medium text-primary hover:bg-primary/20 transition-colors shrink-0"
                                >
                                    <DownloadIcon class="h-3.5 w-3.5" />
                                    Unduh
                                </a>
                            </div>
                        {/each}
                    </div>
                </Card.Content>
            </Card.Root>
        </div>
    </LayoutComposer>
</AppLayout>
