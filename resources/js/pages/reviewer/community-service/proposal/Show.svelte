<script lang="ts">
    import LayoutComposer from '@/layouts/LayoutComposer.svelte';
    import AppLayout from '@/layouts/AppLayout.svelte';
    import { type BreadcrumbItem } from '@/types';
    import Heading from '@/components/Heading.svelte';
    import { Badge } from '@/components/ui/badge';
    import ReviewerSplitLayout from '@/components/reviewer/ReviewerSplitLayout.svelte';
    import SubmissionDetailCard from '@/components/reviewer/SubmissionDetailCard.svelte';
    import ReviewerChatPanel from '@/components/reviewer/ReviewerChatPanel.svelte';

    let { submission, comments } = $props();
    let detail = $derived(submission.latest_detail);
    let canReview = $derived(submission.status === 'need_review');

    const breadcrumbs: BreadcrumbItem[] = [
        { title: 'Reviewer Area', href: '#' },
        { title: 'Pengabdian Masyarakat', href: route('review.community_service.index') },
        { title: 'Review Proposal', href: '#' },
    ];
</script>

<svelte:head>
    <title>Review Proposal Pengabdian</title>
</svelte:head>

<AppLayout {breadcrumbs}>
    <LayoutComposer>
        {#snippet header()}
            <div class="flex items-center justify-between">
                <Heading title="Review Proposal" description="Tinjau usulan proposal pengabdian berikut." />
            </div>
        {/snippet}

        {#snippet actions()}
            <Badge variant={canReview ? 'default' : 'outline'} class="text-sm px-3 py-1">
                {submission.status.replace('_', ' ').toUpperCase()}
            </Badge>
        {/snippet}

        {#snippet children()}
            <ReviewerSplitLayout>
                {#snippet details()}
                    <SubmissionDetailCard {detail} type="community-service" />
                {/snippet}

                {#snippet actions()}
                    <ReviewerChatPanel
                        {comments}
                        {canReview}
                        commentSubmitRoute={route('review.community_service.proposal.comment', submission.id)}
                        actionSubmitRoute={route('review.community_service.proposal.change-state', submission.id)}
                    />
                {/snippet}
            </ReviewerSplitLayout>
        {/snippet}
    </LayoutComposer>
</AppLayout>
