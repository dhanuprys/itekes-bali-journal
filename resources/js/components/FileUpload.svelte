<script lang="ts">
    import { uploadState } from '@/stores/upload-state.svelte';
    import { Button } from '@/components/ui/button';
    import { Label } from '@/components/ui/label';
    import { Progress } from '@/components/ui/progress';
    import { UploadCloudIcon, FileIcon, XIcon, CheckCircle2Icon, AlertCircleIcon } from 'lucide-svelte';
    import axios from 'axios';
    import { cn } from '@/lib/utils';
    import { tick } from 'svelte';

    interface Props {
        action: string;
        value?: string | null | undefined;
        fileName?: string | null | undefined;
        error?: string;
        accept?: string;
        maxSize?: number; // in bytes
        label?: string;
        description?: string;
        id?: string;
        expectedFileName?: string;
    }

    let {
        action,
        value = $bindable(),
        fileName = $bindable(null),
        error = undefined,
        accept = '.doc,.docx',
        maxSize = 5 * 1024 * 1024, // 5MB
        label = 'Upload File',
        description,
        id = 'file-upload-' + Math.random().toString(36).substring(2, 11),
        expectedFileName,
    }: Props = $props();

    let isLocalDragging = $state(false);
    let isUploading = $state(false);
    let progress = $state(0);
    let localError = $state<string | null>(null);

    // If value already exists (e.g. edit mode), try to extract filename from path or just show "File Uploaded"
    $effect(() => {
        if (value && !fileName) {
            fileName = value.split('/').pop() || 'Uploaded File';
        }
    });

    // A flag to optionally disable the convention check if it's Ethical Clearance template strict matching
    // Also skip convention check for Profile Photos and Payment Proofs
    let requiresConventionCheck = $derived(
        !expectedFileName &&
            action !== StorageUploadAction.USER_PROFILE_PHOTO &&
            action !== StorageUploadAction.ETHICS_PAYMENT_PROOF,
    );

    function validateFileNameConvention(fileName: string): boolean {
        if (!requiresConventionCheck) return true;
        
        // Relaxed validation: Just ensure there are at least 4 parts separated by underscores
        // (Name_Category_Identifier_ContextFile.ext)
        // This allows variations like "Pengabdian Masyarakat" without breaking.
        const parts = fileName.split('_');
        if (parts.length < 4) return false;

        // Ensure the last part has an extension
        const lastPart = parts[parts.length - 1];
        if (!lastPart.includes('.')) return false;

        return true;
    }

    function validateFile(file: File): boolean {
        if (expectedFileName && file.name !== expectedFileName) {
            localError = `Nama file harus sama persis dengan template: "${expectedFileName}".`;
            return false;
        }

        if (requiresConventionCheck && !validateFileNameConvention(file.name)) {
            localError = `Format nama file tidak sesuai. Gunakan format: Nama_Kategori_NIM_JenisDokumen`;
            return false;
        }

        if (maxSize && file.size > maxSize) {
            localError = `File size exceeds ${maxSize / 1024 / 1024}MB limit.`;
            return false;
        }
        return true;
    }

    async function uploadFile(file: File) {
        localError = null;
        if (!validateFile(file)) return;

        isUploading = true;
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
            isUploading = false;
            uploadState.finish();
        } catch (err: any) {
            console.error(err);
            const msg = err.response?.data?.message || 'Upload failed. Please try again.';
            localError = msg;
            isUploading = false;
            uploadState.fail(msg);
            value = undefined; // Clear value on error
            fileName = null;
        }
    }

    function handleDrop(e: DragEvent) {
        e.preventDefault();
        isLocalDragging = false;

        if (isUploading) return;

        if (e.dataTransfer?.files && e.dataTransfer.files.length > 0) {
            uploadFile(e.dataTransfer.files[0]);
        }
    }

    function handleDragOver(e: DragEvent) {
        e.preventDefault();
        if (!isUploading) {
            isLocalDragging = true;
        }
    }

    function handleDragLeave() {
        isLocalDragging = false;
    }

    function handleFileSelect(e: Event) {
        const input = e.target as HTMLInputElement;
        if (input.files && input.files.length > 0) {
            uploadFile(input.files[0]);
        }
    }

    function removeFile() {
        value = undefined;
        fileName = null;
        progress = 0;
        localError = null;
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
            onkeydown={(e) => e.key === 'Enter' && document.getElementById(id)?.click()}
            ondrop={handleDrop}
            ondragover={handleDragOver}
            ondragleave={handleDragLeave}
            onclick={() => document.getElementById(id)?.click()}
            class={cn(
                'relative flex flex-col items-center justify-center w-full p-8 border-2 border-dashed rounded-lg transition-colors cursor-pointer',
                isLocalDragging ? 'border-primary bg-primary/5' : 'border-muted-foreground/25 hover:bg-muted/50',
                isUploading && 'pointer-events-none opacity-60',
                (localError || error) && 'border-destructive/50 bg-destructive/5',
            )}
        >
            <input {id} type="file" class="hidden" onchange={handleFileSelect} {accept} disabled={isUploading} />

            {#if isUploading && progress < 100}
                <div class="w-full max-w-xs space-y-4 text-center">
                    <div class="p-4 mx-auto rounded-full bg-muted">
                        <UploadCloudIcon size={32} class="text-muted-foreground animate-pulse" />
                    </div>
                    <div class="space-y-1">
                        <p class="text-sm font-medium">Uploading...</p>
                        <Progress value={progress} class="h-2" />
                        <p class="text-[0.8rem] text-muted-foreground">{description}</p>
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
                            {#if isLocalDragging}
                                Lepaskan file di sini
                            {:else if localError || error}
                                <span class="text-destructive font-semibold">Upload Gagal</span>
                            {:else}
                                Tarik & lepas atau klik untuk mengunggah
                            {/if}
                        </p>
                        {#if localError || error}
                            <p class="text-xs text-destructive font-medium mt-1 bg-destructive/10 py-1 px-3 rounded-full inline-block">
                                Klik area ini untuk mencoba lagi
                            </p>
                        {:else}
                            <p class="text-xs text-muted-foreground">
                                Format: {accept.replace(/\./g, '').split(',').join(', ').toUpperCase()} (Max {maxSize / 1024 / 1024}MB)
                            </p>
                        {/if}
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

    {#if description}
        <p class="text-xs text-muted-foreground">
            {description}
        </p>
    {/if}

    {#if requiresConventionCheck}
        <div class="mt-2 text-xs text-blue-700 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/20 p-2.5 rounded-md border border-blue-200 dark:border-blue-800">
            <span class="font-semibold block mb-1">Format Penamaan File Wajib:</span> 
            <code class="font-mono bg-blue-100 dark:bg-blue-900 px-1 py-0.5 rounded">Nama_Kategori_NIM/NUPTK_JenisDokumen</code>
            <br />
            <span class="text-[0.7rem] opacity-90 block mt-1">Contoh: I Gede Hendrayana_PKM_1912040_Proposal.docx</span>
        </div>
    {/if}
</div>
