<script lang="ts">
    import * as Card from '@/components/ui/card';
    import { Button } from '@/components/ui/button';
    import { Textarea } from '@/components/ui/textarea';
    import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
    import { useForm } from '@inertiajs/svelte';
    import { formatDistanceToNow } from 'date-fns';
    import { id as idLocale } from 'date-fns/locale';

    let { comments = [], submitRoute } = $props();

    const form = useForm({
        content: '',
    });

    function submit() {
        form.post(submitRoute, {
            preserveScroll: true,
            onSuccess: () => form.reset(),
        });
    }

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
    <Card.Root>
        <Card.Header>
            <Card.Title>Komentar Reviewer</Card.Title>
        </Card.Header>
        <Card.Content>
            <div class="space-y-6 mb-6">
                {#if comments.length === 0}
                    <p class="text-muted-foreground text-sm text-center py-4">Belum ada komentar.</p>
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

            <form
                onsubmit={(e) => {
                    e.preventDefault();
                    submit();
                }}
                class="space-y-4"
            >
                <Textarea placeholder="Tulis komentar review Anda di sini..." bind:value={form.content} class="min-h-[100px]" />

                <div class="flex justify-end">
                    <Button type="submit" disabled={form.processing} size="sm">
                        {#if form.processing}
                            Mengirim...
                        {:else}
                            Kirim Komentar
                        {/if}
                    </Button>
                </div>
            </form>
        </Card.Content>
    </Card.Root>
</div>
