<script lang="ts">
    import LayoutComposer from '@/layouts/LayoutComposer.svelte';
    import AppLayout from '@/layouts/AppLayout.svelte';
    import { type BreadcrumbItem } from '@/types';
    import Heading from '@/components/Heading.svelte';
    import { FileTextIcon, ClockIcon, CheckCircleIcon, AlertCircleIcon } from 'lucide-svelte';
    import ReviewerSplitLayout from '@/components/reviewer/ReviewerSplitLayout.svelte';
    import ReviewerChatPanel from '@/components/reviewer/ReviewerChatPanel.svelte';
    import { Badge } from '@/components/ui/badge';
    import SubmissionDetailCard from '@/components/reviewer/SubmissionDetailCard.svelte';

    let { submission, comments } = $props();
    let detail = $derived(submission?.latest_detail);
    let canReview = $derived(submission?.status === 'need_review');

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

    let statusConfig = $derived(getStatusConfig(submission?.status));
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

        <ReviewerSplitLayout>
            {#snippet details()}
                <SubmissionDetailCard {detail} type="community-service" stage="final_report" />
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
    </LayoutComposer>
</AppLayout>
