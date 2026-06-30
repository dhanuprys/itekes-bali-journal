<script lang="ts">
    import { Input } from '@/components/ui/input';
    import { Label } from '@/components/ui/label';
    import FileUpload from '@/components/file-upload.svelte';
    import { StorageUploadAction } from '@/data/storage-upload';
    import { getFinalReportHint, NamingHints } from '@/data/file-naming';

    let { form = $bindable(), type = 'research', mode = 'create' } = $props();
</script>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div class="col-span-1 md:col-span-2 space-y-4">
        <div>
            <Label for="final_report_path">File Laporan Akhir *</Label>
            <FileUpload
                action={type === 'research' ? StorageUploadAction.RESEARCH_FINAL_REPORT : StorageUploadAction.CS_FINAL_REPORT}
                bind:value={form.final_report_path}
                accept=".doc,.docx"
                description="Upload laporan akhir. Format: DOC, DOCX. Maksimal 5MB."
                namingHint={getFinalReportHint(type as 'research' | 'community-service')}
            />
            {#if form.errors?.final_report_path}
                <p class="text-sm text-destructive mt-1">{form.errors.final_report_path}</p>
            {/if}
        </div>

        <div>
            <Label for="manuscript_path">File Manuskrip Akhir *</Label>
            <FileUpload
                action={type === 'research' ? StorageUploadAction.RESEARCH_MANUSCRIPT : StorageUploadAction.CS_MANUSCRIPT}
                bind:value={form.manuscript_path}
                accept=".doc,.docx"
                description="Upload manuskrip. Format: DOC, DOCX. Maksimal 5MB."
                namingHint={NamingHints.MANUSCRIPT}
            />
            {#if form.errors?.manuscript_path}
                <p class="text-sm text-destructive mt-1">{form.errors.manuscript_path}</p>
            {/if}
        </div>

        <div class="space-y-3">
            <Label for="supplementary_path">File Luaran (LOA, HKI, Modul, Buku, Artikel Jurnal dll) *</Label>
            <div
                class="bg-blue-50/50 dark:bg-blue-900/20 text-blue-800 dark:text-blue-300 p-3 rounded-lg border border-blue-200 dark:border-blue-800 text-sm flex gap-3"
            >
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    width="20"
                    height="20"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    class="shrink-0 mt-0.5"><circle cx="12" cy="12" r="10" /><path d="M12 16v-4" /><path d="M12 8h.01" /></svg
                >
                <div class="space-y-1">
                    <p class="font-medium">Perhatian</p>
                    <p class="text-blue-700/90 dark:text-blue-400/90 text-xs leading-relaxed">
                        Mohon satukan file luaran (berisi LOA, HKI, Modul, Buku, Artikel Jurnal, dll) ke dalam bentuk <strong>.ZIP</strong> dengan
                        maksimal <strong>6 MB</strong>. Nama file akan diubah secara otomatis menjadi File Luaran.
                    </p>
                </div>
            </div>
            <FileUpload
                action={type === 'research' ? StorageUploadAction.RESEARCH_SUPPLEMENTARY : StorageUploadAction.CS_SUPPLEMENTARY}
                bind:value={form.supplementary_path}
                accept=".zip"
                maxSize={6 * 1024 * 1024}
                description="Upload file luaran. Format: ZIP. Maksimal 6MB."
                namingHint={NamingHints.SUPPLEMENTARY}
            />
            {#if form.errors?.supplementary_path}
                <p class="text-sm text-destructive mt-1">{form.errors.supplementary_path}</p>
            {/if}
        </div>

        <div class="space-y-3">
            <Label for="notes">Keterangan</Label>
            <Input id="notes" bind:value={form.notes} placeholder="Tambahkan keterangan..." />
            {#if form.errors?.notes}
                <p class="text-sm text-destructive mt-1">{form.errors.notes}</p>
            {/if}
        </div>
    </div>
</div>
