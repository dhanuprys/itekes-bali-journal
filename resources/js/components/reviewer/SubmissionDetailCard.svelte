<script lang="ts">
    import { Button } from '@/components/ui/button';
    import { Separator } from '@/components/ui/separator';
    import * as Alert from '@/components/ui/alert';
    import { InfoIcon } from 'lucide-svelte';

    interface Props {
        detail: any;
        type: 'research' | 'community-service';
        stage: 'proposal' | 'progress_report' | 'final_report';
    }

    // eslint-disable-next-line @typescript-eslint/no-unused-vars
    let { detail, type: _type, stage = 'proposal' }: Props = $props();
</script>

<div class="space-y-6">
    <!-- PROPOSAL SECTION -->
    <div>
        <h3 class="text-lg font-semibold mb-4">Identitas Usulan (Proposal)</h3>
        <dl class="space-y-4">
            <div>
                <dt class="text-sm font-medium text-muted-foreground">Judul</dt>
                <dd class="text-lg font-semibold">{detail?.title}</dd>
            </div>

            <div>
                <dt class="text-sm font-medium text-muted-foreground">Ketua Pelaksana</dt>
                <dd class="text-base font-semibold">{detail?.leader_name}</dd>
            </div>

            <div>
                <dt class="text-sm font-medium text-muted-foreground">Program Studi</dt>
                <dd class="text-base font-semibold">{detail?.study_program?.name || '-'}</dd>
            </div>

            <div>
                <dt class="text-sm font-medium text-muted-foreground">Anggota Tim</dt>
                <dd class="mt-1">
                    {#if detail?.members && detail.members.length > 0}
                        <ul class="list-disc list-inside text-sm">
                            {#each detail.members as member, i (i)}
                                <li>{member.name}</li>
                            {/each}
                        </ul>
                    {:else}
                        -
                    {/if}
                </dd>
            </div>

            <div>
                <dt class="text-sm font-medium text-muted-foreground">Target Luaran</dt>
                <dd class="text-base font-semibold">
                    {detail?.target?.title || '-'}
                </dd>
            </div>

            <div>
                <dt class="text-sm font-medium text-muted-foreground">Usulan Biaya</dt>
                <dd class="text-base font-semibold">Rp {new Intl.NumberFormat('id-ID').format(detail?.budget)}</dd>
            </div>
        </dl>

        <div class="mt-6">
            <h4 class="text-sm font-medium mb-2">Dokumen Proposal</h4>
            <div class="flex items-center gap-4">
                <div>
                    <span class="text-sm font-semibold">File Proposal</span>
                </div>
                {#if detail?.proposal_path}
                    <Button href={`/storage/${detail?.proposal_path}`} target="_blank" rel="noopener noreferrer" variant="outline" size="sm"
                        >Unduh File</Button
                    >
                {:else}
                    <span class="text-xs text-muted-foreground">Tidak ada file</span>
                {/if}
            </div>
        </div>
    </div>

    {#if stage === 'progress_report' || stage === 'final_report'}
        <Separator class="my-6" />

        <div class="space-y-6">
            <Alert.Root>
                <InfoIcon class="h-4 w-4" />
                <Alert.Title>Informasi</Alert.Title>
                <Alert.Description>
                    Data proposal di atas tidak dapat diubah karena usulan sudah memasuki tahap
                    {stage === 'progress_report' ? 'laporan kemajuan' : 'laporan akhir'}.
                </Alert.Description>
            </Alert.Root>

            <div>
                <h3 class="text-lg font-semibold mb-4">
                    {stage === 'progress_report' ? 'Identitas Laporan Kemajuan' : 'Identitas Laporan Akhir'}
                </h3>
                <dl class="space-y-4">
                    <div>
                        <dt class="text-sm font-medium text-muted-foreground">Judul Akhir</dt>
                        <dd class="text-lg font-semibold">{detail?.final_title || '-'}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-muted-foreground">Ketua Pelaksana (Akhir)</dt>
                        <dd class="text-base font-semibold">{detail?.final_leader_name || '-'}</dd>
                    </div>
                </dl>
            </div>

            <div>
                <dt class="text-sm font-medium text-muted-foreground">NIDN/NIP</dt>
                <dd class="text-base font-semibold">{detail?.leader_nidn}</dd>
            </div>

            <div>
                <dt class="text-sm font-medium text-muted-foreground">Skema</dt>
                <dd class="text-base font-semibold">
                    {detail?.schema?.title || '-'}
                </dd>
            </div>

            <div>
                <h4 class="text-sm font-medium mb-2">Dokumen Laporan</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    {#if detail?.progress_report_path}
                        <div class="flex items-center gap-4">
                            <div>
                                <span class="text-sm font-semibold">Laporan Kemajuan</span>
                            </div>
                            <Button href={`/storage/${detail?.progress_report_path}`} target="_blank" rel="noopener noreferrer" variant="outline" size="sm">Unduh</Button>
                        </div>
                    {/if}

                    {#if detail?.final_report_path}
                        <div class="flex items-center gap-4">
                            <div>
                                <span class="text-sm font-semibold">Laporan Akhir</span>
                            </div>
                            <Button href={`/storage/${detail?.final_report_path}`} target="_blank" rel="noopener noreferrer" variant="outline" size="sm">Unduh</Button>
                        </div>
                    {/if}

                    <div class="flex items-center gap-4">
                        <div>
                            <span class="text-sm font-semibold">Manuskrip</span>
                        </div>
                        {#if detail?.manuscript_path}
                            <Button href={`/storage/${detail?.manuscript_path}`} target="_blank" rel="noopener noreferrer" variant="outline" size="sm">Unduh</Button>
                        {:else}
                            <span class="text-xs text-muted-foreground">Tidak ada file</span>
                        {/if}
                    </div>
                </div>
            </div>
        </div>
    {/if}
</div>
