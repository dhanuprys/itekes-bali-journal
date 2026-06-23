<script lang="ts">
    import LayoutComposer from '@/layouts/LayoutComposer.svelte';
    import AppLayout from '@/layouts/AppLayout.svelte';
    import { type BreadcrumbItem } from '@/types';
    import Heading from '@/components/Heading.svelte';
    import { Badge } from '@/components/ui/badge';
    import * as Card from '@/components/ui/card';
    import ReviewerSplitLayout from '@/components/reviewer/ReviewerSplitLayout.svelte';
    import ReviewerChatPanel from '@/components/reviewer/ReviewerChatPanel.svelte';
    import { FileTextIcon, ClockIcon, CheckCircleIcon, AlertCircleIcon, DownloadIcon, XCircleIcon } from 'lucide-svelte';
    import { page } from '@inertiajs/svelte';

    let { submission, comments } = $props();
    const currentUser = $derived($page.props.auth.user);
    
    let detail = $derived(submission?.latest_detail);
    let files = $derived(detail?.files ?? []);
    
    let proposalReviews = $derived(submission.proposal_reviews || []);
    const currentUserReview = $derived(proposalReviews.find((r: any) => r.user_id === currentUser.id));

    let canReview = $derived(submission?.status === 'need_review' && !currentUserReview);

    const breadcrumbs: BreadcrumbItem[] = [
        { title: 'Reviewer Area', href: '#' },
        { title: 'Etik', href: route('review.ethics.index') },
        { title: 'Review Proposal', href: '#' },
    ];

    function getStatusConfig(status: string) {
        switch (status) {
            case 'approved':
                return { color: 'bg-green-500/10 text-green-600 hover:bg-green-500/20 border-green-200', label: 'Disetujui', icon: CheckCircleIcon };
            case 'rejected':
                return { color: 'bg-red-500/10 text-red-600 hover:bg-red-500/20 border-red-200', label: 'Ditolak', icon: AlertCircleIcon };
            case 'revision_needed':
                return { color: 'bg-orange-500/10 text-orange-600 hover:bg-orange-500/20 border-orange-200', label: 'Perlu Revisi', icon: AlertCircleIcon };
            case 'need_review':
                return { color: 'bg-blue-500/10 text-blue-600 hover:bg-blue-500/20 border-blue-200', label: 'Menunggu Review', icon: ClockIcon };
            default:
                return { color: 'bg-muted text-muted-foreground', label: status.replace('_', ' ').toUpperCase(), icon: FileTextIcon };
        }
    }

    function getCategoryLabel(category: string) {
        return category === 'clinical' ? 'Etik Klinik / Uji Coba Hewan' : 'Etik Non Klinis';
    }

    let statusConfig = $derived(getStatusConfig(submission.status));
</script>

<svelte:head>
    <title>Review Pengajuan Etik</title>
</svelte:head>

<AppLayout {breadcrumbs}>
    <LayoutComposer>
        {#snippet header()}
            <div class="flex items-center justify-between">
                <Heading title="Review Pengajuan Etik" description="Tinjau dokumen pengajuan ethical clearance berikut." />
            </div>
        {/snippet}

        {#snippet actions()}
            <Badge variant="outline" class={'px-3 py-1 gap-2 flex items-center ' + statusConfig.color}>
                <statusConfig.icon class="h-4 w-4" />
                {statusConfig.label}
            </Badge>
        {/snippet}

        <ReviewerSplitLayout>
            {#snippet details()}
                <!-- Submission Info -->
                <div class="space-y-6">
                    <div>
                        <h3 class="text-lg font-semibold mb-3">Informasi Pengajuan</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-muted-foreground">Pengaju</p>
                                <p class="font-medium">{submission.user?.name ?? '-'}</p>
                            </div>
                            <div>
                                <p class="text-sm text-muted-foreground">Kategori</p>
                                <p class="font-medium">{getCategoryLabel(submission.category)}</p>
                            </div>
                            <div>
                                <p class="text-sm text-muted-foreground">Tanggal Pengajuan</p>
                                <p class="font-medium">{new Date(submission.created_at).toLocaleDateString('id-ID', { year: 'numeric', month: 'long', day: 'numeric' })}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Info -->
                    <div>
                        <h3 class="text-lg font-semibold mb-3">Informasi Pemohon & Pembayaran</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-muted-foreground">Status Pemohon</p>
                                <p class="font-medium">{submission.is_student ? 'Mahasiswa ITEKES Bali' : 'Publik / Umum'}</p>
                            </div>
                            {#if submission.is_student}
                                <div>
                                    <p class="text-sm text-muted-foreground">NIM</p>
                                    <p class="font-medium">{submission.student_nim ?? '-'}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-muted-foreground">Program Studi</p>
                                    <p class="font-medium">{submission.study_program?.name ?? '-'}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-muted-foreground">Nama Wali</p>
                                    <p class="font-medium">{submission.wali_name ?? '-'}</p>
                                </div>
                            {/if}
                            <div class="md:col-span-2 mt-2">
                                <p class="text-sm text-muted-foreground mb-1">Bukti Transfer</p>
                                {#if submission.payment_proof_path}
                                    <a
                                        href={'/storage/' + submission.payment_proof_path}
                                        target="_blank"
                                        download={`Bukti_Pembayaran_Etik_${submission.user?.name}.png`}
                                        class="inline-flex items-center gap-1.5 rounded-md border border-input bg-background px-3 py-1.5 text-xs font-medium hover:bg-accent hover:text-accent-foreground transition-colors shrink-0"
                                    >
                                        <FileTextIcon class="h-3.5 w-3.5" />
                                        Lihat Bukti Transfer
                                    </a>
                                {:else}
                                    <p class="text-sm italic text-muted-foreground">Tidak ada bukti transfer.</p>
                                {/if}
                            </div>
                        </div>
                    </div>

                    <!-- Uploaded Documents -->
                    <div>
                        <h3 class="text-lg font-semibold mb-3">Dokumen yang Diunggah</h3>
                        <div class="space-y-2">
                            {#each files as file (file.id)}
                                <div class="flex items-center justify-between border rounded-lg p-3">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <FileTextIcon class="h-5 w-5 text-muted-foreground shrink-0" />
                                        <div class="min-w-0">
                                            <p class="font-medium text-sm truncate">{file.original_name || file.template_key}</p>
                                            <p class="text-xs text-muted-foreground capitalize">{file.template_key.replace(/_/g, ' ')}</p>
                                        </div>
                                    </div>
                                    <a
                                        href={'/storage/' + file.file_path}
                                        target="_blank"
                                        download={file.original_name || file.template_key}
                                        class="inline-flex items-center gap-1.5 rounded-md bg-primary/10 px-3 py-1.5 text-xs font-medium text-primary hover:bg-primary/20 transition-colors shrink-0"
                                    >
                                        <DownloadIcon class="h-3.5 w-3.5" />
                                        Unduh
                                    </a>
                                </div>
                            {/each}
                        </div>
                    </div>

                    <!-- Status Reviewer -->
                    <div>
                        <h3 class="text-lg font-semibold mb-3">Status Reviewer</h3>
                        <div class="space-y-2">
                            {#each submission.reviewers as reviewer}
                                {@const revVerif = proposalReviews.find((v: any) => v.user_id === reviewer.user_id)}
                                <div class="flex items-start justify-between p-3 rounded-lg border bg-muted/20">
                                    <div class="space-y-1">
                                        <span class="text-sm font-medium block">
                                            {reviewer.user?.name}
                                            {#if reviewer.user_id === currentUser.id}
                                                <span class="text-xs text-muted-foreground ml-1">(Anda)</span>
                                            {/if}
                                        </span>
                                        {#if revVerif}
                                            {#if revVerif.status === 'approved'}
                                                <Badge variant="outline" class="text-green-600 border-green-200 bg-green-50">Disetujui</Badge>
                                            {:else if revVerif.status === 'revision_needed'}
                                                <Badge variant="outline" class="text-orange-500 border-orange-200 bg-orange-50">Perlu Revisi</Badge>
                                            {:else}
                                                <Badge variant="destructive">Ditolak</Badge>
                                            {/if}
                                            {#if revVerif.notes}
                                                <p class="text-xs text-muted-foreground mt-2 italic border-l-2 pl-2">"{revVerif.notes}"</p>
                                            {/if}
                                        {:else}
                                            <Badge variant="outline" class="text-blue-500 border-blue-200 bg-blue-50">Menunggu</Badge>
                                        {/if}
                                    </div>
                                    <div>
                                        {#if revVerif}
                                            {#if revVerif.status === 'approved'}
                                                <CheckCircleIcon class="h-5 w-5 text-green-500" />
                                            {:else if revVerif.status === 'revision_needed'}
                                                <AlertCircleIcon class="h-5 w-5 text-orange-400" />
                                            {:else}
                                                <XCircleIcon class="h-5 w-5 text-red-500" />
                                            {/if}
                                        {:else}
                                            <ClockIcon class="h-5 w-5 text-blue-400" />
                                        {/if}
                                    </div>
                                </div>
                            {/each}
                        </div>
                    </div>
                </div>
            {/snippet}

            {#snippet actions()}
                <ReviewerChatPanel
                    {comments}
                    {canReview}
                    commentSubmitRoute={route('review.ethics.proposal.comment', submission.id)}
                    actionSubmitRoute={route('review.ethics.proposal.change-state', submission.id)}
                />
            {/snippet}
        </ReviewerSplitLayout>
    </LayoutComposer>
</AppLayout>
