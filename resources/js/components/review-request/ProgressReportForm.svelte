<script lang="ts">
    import { Input } from '@/components/ui/input';
    import * as Field from '@/components/ui/field';
    import * as Card from '@/components/ui/card';
    import FileUpload from '@/components/FileUpload.svelte';
    import { StorageUploadAction } from '@/data/storage-upload';
    import * as Select from '@/components/ui/select';
    import Label from '../ui/label/label.svelte';

    let { form = $bindable(), data, type = 'research', mode = 'create' } = $props();

    // Derived states
    let isResearch = $derived(type === 'research');
    let titleLabel = $derived(isResearch ? 'Judul Akhir Penelitian' : 'Judul Akhir Pengabdian');
    let leaderLabel = $derived(isResearch ? 'Nama Ketua Peneliti (Akhir)' : 'Nama Ketua Pengabdi (Akhir)');
</script>

<Card.Root>
    <Card.Header>
        <Card.Title>Laporan Kemajuan</Card.Title>
        <Card.Description>Lengkapi data laporan kemajuan/akhir Anda. Data ini akan digunakan untuk verifikasi akhir.</Card.Description>
    </Card.Header>
    <Card.Content>
        <div class="space-y-6">
            <Field.Set>
                <Field.Legend>Informasi Akhir</Field.Legend>
                <Field.Description>Perbarui informasi jika ada perubahan dari proposal awal.</Field.Description>

                <Field.Group>
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <Field.Field>
                            <Field.Label for="final_title">{titleLabel}</Field.Label>
                            <Input id="final_title" bind:value={form.final_title} placeholder="Judul Lengkap Akhir" />
                            {#if form.errors?.final_title}
                                <Field.Error>{form.errors?.final_title}</Field.Error>
                            {/if}
                        </Field.Field>

                        <Field.Field>
                            <Field.Label for="final_leader_name">{leaderLabel}</Field.Label>
                            <Input id="final_leader_name" bind:value={form.final_leader_name} placeholder="Nama Lengkap dengan Gelar" />
                            {#if form.errors?.final_leader_name}
                                <Field.Error>{form.errors?.final_leader_name}</Field.Error>
                            {/if}
                        </Field.Field>
                    </div>
                </Field.Group>
            </Field.Set>

            <Field.Set>
                <Field.Legend>Dokumen Laporan</Field.Legend>
                <Field.Description>Unggah laporan kemajuan dan naskah publikasi.</Field.Description>

                <Field.Group>
                    <Field.Field>
                        <div class="col-span-1 md:col-span-2">
                            <Label for="progress_report_path">File Laporan Kemajuan *</Label>
                            <FileUpload
                                action={type === 'research' ? StorageUploadAction.RESEARCH_PROGRESS_REPORT : StorageUploadAction.CS_PROGRESS_REPORT}
                                bind:value={form.progress_report_path}
                                accept=".doc,.docx"
                                description="Upload laporan kemajuan."
                            />
                            {#if form.errors?.progress_report_path}
                                <p class="text-sm text-destructive mt-1">{form.errors.progress_report_path}</p>
                            {/if}
                            {#if mode === 'revise'}
                                <p class="text-[0.8rem] text-muted-foreground mt-1">Biarkan kosong jika tidak mengubah file.</p>
                            {/if}
                        </div>
                    </Field.Field>
                </Field.Group>
            </Field.Set>
        </div>
    </Card.Content>
</Card.Root>
