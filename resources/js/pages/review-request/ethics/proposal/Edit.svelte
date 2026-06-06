<script lang="ts">
    import LayoutComposer from '@/layouts/LayoutComposer.svelte';
    import AppLayout from '@/layouts/AppLayout.svelte';
    import { type BreadcrumbItem } from '@/types';
    import Heading from '@/components/Heading.svelte';
    import { useForm } from '@inertiajs/svelte';
    import { Button } from '@/components/ui/button';
    import * as Card from '@/components/ui/card';
    import Label from '@/components/ui/label/label.svelte';
    import FileUpload from '@/components/FileUpload.svelte';
    import { StorageUploadAction } from '@/data/storage-upload';
    import { toast } from 'svelte-sonner';
    import { uploadState } from '@/stores/upload-state.svelte';
    import { CheckCircle2Icon, XCircleIcon, FileTextIcon } from 'lucide-svelte';

    let { submission } = $props();
    let detail = $derived(submission.latest_detail);
    let existingFiles = $derived(detail?.files ?? []);

    const breadcrumbs: BreadcrumbItem[] = [
        { title: 'Permintaan Review', href: '#' },
        { title: 'Etik', href: '#' },
        { title: 'Proposal', href: route('apply.ethics.proposal.index') },
        { title: 'Revisi', href: '#' },
    ];

    // Re-populate uploaded files from existing data
    let uploadedFiles = $state<Record<string, { file_path: string; original_name: string }>>({});

    // Initialize from existing files
    $effect(() => {
        const init: Record<string, { file_path: string; original_name: string }> = {};
        for (const f of existingFiles) {
            init[f.template_key] = { file_path: f.file_path, original_name: f.original_name };
        }
        uploadedFiles = init;
    });

    // Get templates list based on existing keys (we don't change category on revision)
    let templateKeys = $derived(existingFiles.map((f: any) => f.template_key));

    let hasAnyUpload = $derived(() => Object.values(uploadedFiles).some((v) => v.file_path));

    const form = useForm({ files: [] as any[] });

    function submit() {
        if (uploadState.isUploading) return;

        const filesPayload = Object.entries(uploadedFiles)
            .filter(([_, val]) => val.file_path)
            .map(([key, val]) => ({
                template_key: key,
                file_path: val.file_path,
                original_name: val.original_name,
            }));

        if (filesPayload.length === 0) {
            toast.error('Silakan unggah minimal satu dokumen.');
            return;
        }

        $form.files = filesPayload;

        $form.post(route('apply.ethics.proposal.revise', submission.id), {
            onSuccess: () => {
                toast.success('Revisi berhasil dikirim.');
            },
            onError: () => {
                toast.error('Gagal mengirim revisi.');
            },
        });
    }
</script>

<svelte:head>
    <title>Revisi Pengajuan Etik</title>
</svelte:head>

<AppLayout {breadcrumbs}>
    <LayoutComposer>
        {#snippet header()}
            <Heading title="Revisi Pengajuan Etik" description="Unggah ulang dokumen yang perlu diperbaiki." />
        {/snippet}

        <div class="space-y-6">
            <!-- Comments from reviewer -->
            {#if detail?.comments && detail.comments.length > 0}
                <Card.Root>
                    <Card.Header>
                        <Card.Title>Catatan Reviewer</Card.Title>
                    </Card.Header>
                    <Card.Content>
                        <div class="space-y-3">
                            {#each detail.comments as comment (comment.id)}
                                <div class="border rounded-lg p-3">
                                    <div class="flex items-center justify-between mb-1">
                                        <p class="font-medium text-sm">{comment.user?.name ?? 'Unknown'}</p>
                                        <p class="text-xs text-muted-foreground">{new Date(comment.created_at).toLocaleString('id-ID')}</p>
                                    </div>
                                    <p class="text-sm text-muted-foreground">{comment.content}</p>
                                </div>
                            {/each}
                        </div>
                    </Card.Content>
                </Card.Root>
            {/if}

            <!-- Re-upload documents -->
            <Card.Root>
                <Card.Header>
                    <Card.Title>Unggah Ulang Dokumen</Card.Title>
                    <Card.Description
                        >Anda dapat mengunggah ulang dokumen yang perlu diperbaiki. Dokumen yang tidak diubah akan tetap menggunakan file sebelumnya.</Card.Description
                    >
                </Card.Header>
                <Card.Content>
                    <div class="space-y-4">
                        {#each existingFiles as file (file.id)}
                            <div class="border rounded-lg p-4 space-y-2">
                                <div class="flex items-center justify-between">
                                    <Label class="font-medium">
                                        <span class="capitalize">{file.template_key.replace(/_/g, ' ')}</span>
                                    </Label>
                                    {#if uploadedFiles[file.template_key]?.file_path}
                                        <span class="flex items-center gap-1 text-xs text-green-600">
                                            <CheckCircle2Icon class="h-3.5 w-3.5" />
                                            {uploadedFiles[file.template_key].file_path === file.file_path ? 'File sebelumnya' : 'File baru'}
                                        </span>
                                    {/if}
                                </div>
                                <FileUpload
                                    action={StorageUploadAction.ETHICS_PROPOSAL}
                                    bind:value={
                                        () => uploadedFiles[file.template_key]?.file_path ?? '',
                                        (v) => {
                                            if (!uploadedFiles[file.template_key]) {
                                                uploadedFiles[file.template_key] = { file_path: v || '', original_name: '' };
                                            } else {
                                                uploadedFiles[file.template_key].file_path = v || '';
                                            }
                                        }
                                    }
                                    bind:fileName={
                                        () => uploadedFiles[file.template_key]?.original_name ?? null,
                                        (v) => {
                                            if (!uploadedFiles[file.template_key]) {
                                                uploadedFiles[file.template_key] = { file_path: '', original_name: v || '' };
                                            } else {
                                                uploadedFiles[file.template_key].original_name = v || '';
                                            }
                                        }
                                    }
                                    accept=".doc,.docx"
                                    label="Pilih file baru atau seret ke sini"
                                />
                            </div>
                        {/each}
                    </div>
                </Card.Content>
            </Card.Root>

            <div class="flex justify-end items-center gap-4">
                {#if uploadState.isUploading}
                    <span class="text-sm text-muted-foreground animate-pulse">Mengunggah file...</span>
                {/if}
                <Button onclick={submit} disabled={uploadState.isUploading}>Kirim Revisi</Button>
            </div>
        </div>
    </LayoutComposer>
</AppLayout>
