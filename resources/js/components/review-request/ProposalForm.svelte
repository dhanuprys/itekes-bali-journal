<script lang="ts">
    import { Input } from '@/components/ui/input';
    import { Button } from '@/components/ui/button';
    import * as Field from '@/components/ui/field';
    import * as Card from '@/components/ui/card';
    import { Trash2Icon, PlusIcon } from 'lucide-svelte';
    import FileUpload from '@/components/FileUpload.svelte';
    import { StorageUploadAction } from '@/data/storage-upload';
    import { getProposalHint } from '@/data/file-naming';
    import * as Select from '@/components/ui/select';
    import { CurrencyInput } from '@canutin/svelte-currency-input';

    let { form = $bindable(), data, type = 'research', mode = 'create' } = $props();

    // Derived states
    let isResearch = $derived(type === 'research');
    let targetLabel = $derived(isResearch ? 'Target Luaran' : 'Target Luaran');

    let titleLabel = $derived(isResearch ? 'Judul Penelitian' : 'Judul Pengabdian');
    let leaderLabel = $derived(isResearch ? 'Nama Ketua Peneliti' : 'Nama Ketua Pengabdi');

    // Resolve targets and schemas based on type
    let targets = $derived(data.targets || []);
    let schemas = $derived(data.schemas || []);

    function addMember(e: Event) {
        e.preventDefault();
        if (!form.members) form.members = [];
        form.members = [...form.members, { name: '', identifier: '' }];
    }

    function removeMember(index: number) {
        form.members = form.members.filter((_: any, i: number) => i !== index);
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
            <!-- GENERAL INFO -->
            <Field.Set>
                <Field.Legend>Informasi Umum</Field.Legend>
                <Field.Description>Data dasar mengenai usulan ini.</Field.Description>

                <Field.Group>
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <Field.Field>
                            <Field.Label for="title">{titleLabel}</Field.Label>
                            <Input id="title" bind:value={form.title} placeholder="Judul Lengkap" />
                            {#if form.errors?.title}
                                <Field.Error>{form.errors?.title}</Field.Error>
                            {/if}
                        </Field.Field>

                        <Field.Field>
                            <Field.Label for="study_program_id">Program Studi</Field.Label>
                            <Select.Root type="single" bind:value={form.study_program_id} name="study_program_id">
                                <Select.Trigger class="w-full">
                                    {data.studyPrograms.find((s: any) => s.id === form.study_program_id)?.name ?? 'Pilih Program Studi'}
                                </Select.Trigger>
                                <Select.Content>
                                    {#each data.studyPrograms as program (program.id)}
                                        <Select.Item value={program.id} label={program.name}>{program.name}</Select.Item>
                                    {/each}
                                </Select.Content>
                            </Select.Root>
                            {#if form.errors?.study_program_id}
                                <Field.Error>{form.errors?.study_program_id}</Field.Error>
                            {/if}
                        </Field.Field>
                        <Field.Field>
                            <Field.Label for="schema">Skema</Field.Label>
                            {#if isResearch}
                                <Select.Root type="single" bind:value={form.research_schema_id} name="research_schema_id">
                                    <Select.Trigger class="w-full">
                                        {schemas.find((s: any) => s.id === form.research_schema_id)?.title ?? 'Pilih Skema'}
                                    </Select.Trigger>
                                    <Select.Content>
                                        {#each schemas as schema (schema.id)}
                                            <Select.Item value={schema.id} label={schema.title}>{schema.title}</Select.Item>
                                        {/each}
                                    </Select.Content>
                                </Select.Root>
                                {#if form.errors?.research_schema_id}
                                    <Field.Error>{form.errors?.research_schema_id}</Field.Error>
                                {/if}
                            {:else}
                                <Select.Root type="single" bind:value={form.community_service_schema_id} name="community_service_schema_id">
                                    <Select.Trigger class="w-full">
                                        {schemas.find((s: any) => s.id === form.community_service_schema_id)?.title ?? 'Pilih Skema'}
                                    </Select.Trigger>
                                    <Select.Content>
                                        {#each schemas as schema (schema.id)}
                                            <Select.Item value={schema.id} label={schema.title}>{schema.title}</Select.Item>
                                        {/each}
                                    </Select.Content>
                                </Select.Root>
                                {#if form.errors?.community_service_schema_id}
                                    <Field.Error>{form.errors?.community_service_schema_id}</Field.Error>
                                {/if}
                            {/if}
                        </Field.Field>
                    </div>
                </Field.Group>
            </Field.Set>

            <!-- TEAM -->
            <Field.Set>
                <Field.Legend>Tim Pelaksana</Field.Legend>
                <Field.Description>Identitas ketua dan anggota tim.</Field.Description>

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
                            <Field.Label for="leader_nidn">NIDN Ketua (Opsional)</Field.Label>
                            <Input id="leader_nidn" bind:value={form.leader_nidn} placeholder="NIDN Ketua" />
                            {#if form.errors?.leader_nidn}
                                <Field.Error>{form.errors?.leader_nidn}</Field.Error>
                            {/if}
                        </Field.Field>

                        <Field.Field>
                            <Field.Label for="leader_nuptk">NUPTK Ketua</Field.Label>
                            <Input id="leader_nuptk" bind:value={form.leader_nuptk} placeholder="NUPTK Ketua" />
                            {#if form.errors?.leader_nuptk}
                                <Field.Error>{form.errors?.leader_nuptk}</Field.Error>
                            {/if}
                        </Field.Field>
                    </div>

                    <div class="space-y-3">
                        <Field.Label>Anggota Tim</Field.Label>
                        {#each form.members as member, i (i)}
                            <div class="flex items-end gap-3">
                                <Field.Field class="flex-1">
                                    <Input id={`member-${i}`} bind:value={member.name} placeholder={`Nama Anggota ${i + 1}`} />
                                    {#if form.errors?.[`members.${i}.name`]}
                                        <Field.Error>{form.errors?.[`members.${i}.name`]}</Field.Error>
                                    {/if}
                                </Field.Field>
                                <Field.Field class="flex-1">
                                    <Input id={`member-id-${i}`} bind:value={member.identifier} placeholder="NIM / NUPTK (Opsional)" />
                                    {#if form.errors?.[`members.${i}.identifier`]}
                                        <Field.Error>{form.errors?.[`members.${i}.identifier`]}</Field.Error>
                                    {/if}
                                </Field.Field>
                                <Button type="button" variant="ghost" size="icon" onclick={() => removeMember(i)} class="mb-0.5">
                                    <Trash2Icon class="h-4 w-4 text-destructive" />
                                </Button>
                            </div>
                        {/each}

                        <div>
                            <Button type="button" variant="outline" size="sm" onclick={addMember} class="gap-2">
                                <PlusIcon class="h-4 w-4" /> Tambah Anggota
                            </Button>
                        </div>
                    </div>
                </Field.Group>
            </Field.Set>

            <!-- PROPOSAL DETAILS & BUDGET -->
            <Field.Set>
                <Field.Legend>Detail & Biaya</Field.Legend>
                <Field.Description>Target luaran dan rencana anggaran.</Field.Description>

                <Field.Group>
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <Field.Field>
                            <Field.Label for="target_id">{targetLabel}</Field.Label>
                            {#if isResearch}
                                <Select.Root type="single" bind:value={form.research_target_id} name="research_target_id">
                                    <Select.Trigger class="w-full">
                                        {targets.find((t: any) => t.id === form.research_target_id)?.title ?? 'Pilih Target'}
                                    </Select.Trigger>
                                    <Select.Content>
                                        {#each targets as target (target.id)}
                                            <Select.Item value={target.id} label={target.title}>{target.title}</Select.Item>
                                        {/each}
                                    </Select.Content>
                                </Select.Root>
                                {#if form.errors?.research_target_id}
                                    <Field.Error>{form.errors?.research_target_id}</Field.Error>
                                {/if}
                            {:else}
                                <Select.Root type="single" bind:value={form.community_service_target_id} name="community_service_target_id">
                                    <Select.Trigger class="w-full">
                                        {targets.find((t: any) => t.id === form.community_service_target_id)?.title ?? 'Pilih Target'}
                                    </Select.Trigger>
                                    <Select.Content>
                                        {#each targets as target (target.id)}
                                            <Select.Item value={target.id} label={target.title}>{target.title}</Select.Item>
                                        {/each}
                                    </Select.Content>
                                </Select.Root>
                                {#if form.errors?.community_service_target_id}
                                    <Field.Error>{form.errors?.community_service_target_id}</Field.Error>
                                {/if}
                            {/if}
                        </Field.Field>

                        <Field.Field>
                            <Field.Label for="budget">Usulan Biaya (Rp)</Field.Label>
                            <CurrencyInput
                                id="budget"
                                intlConfig={{ locale: 'id-ID', currency: 'IDR' }}
                                decimalsLimit={0}
                                disableAbbreviations={true}
                                bind:value={form.budget}
                                class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                                placeholder="0"
                            />
                            {#if form.errors?.budget}
                                <Field.Error>{form.errors?.budget}</Field.Error>
                            {/if}
                        </Field.Field>
                    </div>
                </Field.Group>
            </Field.Set>

            <!-- DOCUMENTS -->
            <Field.Set>
                <Field.Legend>Dokumen</Field.Legend>
                <Field.Description>Unggah file proposal Anda.</Field.Description>

                <Field.Group>
                    <Field.Field>
                        <FileUpload
                            label="File Proposal (DOC, Max 5MB)"
                            action={isResearch ? StorageUploadAction.RESEARCH_PROPOSAL : StorageUploadAction.CS_PROPOSAL}
                            bind:value={form.proposal_path}
                            error={form.errors?.proposal_path}
                            namingHint={getProposalHint(type as 'research' | 'community-service', mode as 'create' | 'revise')}
                        />
                        {#if mode === 'revise'}
                            <Field.Description>Biarkan kosong jika tidak mengubah file.</Field.Description>
                        {/if}
                    </Field.Field>
                </Field.Group>
            </Field.Set>
        </div>
    </Card.Content>
</Card.Root>
