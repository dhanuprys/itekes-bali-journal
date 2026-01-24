<script lang="ts">
    import { Button } from '@/components/ui/button';
    import * as Sheet from '@/components/ui/sheet';
    import { MessageSquareIcon } from 'lucide-svelte';
    import { formatDistanceToNow } from 'date-fns';
    import { id as idLocale } from 'date-fns/locale';

    let { comments } = $props();
    let isCommentOpen = $state(false);
</script>

{#if comments && comments.length > 0}
    <Sheet.Root bind:open={isCommentOpen}>
        <Sheet.Trigger>
            <Button variant="outline">
                <MessageSquareIcon class="mr-2 h-4 w-4" />
                Lihat Komentar Reviewer
            </Button>
        </Sheet.Trigger>
        <Sheet.Content side="right" class="w-[400px] sm:w-[540px] overflow-y-auto">
            <Sheet.Header>
                <Sheet.Title>Komentar Reviewer</Sheet.Title>
                <Sheet.Description>Berikut adalah catatan dari reviewer untuk revisi ini.</Sheet.Description>
            </Sheet.Header>
            <div class="mt-6 space-y-6">
                {#each comments as comment}
                    <div class="flex flex-col gap-2 p-4 border rounded-lg bg-muted/30">
                        <div class="flex items-center justify-between">
                            <span class="font-semibold text-sm">{comment.user.name}</span>
                            <span class="text-xs text-muted-foreground">
                                {formatDistanceToNow(new Date(comment.created_at), { addSuffix: true, locale: idLocale })}
                            </span>
                        </div>
                        <p class="text-sm text-foreground/90 whitespace-pre-wrap">{comment.content}</p>
                    </div>
                {/each}
            </div>
        </Sheet.Content>
    </Sheet.Root>
{/if}

{#snippet triggerLink()}
    <button onclick={() => (isCommentOpen = true)} class="underline font-medium hover:text-foreground">cek komentar reviewer</button>
{/snippet}

<div class="hidden">
    <!-- Expose trigger link for external usage if needed via slot/binding workaround, 
         but for now we export nothing complex. The consumer just uses the component. 
         Wait, needed for the Alert message link? 
         Optimisation: Check if we can export the function to open. -->
</div>
