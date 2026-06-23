<script lang="ts">
    import LayoutComposer from '@/layouts/LayoutComposer.svelte';
    import AppLayout from '@/layouts/AppLayout.svelte';
    import { type BreadcrumbItem } from '@/types';
    import Heading from '@/components/Heading.svelte';
    import * as Card from '@/components/ui/card';
    import { Badge } from '@/components/ui/badge';
    import { Button } from '@/components/ui/button';
    import { useForm } from '@inertiajs/svelte';
    import FileUpload from '@/components/FileUpload.svelte';
    import { StorageUploadAction } from '@/data/storage-upload';
    import { toast } from 'svelte-sonner';
    import { uploadState } from '@/stores/upload-state.svelte';
    import { FileTextIcon, DownloadIcon, CheckCircleIcon, AlertCircleIcon, XCircleIcon } from 'lucide-svelte';
    import { Textarea } from '@/components/ui/textarea';
    import Label from '@/components/ui/label/label.svelte';

    let { submission, comments } = $props();
    let detail = $derived(submission?.latest_detail);
    let files = $derived(detail?.files ?? []);
    let output = $derived(submission?.latest_output);
    let hasDocument = $derived(!!output?.document_path);

    const breadcrumbs: BreadcrumbItem[] = [
        { title: 'Reviewer Area', href: '#' },
        { title: 'Etik', href: route('review.ethics.index') },
        { title: 'Output', href: route('review.ethics.wait_for_output.index') },
        { title: 'Upload EC', href: '#' },
    ];

    function getCategoryLabel(category: string) {
        return category === 'clinical' ? 'Etik Klinik / Uji Coba Hewan' : 'Etik Non Klinis';
    }

    const form = useForm({
        document_path: '',
        notes: '',
    });

    function submitDocument() {
        if (uploadState.isUploading) return;
        if (!$form.document_path) {
            toast.error('Silakan unggah dokumen Ethical Clearance.');
            return;
        }

        $form.post(route('review.ethics.wait_for_output.update_document', submission.id), {
            onSuccess: () => {
                toast.success('Dokumen EC berhasil diunggah.');
            },
            onError: () => {
                toast.error('Gagal mengunggah dokumen EC.');
            },
        });
    }
</script>

<svelte:head>
    <title>Upload Ethical Clearance</title>
</svelte:head>

<AppLayout {breadcrumbs}>
    <LayoutComposer>
        {#snippet header()}
            <Heading title="Upload Ethical Clearance" description="Unggah dokumen EC untuk pengajuan ini." />
        {/snippet}

        {#snippet actions()}
            {#if hasDocument}
                <Badge class="px-3 py-1 gap-2 flex items-center bg-green-500/10 text-green-600 border-green-200">
                    <CheckCircleIcon class="h-4 w-4" />
                    EC Diterbitkan
                </Badge>
            {:else}
                <Badge variant="secondary" class="px-3 py-1">Menunggu Upload EC</Badge>
            {/if}
        {/snippet}

        <div class="space-y-6">
            {#if submission.stage === 'output' && submission.status === 'need_review'}
                <div class="rounded-lg bg-red-50 p-4 border border-red-200 dark:bg-red-950/20 dark:border-red-900">
                    <div class="flex items-start gap-3">
                        <AlertCircleIcon class="h-5 w-5 text-red-600 mt-0.5" />
                        <div>
                            <h3 class="font-medium text-red-800 dark:text-red-200">Dokumen Memerlukan Revisi</h3>
                            <p class="text-sm text-red-700 dark:text-red-300 mt-1">Dokumen Ethical Clearance yang diunggah sebelumnya telah ditolak oleh reviewer. Silakan periksa catatan dari reviewer dan unggah kembali dokumen yang baru.</p>
                            
                            {#if output?.verifications?.length > 0}
                                <div class="mt-3 space-y-2">
                                    {#each output.verifications as verification}
                                        {#if verification.status === 'rejected'}
                                            <div class="text-sm bg-red-100 dark:bg-red-900/50 p-3 rounded-md border border-red-200 dark:border-red-800">
                                                <span class="font-medium text-red-900 dark:text-red-100 block mb-1">Catatan Penolakan dari {verification.user?.name || 'Reviewer'}:</span>
                                                <span class="text-red-800 dark:text-red-200 italic">"{verification.notes || 'Tanpa catatan'}"</span>
                                            </div>
                                        {/if}
                                    {/each}
                                </div>
                            {/if}
                        </div>
                    </div>
                </div>
            {/if}

            <!-- Submission Info -->
            <Card.Root>
                <Card.Header>
                    <Card.Title>Informasi Pengajuan</Card.Title>
                </Card.Header>
                <Card.Content>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-muted-foreground">Pengaju</p>
                            <p class="font-medium">{submission.user?.name ?? '-'}</p>
                        </div>
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
                    <div class="mt-4 p-4 rounded-lg bg-muted/50 border border-muted-foreground/20">
                        <p class="text-sm text-muted-foreground mb-1">Nomor Dokumen EC (Sistem)</p>
                        <p class="font-mono text-xl font-bold tracking-tight">
                            {submission.formatted_document_number ?? 'Belum ditetapkan'}
                        </p>
                        <p class="text-xs text-muted-foreground mt-2">
                            Gunakan nomor di atas pada dokumen fisik/Word Ethical Clearance sebelum mengunggahnya.
                        </p>
                    </div>
                </Card.Content>
            </Card.Root>

            <!-- Uploaded Proposal Documents -->
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
                                        <p class="text-xs text-muted-foreground capitalize">{file.template_key.replace(/_/g, ' ')}</p>
                                    </div>
                                </div>
                                <a
                                    href={'/storage/' + file.file_path}
                                    target="_blank"
                                    download={file.original_name || file.template_key}
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

            <!-- Existing EC Document -->
            {#if hasDocument}
                <Card.Root class="border-green-200 bg-green-50/50 dark:bg-green-950/20 dark:border-green-900">
                    <Card.Header>
                        <Card.Title class="text-green-800 dark:text-green-200">
                            {#if submission.stage === 'output' && submission.status === 'need_review'}
                                Dokumen EC Sebelumnya (Ditolak)
                            {:else if submission.stage === 'verification'}
                                Dokumen EC yang Sedang Diverifikasi
                            {:else}
                                Dokumen EC yang Diterbitkan
                            {/if}
                        </Card.Title>
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
                                </div>
                            </div>
                            <a
                                href={'/storage/' + output.document_path}
                                target="_blank"
                                download={output.original_name || 'Dokumen_Output.pdf'}
                                class="inline-flex items-center gap-2 rounded-md bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-700 transition-colors"
                            >
                                <DownloadIcon class="h-4 w-4" />
                                Unduh
                            </a>
                        </div>
                    </Card.Content>
                </Card.Root>
            {/if}

            {#if !hasDocument || (submission.stage === 'output' && submission.status === 'need_review')}
                <!-- Upload EC Form -->
                <Card.Root>
                    <Card.Header>
                        <Card.Title>{hasDocument ? 'Upload Ulang Dokumen (Revisi)' : 'Upload Dokumen Ethical Clearance'}</Card.Title>
                        <Card.Description>
                            {hasDocument 
                                ? 'Unggah file dokumen Ethical Clearance yang telah direvisi.' 
                                : 'Unggah file dokumen Ethical Clearance resmi untuk diverifikasi oleh reviewer sebelum diterbitkan kepada pengaju.'}
                        </Card.Description>
                    </Card.Header>
                    <Card.Content>
                        <form
                            onsubmit={(e) => {
                                e.preventDefault();
                                submitDocument();
                            }}
                            class="space-y-4"
                        >
                            <div>
                                <Label for="ec_document">Dokumen EC *</Label>
                                <FileUpload
                                    action={StorageUploadAction.ETHICS_OUTPUT}
                                    bind:value={$form.document_path}
                                    accept=".doc,.docx"
                                    label="Pilih dokumen EC"
                                    contextHint="Dokumen Output EC"
                                />
                                {#if $form.errors?.document_path}
                                    <p class="text-sm text-destructive mt-1">{$form.errors.document_path}</p>
                                {/if}
                            </div>

                            <div>
                                <Label for="notes">Catatan (opsional)</Label>
                                <Textarea
                                    id="notes"
                                    bind:value={$form.notes}
                                    class="mt-2"
                                    placeholder="Catatan tambahan untuk reviewer..."
                                    rows={3}
                                />
                            </div>

                            <div class="flex justify-end items-center gap-4">
                                {#if uploadState.isUploading}
                                    <span class="text-sm text-muted-foreground animate-pulse">Mengunggah file...</span>
                                {/if}
                                <Button type="submit" disabled={$form.processing || uploadState.isUploading || !$form.document_path}>
                                    {#if $form.processing}
                                        Menyimpan...
                                    {:else}
                                        Kirim ke Reviewer
                                    {/if}
                                </Button>
                            </div>
                        </form>
                    </Card.Content>
                </Card.Root>
            {/if}
        </div>
    </LayoutComposer>
</AppLayout>
