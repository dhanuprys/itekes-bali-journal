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
        { title: 'Penelitian', href: route('review.research.index') },
        { title: 'Review Proposal', href: '#' },
    ];
</script>

<svelte:head>
    <title>Review Proposal Penelitian</title>
</svelte:head>

<AppLayout {breadcrumbs}>
    <LayoutComposer>
        {#snippet header()}
            <div class="flex items-center justify-between">
                <Heading title="Review Proposal" description="Tinjau usulan proposal penelitian berikut." />
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
                    <SubmissionDetailCard {detail} type="research" />
                {/snippet}

                {#snippet actions()}
                    <ReviewerChatPanel
                        {comments}
                        {canReview}
                        commentSubmitRoute={route('review.research.proposal.comment', submission.id)}
                        actionSubmitRoute={route('review.research.proposal.change-state', submission.id)}
                    />
                {/snippet}
            </ReviewerSplitLayout>
        {/snippet}
    </LayoutComposer>
</AppLayout>
