<script lang="ts">
    import { Input } from '@/components/ui/input';
    import * as Field from '@/components/ui/field';
    import * as Card from '@/components/ui/card';
    import FileUpload from '@/components/FileUpload.svelte';
    import { StorageUploadAction } from '@/data/storage-upload';

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
                            <Field.Label for="leader_nidn">NIDN/NIP Ketua</Field.Label>
                            <Input id="leader_nidn" bind:value={form.leader_nidn} placeholder="Nomor Induk" />
                            {#if form.errors?.leader_nidn}
                                <Field.Error>{form.errors?.leader_nidn}</Field.Error>
                            {/if}
                        </Field.Field>
                    </div>

                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <Field.Field>
                            <Field.Label for="final_leader_name">{leaderLabel}</Field.Label>
                            <Input id="final_leader_name" bind:value={form.final_leader_name} placeholder="Nama Lengkap dengan Gelar" />
                            {#if form.errors?.final_leader_name}
                                <Field.Error>{form.errors?.final_leader_name}</Field.Error>
                            {/if}
                        </Field.Field>

                        <Field.Field>
                            <Field.Label for="schema_id">Skema</Field.Label>
                            <select
                                id="schema_id"
                                bind:value={form.schema_id}
                                class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50"
                            >
                                <option value="">Pilih Skema</option>
                                {#each data.schemas as schema}
                                    <option value={schema.id}>{schema.name}</option>
                                {/each}
                            </select>
                            {#if form.errors?.schema_id}
                                <Field.Error>{form.errors?.schema_id}</Field.Error>
                            {/if}
                        </Field.Field>
                    </div>
                </Field.Group>
            </Field.Set>

            <Field.Set>
                <Field.Legend>Dokumen Laporan</Field.Legend>
                <Field.Description>Unggah laporan akhir dan naskah publikasi.</Field.Description>

                <Field.Group>
                    <Field.Field>
                        <FileUpload
                            label="Laporan Akhir (PDF/DOC, Max 10MB)"
                            action={isResearch ? StorageUploadAction.RESEARCH_FINAL_REPORT : StorageUploadAction.CS_FINAL_REPORT}
                            bind:value={form.final_report_path}
                            error={form.errors?.final_report_path}
                        />
                    </Field.Field>

                    <Field.Field>
                        <FileUpload
                            label="Naskah Publikasi / Manuskrip (PDF/DOC, Max 10MB)"
                            action={isResearch ? StorageUploadAction.RESEARCH_MANUSCRIPT : StorageUploadAction.CS_MANUSCRIPT}
                            bind:value={form.manuscript_path}
                            error={form.errors?.manuscript_path}
                        />
                    </Field.Field>
                </Field.Group>
            </Field.Set>
        </div>
    </Card.Content>
</Card.Root>
