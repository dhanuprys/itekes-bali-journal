<script lang="ts">
    import CommentSection from '@/components/reviewer/CommentSection.svelte';
    import CommentForm from '@/components/reviewer/CommentForm.svelte';
    import ReviewActions from '@/components/reviewer/ReviewActions.svelte';
    let { comments = [], commentSubmitRoute, actionSubmitRoute, canReview = false } = $props();
</script>

<div class="flex flex-col h-full bg-background">
    <!-- Status Header -->
    <div class="flex items-center justify-between px-6 py-3 border-b bg-muted/20">
        <span class="text-sm font-medium text-muted-foreground">Komentar</span>
    </div>

    <!-- Scrollable Comments Area -->
    <div class="flex-1 overflow-y-auto p-6 scroll-smooth">
        <CommentSection {comments} />
    </div>

    <!-- Sticky Bottom Input -->
    <div class="border-t bg-background p-6">
        <p class="text-xs text-muted-foreground mb-3 px-1">
            * Anda tetap dapat mengirim komentar diskusi meskipun sudah memberikan keputusan review.
        </p>
        <CommentForm submitRoute={commentSubmitRoute}>
            {#snippet extraActions()}
                <ReviewActions {canReview} submitRoute={actionSubmitRoute} />
            {/snippet}
        </CommentForm>
    </div>
</div>
