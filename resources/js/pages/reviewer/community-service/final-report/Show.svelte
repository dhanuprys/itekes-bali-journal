<script lang="ts">
    import LayoutComposer from '@/layouts/LayoutComposer.svelte';
    import AppLayout from '@/layouts/AppLayout.svelte';
    import { type BreadcrumbItem } from '@/types';
    import Heading from '@/components/Heading.svelte';
    import { Button } from '@/components/ui/button';
    import { Separator } from '@/components/ui/separator';
    import { FileTextIcon, ClockIcon, CheckCircleIcon, AlertCircleIcon } from 'lucide-svelte';
    import ReviewerSplitLayout from '@/components/reviewer/ReviewerSplitLayout.svelte';
    import ReviewerChatPanel from '@/components/reviewer/ReviewerChatPanel.svelte';
    import { Badge } from '@/components/ui/badge';

    let { submission, comments } = $props();
    let detail = $derived(submission.latest_detail);
    let canReview = $derived(submission.status === 'need_review');

    const breadcrumbs: BreadcrumbItem[] = [
        { title: 'Reviewer Area', href: '#' },
        { title: 'Pengabdian Masyarakat', href: route('review.community_service.index') },
        { title: 'Review Laporan Akhir', href: '#' },
    ];

    function getStatusConfig(status: string) {
        switch (status) {
            case 'approved':
                return { color: 'bg-green-500/10 text-green-600 hover:bg-green-500/20 border-green-200', label: 'Disetujui', icon: CheckCircleIcon };
            case 'rejected':
                return { color: 'bg-red-500/10 text-red-600 hover:bg-red-500/20 border-red-200', label: 'Ditolak', icon: AlertCircleIcon };
            case 'revision_needed':
                return {
                    color: 'bg-orange-500/10 text-orange-600 hover:bg-orange-500/20 border-orange-200',
                    label: 'Perlu Revisi',
                    icon: AlertCircleIcon,
                };
            case 'need_review':
                return { color: 'bg-blue-500/10 text-blue-600 hover:bg-blue-500/20 border-blue-200', label: 'Menunggu Review', icon: ClockIcon };
            default:
                return { color: 'bg-muted text-muted-foreground', label: status.replace('_', ' ').toUpperCase(), icon: FileTextIcon };
        }
    }

    let statusConfig = $derived(getStatusConfig(submission.status));
</script>

<svelte:head>
    <title>Review Laporan Akhir</title>
</svelte:head>

<AppLayout {breadcrumbs}>
    <LayoutComposer>
        {#snippet header()}
            <Heading title="Review Laporan Akhir" description="Tinjau laporan akhir pengabdian berikut." />
        {/snippet}

        {#snippet actions()}
            <Badge variant="outline" class={'px-3 py-1 gap-2 flex items-center ' + statusConfig.color}>
                <statusConfig.icon class="h-4 w-4" />
                {statusConfig.label}
            </Badge>
        {/snippet}

        {#snippet children()}
            <ReviewerSplitLayout>
                {#snippet details()}
                    <h3 class="text-lg font-semibold mb-4">Identitas Laporan</h3>
                    <div class="space-y-6">
                        <dl class="space-y-4">
                            <div>
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

                            <div>
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

                        <Separator class="my-6" />

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <h4 class="text-sm font-medium mb-2">Laporan Akhir</h4>
                                {#if detail?.final_report_path}
                                    <div class="flex items-center gap-3">
                                        <FileTextIcon class="h-6 w-6 text-primary" />
                                        <div class="flex-1 overflow-hidden">
                                            <p class="text-sm truncate">File Laporan</p>
                                        </div>
                                        <Button href={`/storage/${detail?.final_report_path}`} target="_blank" variant="outline" size="sm"
                                            >Unduh</Button
                                        >
                                    </div>
                                {:else}
                                    <p class="text-sm text-muted-foreground">Tidak tersedia</p>
                                {/if}
                            </div>
                            <div>
                                <h4 class="text-sm font-medium mb-2">Manuskrip</h4>
                                {#if detail?.manuscript_path}
                                    <div class="flex items-center gap-3">
                                        <FileTextIcon class="h-6 w-6 text-primary" />
                                        <div class="flex-1 overflow-hidden">
                                            <p class="text-sm truncate">File Manuskrip</p>
                                        </div>
                                        <Button href={`/storage/${detail?.manuscript_path}`} target="_blank" variant="outline" size="sm">Unduh</Button
                                        >
                                    </div>
                                {:else}
                                    <p class="text-sm text-muted-foreground">Tidak tersedia</p>
                                {/if}
                            </div>
                        </div>
                    </div>
                {/snippet}

                {#snippet actions()}
                    <ReviewerChatPanel
                        {comments}
                        {canReview}
                        commentSubmitRoute={route('review.community_service.final_report.comment', submission.id)}
                        actionSubmitRoute={route('review.community_service.final_report.change-state', submission.id)}
                    />
                {/snippet}
            </ReviewerSplitLayout>
        {/snippet}
    </LayoutComposer>
</AppLayout>
