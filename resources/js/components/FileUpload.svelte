<script lang="ts">
    import { uploadState } from '@/stores/upload-state.svelte';
    import { StorageUploadAction } from '@/data/storage-upload';
    import { Button } from '@/components/ui/button';
    import { Label } from '@/components/ui/label';
    import { Progress } from '@/components/ui/progress';
    import { UploadCloudIcon, FileIcon, XIcon, CheckCircle2Icon, AlertCircleIcon } from 'lucide-svelte';
    import axios from 'axios';
    import { cn } from '@/lib/utils';
    import { tick } from 'svelte';

    interface Props {
        action: string;
        value?: string | null;
        error?: string;
        accept?: string;
        maxSize?: number; // in bytes
        label?: string;
    }

    let {
        action,
        value = $bindable(null),
        error = undefined,
        accept = '.pdf,.doc,.docx',
        maxSize = 10 * 1024 * 1024, // 10MB default
        label = 'Upload File',
    }: Props = $props();

    let isDragging = $state(false);
    let progress = $state(0);
    let localError = $state<string | null>(null);
    let fileName = $state<string | null>(null);

    // If value already exists (e.g. edit mode), try to extract filename from path or just show "File Uploaded"
    $effect(() => {
        if (value && !fileName) {
            fileName = value.split('/').pop() || 'Uploaded File';
        }
    });

    function validateFile(file: File): boolean {
        if (maxSize && file.size > maxSize) {
            localError = `File size exceeds ${maxSize / 1024 / 1024}MB limit.`;
            return false;
        }
        // Basic extension check could be added here if needed, but 'accept' attribute handles the dialog
        return true;
    }

    async function uploadFile(file: File) {
        localError = null;
        if (!validateFile(file)) return;

        uploadState.start();
        progress = 0;
        fileName = file.name;

        const formData = new FormData();
        formData.append('file', file);
        formData.append('action', action);

        try {
            const response = await axios.post(route('storage.upload'), formData, {
                headers: {
                    'Content-Type': 'multipart/form-data',
                },
                onUploadProgress: (progressEvent) => {
                    if (progressEvent.total) {
                        progress = Math.round((progressEvent.loaded * 100) / progressEvent.total);
                    }
                },
            });

            // Update value
            value = response.data.path;

            await tick(); // Force DOM update
            uploadState.finish();
        } catch (err: any) {
            console.error(err);
            const msg = err.response?.data?.message || 'Upload failed. Please try again.';
            localError = msg;
            uploadState.fail(msg);
            value = null; // Clear value on error
            fileName = null;
        }
    }

    function handleDrop(e: DragEvent) {
        e.preventDefault();
        isDragging = false;

        if (uploadState.isUploading) return;

        if (e.dataTransfer?.files && e.dataTransfer.files.length > 0) {
            uploadFile(e.dataTransfer.files[0]);
        }
    }

    function handleDragOver(e: DragEvent) {
        e.preventDefault();
        if (!uploadState.isUploading) {
            isDragging = true;
        }
    }

    function handleDragLeave() {
        isDragging = false;
    }

    function handleFileSelect(e: Event) {
        const input = e.target as HTMLInputElement;
        if (input.files && input.files.length > 0) {
            uploadFile(input.files[0]);
        }
    }

    function removeFile() {
        value = null;
        fileName = null;
        progress = 0;
        localError = null;
        // Optionally notify backend to delete/unused the file if needed, but for now just clear local state
    }
</script>

<div class="space-y-2">
    {#if label}
        <Label>{label}</Label>
    {/if}

    {#if value}
        <!-- File Preview / Connected State -->
        <div class="relative flex items-center justify-between p-4 border rounded-lg bg-card text-card-foreground shadow-sm">
            <div class="flex items-center gap-3 overflow-hidden">
                <div class="p-2 rounded-full bg-primary/10 text-primary">
                    <FileIcon size={24} />
                </div>
                <div class="flex flex-col min-w-0">
                    <span class="text-sm font-medium truncate">{fileName}</span>
                    <span class="text-xs text-muted-foreground flex items-center gap-1">
                        <CheckCircle2Icon size={12} class="text-green-500" /> Uploaded
                    </span>
                </div>
            </div>
            <Button variant="ghost" size="icon" onclick={removeFile} aria-label="Remove file">
                <XIcon size={18} class="text-muted-foreground hover:text-destructive" />
            </Button>
        </div>
    {:else}
        <!-- Dropzone -->
        <div
            role="button"
            tabindex="0"
            onkeydown={(e) => e.key === 'Enter' && document.getElementById('file-upload-input')?.click()}
            ondrop={handleDrop}
            ondragover={handleDragOver}
            ondragleave={handleDragLeave}
            onclick={() => document.getElementById('file-upload-input')?.click()}
            class={cn(
                'relative flex flex-col items-center justify-center w-full p-8 border-2 border-dashed rounded-lg transition-colors cursor-pointer',
                isDragging ? 'border-primary bg-primary/5' : 'border-muted-foreground/25 hover:bg-muted/50',
                uploadState.isUploading && 'pointer-events-none opacity-60',
                (localError || error) && 'border-destructive/50 bg-destructive/5',
            )}
        >
            <input id="file-upload-input" type="file" class="hidden" onchange={handleFileSelect} {accept} disabled={uploadState.isUploading} />

            {#if uploadState.isUploading && progress < 100}
                <div class="w-full max-w-xs space-y-4 text-center">
                    <div class="p-4 mx-auto rounded-full bg-muted">
                        <UploadCloudIcon size={32} class="text-muted-foreground animate-pulse" />
                    </div>
                    <div class="space-y-1">
                        <p class="text-sm font-medium">Uploading...</p>
                        <Progress value={progress} class="h-2" />
                        <p class="text-xs text-muted-foreground">{progress}%</p>
                    </div>
                </div>
            {:else}
                <div class="flex flex-col items-center gap-2 text-center">
                    <div
                        class={cn('p-4 rounded-full', localError || error ? 'bg-destructive/10 text-destructive' : 'bg-muted text-muted-foreground')}
                    >
                        {#if localError || error}
                            <AlertCircleIcon size={32} />
                        {:else}
                            <UploadCloudIcon size={32} />
                        {/if}
                    </div>
                    <div class="space-y-1">
                        <p class="text-sm font-medium">
                            {#if isDragging}
                                Drop files here
                            {:else if localError || error}
                                <span class="text-destructive">Upload Failed</span>
                            {:else}
                                Drag & drop or click to upload
                            {/if}
                        </p>
                        <p class="text-xs text-muted-foreground">
                            Supported formats: {accept.replace(/\./g, ' ').toUpperCase()} (Max {maxSize / 1024 / 1024}MB)
                        </p>
                    </div>
                </div>
            {/if}
        </div>
    {/if}

    <!-- Error Display -->
    {#if localError || error}
        <p class="text-sm font-medium text-destructive transition-all">
            {localError || error}
        </p>
    {/if}
</div>
