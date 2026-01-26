<script lang="ts">
    import { Button } from '@/components/ui/button';
    import { Textarea } from '@/components/ui/textarea';
    import { useForm } from '@inertiajs/svelte';
    import { toast } from 'svelte-sonner';

    let { submitRoute, extraActions } = $props();

    const form = useForm({
        content: '',
    });

    function submit() {
        $form.post(submitRoute, {
            preserveScroll: true,
            onSuccess: () => {
                $form.reset();
                toast.success('Komentar berhasil dikirim.');
            },
            onError: () => {
                toast.error('Gagal mengirim komentar.');
            },
        });
    }
</script>

<form
    onsubmit={(e) => {
        e.preventDefault();
        submit();
    }}
    class="space-y-4"
>
    <Textarea placeholder="Tulis komentar review Anda di sini..." bind:value={$form.content} class="min-h-[80px]" />

    <div class="flex justify-between items-center">
        {#if extraActions}
            <div>
                {@render extraActions()}
            </div>
        {:else}
            <div></div>
        {/if}

        <Button type="submit" disabled={$form.processing} size="sm">
            {#if $form.processing}
                Mengirim...
            {:else}
                Kirim Komentar
            {/if}
        </Button>
    </div>
</form>
