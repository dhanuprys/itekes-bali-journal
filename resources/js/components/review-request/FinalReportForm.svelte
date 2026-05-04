<script lang="ts">
    import { Input } from '@/components/ui/input';
    import { Label } from '@/components/ui/label';
    import FileUpload from '@/components/FileUpload.svelte';
    import { StorageUploadAction } from '@/data/storage-upload';

    let { form = $bindable(), type = 'research', mode = 'create' } = $props();
</script>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div class="col-span-1 md:col-span-2">
        <Label for="final_report_path">File Laporan Akhir *</Label>
        <FileUpload
            action={type === 'research' ? StorageUploadAction.RESEARCH_FINAL_REPORT : StorageUploadAction.CS_FINAL_REPORT}
            bind:value={form.final_report_path}
            accept=".pdf,.doc,.docx"
            description="Upload laporan akhir. Format: PDF, DOC, DOCX. Maksimal 4MB."
        />
        {#if form.errors?.final_report_path}
            <p class="text-sm text-destructive mt-1">{form.errors.final_report_path}</p>
        {/if}
    </div>
</div>
