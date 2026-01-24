<script lang="ts">
    import { Input } from '@/components/ui/input';
    import { Button } from '@/components/ui/button';
    import * as Field from '@/components/ui/field';
    import * as Card from '@/components/ui/card';
    import * as Alert from '@/components/ui/alert';
    import { Trash2Icon, PlusIcon } from 'lucide-svelte';

    let { form, data, type = 'research', mode = 'create' } = $props();

    // Derived states
    let isResearch = $derived(type === 'research');
    let schemaLabel = $derived(isResearch ? 'Skema Penelitian' : 'Skema Pengabdian');
    let targetLabel = $derived(isResearch ? 'Target Luaran' : 'Target Luaran');
    let titleLabel = $derived(isResearch ? 'Judul Penelitian' : 'Judul Pengabdian');
    let leaderLabel = $derived(isResearch ? 'Nama Ketua Peneliti' : 'Nama Ketua Pengabdi');

    function addMember() {
        if (!form.members) form.members = [];
        form.members = [...form.members, { name: '' }];
    }

    function removeMember(index: number) {
        form.members = form.members.filter((_: any, i: number) => i !== index);
    }

    function handleFileChange(e: Event) {
        const target = e.target as HTMLInputElement;
        if (target.files && target.files.length > 0) {
            form.proposal_file = target.files[0];
        }
    }
</script>

{#if mode === 'revise' && form.processing}
    <!-- Status alert or something could go here -->
{/if}

<Card.Root>
    <Card.Header>
        <Card.Title>{mode === 'create' ? 'Formulir Pengajuan' : 'Formulir Revisi'}</Card.Title>
        <Card.Description>
            {mode === 'create' ? 'Lengkapi data usulan Anda.' : 'Perbarui data usulan Anda.'}
        </Card.Description>
    </Card.Header>
    <Card.Content>
        <div class="space-y-6">
            <Field.Set>
                <Field.Legend>Informasi Umum</Field.Legend>
                <Field.Description>Data dasar mengenai usulan ini.</Field.Description>

                <Field.Group>
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <Field.Field>
                            <Field.Label for="study_program_id">Program Studi</Field.Label>
                            <select
                                id="study_program_id"
                                bind:value={form.study_program_id}
                                class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50"
                            >
                                <option value="">Pilih Program Studi</option>
                                {#each data.studyPrograms as program}
                                    <option value={program.id}>{program.name}</option>
                                {/each}
                            </select>
                            {#if form.errors?.study_program_id}
                                <Field.Error>{form.errors?.study_program_id}</Field.Error>
                            {/if}
                        </Field.Field>

                        <Field.Field>
                            <Field.Label for="title">{titleLabel}</Field.Label>
                            <Input id="title" bind:value={form.title} placeholder="Judul Lengkap" />
                            {#if form.errors?.title}
                                <Field.Error>{form.errors?.title}</Field.Error>
                            {/if}
                        </Field.Field>
                    </div>
                </Field.Group>
            </Field.Set>

            <Field.Set>
                <Field.Legend>Anggota Tim</Field.Legend>
                <Field.Description>Daftar anggota yang terlibat.</Field.Description>

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
                <Field.Legend>Ketua Pelaksana</Field.Legend>
                <Field.Description>Identitas ketua pengusul.</Field.Description>

                <Field.Group>
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <Field.Field>
                            <Field.Label for="leader_name">{leaderLabel}</Field.Label>
                            <Input id="leader_name" bind:value={form.leader_name} placeholder="Nama Lengkap dengan Gelar" />
                            {#if form.errors?.leader_name}
                                <Field.Error>{form.errors?.leader_name}</Field.Error>
                            {/if}
                        </Field.Field>

                        <Field.Field>
                            <Field.Label for="leader_nidn">NIDN/NIP</Field.Label>
                            <Input id="leader_nidn" bind:value={form.leader_nidn} placeholder="Nomor Induk" />
                            {#if form.errors?.leader_nidn}
                                <Field.Error>{form.errors?.leader_nidn}</Field.Error>
                            {/if}
                        </Field.Field>
                    </div>
                </Field.Group>
            </Field.Set>

            <Field.Set>
                <Field.Legend>Detail Kegiatan</Field.Legend>
                <Field.Description>Rincian skema, target, dan biaya.</Field.Description>

                <Field.Group>
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <Field.Field>
                            <Field.Label for="schema_id">{schemaLabel}</Field.Label>
                            {#if isResearch}
                                <select
                                    id="schema_id"
                                    bind:value={form.research_schema_id}
                                    class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50"
                                >
                                    <option value="">Pilih Skema</option>
                                    {#each data.schemas as schema}
                                        <option value={schema.id}>{schema.name}</option>
                                    {/each}
                                </select>
                                {#if form.errors?.research_schema_id}
                                    <Field.Error>{form.errors?.research_schema_id}</Field.Error>
                                {/if}
                            {:else}
                                <select
                                    id="schema_id"
                                    bind:value={form.community_service_schema_id}
                                    class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50"
                                >
                                    <option value="">Pilih Skema</option>
                                    {#each data.schemas as schema}
                                        <option value={schema.id}>{schema.name}</option>
                                    {/each}
                                </select>
                                {#if form.errors?.community_service_schema_id}
                                    <Field.Error>{form.errors?.community_service_schema_id}</Field.Error>
                                {/if}
                            {/if}
                        </Field.Field>

                        <Field.Field>
                            <Field.Label for="target_id">{targetLabel}</Field.Label>
                            {#if isResearch}
                                <select
                                    id="target_id"
                                    bind:value={form.research_target_id}
                                    class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50"
                                >
                                    <option value="">Pilih Target</option>
                                    {#each data.targets as target}
                                        <option value={target.id}>{target.name}</option>
                                    {/each}
                                </select>
                                {#if form.errors?.research_target_id}
                                    <Field.Error>{form.errors?.research_target_id}</Field.Error>
                                {/if}
                            {:else}
                                <select
                                    id="target_id"
                                    bind:value={form.community_service_target_id}
                                    class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50"
                                >
                                    <option value="">Pilih Target</option>
                                    {#each data.targets as target}
                                        <option value={target.id}>{target.name}</option>
                                    {/each}
                                </select>
                                {#if form.errors?.community_service_target_id}
                                    <Field.Error>{form.errors?.community_service_target_id}</Field.Error>
                                {/if}
                            {/if}
                        </Field.Field>
                    </div>

                    <Field.Field>
                        <Field.Label for="budget">Usulan Biaya (Rp)</Field.Label>
                        <Input id="budget" type="number" bind:value={form.budget} placeholder="0" />
                        {#if form.errors?.budget}
                            <Field.Error>{form.errors?.budget}</Field.Error>
                        {/if}
                    </Field.Field>
                </Field.Group>
            </Field.Set>

            <Field.Set>
                <Field.Legend>Dokumen</Field.Legend>
                <Field.Description>Unggah file proposal Anda.</Field.Description>

                <Field.Group>
                    <Field.Field>
                        <Field.Label for="proposal_file">File Proposal (PDF/DOC, Max 10MB)</Field.Label>
                        <Input id="proposal_file" type="file" onchange={handleFileChange} accept=".pdf,.doc,.docx" />
                        {#if mode === 'revise'}
                            <Field.Description>Biarkan kosong jika tidak mengubah file.</Field.Description>
                        {/if}
                        {#if form.errors?.proposal_file}
                            <Field.Error>{form.errors?.proposal_file}</Field.Error>
                        {/if}
                    </Field.Field>
                </Field.Group>
            </Field.Set>
        </div>
    </Card.Content>
</Card.Root>
