<script lang="ts">
    import { Avatar, AvatarFallback } from '@/components/ui/avatar';
    import { formatDistanceToNow } from 'date-fns';
    import { id as idLocale } from 'date-fns/locale';

    let { comments = [] } = $props();

    function getInitials(name: string) {
        return name
            .split(' ')
            .map((n) => n[0])
            .slice(0, 2)
            .join('')
            .toUpperCase();
    }
</script>

<div class="space-y-6">
    <div class="space-y-6">
        {#if comments.length === 0}
            <p class="text-muted-foreground text-sm py-4">Belum ada komentar.</p>
        {:else}
            {#each comments as comment}
                <div class="flex gap-4">
                    <Avatar class="h-8 w-8">
                        <AvatarFallback>{getInitials(comment.user.name)}</AvatarFallback>
                    </Avatar>
                    <div class="space-y-1">
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-semibold">{comment.user.name}</span>
                            <span class="text-xs text-muted-foreground">
                                {formatDistanceToNow(new Date(comment.created_at), { addSuffix: true, locale: idLocale })}
                            </span>
                        </div>
                        <p class="text-sm text-foreground/90 whitespace-pre-wrap">{comment.content}</p>
                    </div>
                </div>
            {/each}
        {/if}
    </div>
</div>
