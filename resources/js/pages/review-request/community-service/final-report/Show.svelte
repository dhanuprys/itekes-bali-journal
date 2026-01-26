<script lang="ts">
    import LayoutComposer from '@/layouts/LayoutComposer.svelte';
    import AppLayout from '@/layouts/AppLayout.svelte';
    import { type BreadcrumbItem } from '@/types';
    import Heading from '@/components/Heading.svelte';
    import { Button } from '@/components/ui/button';
    import * as Card from '@/components/ui/card';
    import { Badge } from '@/components/ui/badge';
    import { Separator } from '@/components/ui/separator';
    import {
        FileTextIcon,
        UserIcon,
        UsersIcon,
        BookOpenIcon,
        TargetIcon,
        BanknoteIcon,
        CalendarIcon,
        ClockIcon,
        CheckCircleIcon,
        AlertCircleIcon,
    } from 'lucide-svelte';

    let { submission } = $props();
    let detail = $derived(submission.latest_detail);
    let members = $derived(detail.members || []);

    const breadcrumbs: BreadcrumbItem[] = [
        { title: 'Permintaan Review', href: '#' },
        { title: 'Pengabdian Masyarakat', href: '#' },
        { title: 'Laporan Akhir', href: route('apply.community_service.final_report.index') },
        { title: 'Detail', href: '#' },
    ];

    function getStatusConfig(status: string) {
        switch (status) {
            case 'approved':
                return { color: 'bg-green-500/10 text-green-600 hover:bg-green-500/20', label: 'Disetujui', icon: CheckCircleIcon };
            default:
                return {
                    color: 'bg-muted text-muted-foreground',
                    label: status.replace('_', ' ').toUpperCase(),
                    icon: FileTextIcon,
                };
        }
    }

    let statusConfig = $derived(getStatusConfig(submission.status));
</script>

<svelte:head>
    <title>Detail Laporan Akhir - {detail?.final_title || 'Laporan'}</title>
</svelte:head>

<AppLayout {breadcrumbs}>
    <LayoutComposer>
        {#snippet header()}
            <div class="flex items-center justify-between">
                <Heading title="Detail Laporan Akhir" description="Informasi lengkap laporan akhir pengabdian." />
                <Badge variant="default" class="text-sm px-3 py-1">DISETUJUI</Badge>
            </div>
        {/snippet}

        {#snippet children()}
            <div class="space-y-6">
                <!-- Identitas Laporan -->
                <Card.Root>
                    <Card.Header>
                        <Card.Title>Identitas Usulan</Card.Title>
                    </Card.Header>
                    <Card.Content>
                        <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div class="sm:col-span-2">
                                <dt class="text-sm font-medium text-muted-foreground">Judul Akhir</dt>
                                <dd class="text-lg font-semibold">{detail?.final_title || detail?.title}</dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-muted-foreground">Ketua Pelaksana</dt>
                                <dd class="text-base font-semibold">{detail?.final_leader_name || detail?.leader_name}</dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-muted-foreground">NIDN/NIP</dt>
                                <dd class="text-base font-semibold">{detail?.leader_nidn}</dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-muted-foreground">Program Studi</dt>
                                <dd class="text-base font-semibold">{detail?.study_program?.name || '-'}</dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-muted-foreground">Skema Pengabdian</dt>
                                <dd class="text-base font-semibold">{detail?.schema?.name || '-'}</dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-muted-foreground">Target Luaran</dt>
                                <dd class="text-base font-semibold">{detail?.target?.name || '-'}</dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-muted-foreground">Anggaran Disetujui</dt>
                                <dd class="text-base font-semibold">
                                    {new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(detail?.budget || 0)}
                                </dd>
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
                        </dl>
                    </Card.Content>
                </Card.Root>

                <!-- Dokumen -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <Card.Root>
                        <Card.Header>
                            <Card.Title>Laporan Akhir</Card.Title>
                        </Card.Header>
                        <Card.Content>
                            {#if detail?.final_report_path}
                                <div class="flex items-center gap-3 p-3 border rounded-md">
                                    <FileTextIcon class="h-8 w-8 text-primary" />
                                    <div class="flex-1 overflow-hidden">
                                        <p class="text-sm font-medium truncate">File Laporan Akhir</p>
                                    </div>
                                    <Button variant="outline" size="sm" href={`/storage/${detail.final_report_path}`} target="_blank">Unduh</Button>
                                </div>
                            {:else}
                                <p class="text-muted-foreground text-sm">File tidak tersedia.</p>
                            {/if}
                        </Card.Content>
                    </Card.Root>

                    <Card.Root>
                        <Card.Header>
                            <Card.Title>Manuskrip Publikasi</Card.Title>
                        </Card.Header>
                        <Card.Content>
                            {#if detail?.manuscript_path}
                                <div class="flex items-center gap-3 p-3 border rounded-md">
                                    <FileTextIcon class="h-8 w-8 text-primary" />
                                    <div class="flex-1 overflow-hidden">
                                        <p class="text-sm font-medium truncate">File Manuskrip</p>
                                    </div>
                                    <Button variant="outline" size="sm" href={`/storage/${detail.manuscript_path}`} target="_blank">Unduh</Button>
                                </div>
                            {:else}
                                <p class="text-muted-foreground text-sm">File tidak tersedia.</p>
                            {/if}
                        </Card.Content>
                    </Card.Root>
                </div>
            </div>
        {/snippet}
    </LayoutComposer>
</AppLayout>
