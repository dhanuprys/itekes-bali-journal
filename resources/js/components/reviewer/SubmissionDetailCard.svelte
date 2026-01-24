<script lang="ts">
    import * as Card from '@/components/ui/card';
    import { Button } from '@/components/ui/button';
    import { Separator } from '@/components/ui/separator';

    let { detail, type = 'research' } = $props();
</script>

<Card.Root>
    <Card.Header>
        <Card.Title>Identitas Usulan</Card.Title>
    </Card.Header>
    <Card.Content>
        <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div class="sm:col-span-2">
                <dt class="text-sm font-medium text-muted-foreground">Judul</dt>
                <dd class="text-lg font-semibold">{detail?.title}</dd>
            </div>

            <div>
                <dt class="text-sm font-medium text-muted-foreground">Ketua Pelaksana</dt>
                <dd class="text-base font-semibold">{detail?.leader_name}</dd>
            </div>

            <div>
                <dt class="text-sm font-medium text-muted-foreground">NIDN/NIP</dt>
                <dd class="text-base font-semibold">{detail?.leader_nidn}</dd>
            </div>

            <div>
                <dt class="text-sm font-medium text-muted-foreground">Program Studi</dt>
                <dd class="text-base font-semibold">{detail?.study_program?.name || '-'}</dd>
            </div>

            <div class="sm:col-span-2">
                <dt class="text-sm font-medium text-muted-foreground">Anggota Tim</dt>
                <dd class="mt-1">
                    {#if detail?.members && detail.members.length > 0}
                        <ul class="list-disc list-inside text-sm">
                            {#each detail.members as member}
                                <li>{member.name}</li>
                            {/each}
                        </ul>
                    {:else}
                        -
                    {/if}
                </dd>
            </div>

            <div>
                <dt class="text-sm font-medium text-muted-foreground">Skema</dt>
                <dd class="text-base font-semibold">
                    {#if type === 'research'}
                        {detail?.research_schema?.name || '-'}
                    {:else}
                        {detail?.schema?.name || '-'}
                    {/if}
                </dd>
            </div>

            <div>
                <dt class="text-sm font-medium text-muted-foreground">Target Luaran</dt>
                <dd class="text-base font-semibold">
                    {#if type === 'research'}
                        {detail?.research_target?.name || '-'}
                    {:else}
                        {detail?.target?.name || '-'}
                    {/if}
                </dd>
            </div>

            <div>
                <dt class="text-sm font-medium text-muted-foreground">Usulan Biaya</dt>
                <dd class="text-base font-semibold">Rp {new Intl.NumberFormat('id-ID').format(detail?.budget)}</dd>
            </div>
        </dl>

        <Separator class="my-6" />

        <div>
            <h4 class="text-sm font-medium mb-2">Dokumen Proposal</h4>
            <div class="flex items-center gap-4">
                <div class="p-3 border rounded bg-muted/50">
                    <span class="text-sm font-mono">File Proposal</span>
                </div>
                <!-- Logic for handling proposal path, it might be different if it's progress report -->
                <Button href={`/storage/${detail?.proposal_path}`} target="_blank" variant="outline" size="sm">Unduh File</Button>
            </div>
        </div>
    </Card.Content>
</Card.Root>
