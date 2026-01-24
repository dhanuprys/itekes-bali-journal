<script lang="ts">
    import { Input } from '@/components/ui/input';
    import { Button } from '@/components/ui/button';
    import * as Field from '@/components/ui/field';
    import * as Card from '@/components/ui/card';
    import { Trash2Icon, PlusIcon } from 'lucide-svelte';

    let { form, type = 'research', mode = 'create' } = $props();

    // Derived states
    let isResearch = $derived(type === 'research');
    let titleLabel = $derived(isResearch ? 'Judul Akhir Penelitian' : 'Judul Akhir Pengabdian');
    let leaderLabel = $derived(isResearch ? 'Nama Ketua Peneliti (Akhir)' : 'Nama Ketua Pengabdi (Akhir)');

    function addMember() {
        if (!form.members) form.members = [];
        form.members = [...form.members, { name: '' }];
    }

    function removeMember(index: number) {
        form.members = form.members.filter((_: any, i: number) => i !== index);
    }

    function handleReportFileChange(e: Event) {
        const target = e.target as HTMLInputElement;
        if (target.files && target.files.length > 0) {
            form.final_report_file = target.files[0];
        }
    }

    function handleManuscriptFileChange(e: Event) {
        const target = e.target as HTMLInputElement;
        if (target.files && target.files.length > 0) {
            form.manuscript_file = target.files[0];
        }
    }
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

                    <Field.Field>
                        <Field.Label for="final_leader_name">{leaderLabel}</Field.Label>
                        <Input id="final_leader_name" bind:value={form.final_leader_name} placeholder="Nama Lengkap dengan Gelar" />
                        {#if form.errors?.final_leader_name}
                            <Field.Error>{form.errors?.final_leader_name}</Field.Error>
                        {/if}
                    </Field.Field>
                </Field.Group>
            </Field.Set>

            <Field.Set>
                <Field.Legend>Anggota Tim (Akhir)</Field.Legend>
                <Field.Description>Perbarui daftar anggota yang terlibat dalam pelaksanaan.</Field.Description>

                <Field.Group>
                    {#each form.members as member, i}
                        <div class="flex items-end gap-3">
                            <Field.Field class="flex-1">
                                <Field.Label for={`member-${i}`}>Nama Anggota {i + 1}</Field.Label>
                                <Input id={`member-${i}`} bind:value={member.name} placeholder="Nama Anggota" />
                                {#if form.errors?.[`members.${i}.name`]}
                                    <Field.Error>{form.errors?.[`members.${i}.name`]}</Field.Error>
                                {/if}
                            </Field.Field>
                            <Button variant="ghost" size="icon" onclick={() => removeMember(i)} class="mb-0.5">
                                <Trash2Icon class="h-4 w-4 text-destructive" />
                            </Button>
                        </div>
                    {/each}

                    <div>
                        <Button variant="outline" size="sm" onclick={addMember} class="gap-2">
                            <PlusIcon class="h-4 w-4" /> Tambah Anggota
                        </Button>
                    </div>
                </Field.Group>
            </Field.Set>

            <Field.Set>
                <Field.Legend>Dokumen Laporan</Field.Legend>
                <Field.Description>Unggah laporan akhir dan naskah publikasi.</Field.Description>

                <Field.Group>
                    <Field.Field>
                        <Field.Label for="final_report_file">Laporan Akhir (PDF/DOC, Max 10MB)</Field.Label>
                        <Input id="final_report_file" type="file" onchange={handleReportFileChange} accept=".pdf,.doc,.docx" />
                        {#if form.errors?.final_report_file}
                            <Field.Error>{form.errors?.final_report_file}</Field.Error>
                        {/if}
                    </Field.Field>

                    <Field.Field>
                        <Field.Label for="manuscript_file">Naskah Publikasi / Manuskrip (PDF/DOC, Max 10MB)</Field.Label>
                        <Input id="manuscript_file" type="file" onchange={handleManuscriptFileChange} accept=".pdf,.doc,.docx" />
                        {#if form.errors?.manuscript_file}
                            <Field.Error>{form.errors?.manuscript_file}</Field.Error>
                        {/if}
                    </Field.Field>
                </Field.Group>
            </Field.Set>
        </div>
    </Card.Content>
</Card.Root>
