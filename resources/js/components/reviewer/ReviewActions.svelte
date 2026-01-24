<script lang="ts">
    import * as Card from '@/components/ui/card';
    import { Button } from '@/components/ui/button';
    import { useForm } from '@inertiajs/svelte';
    import { CheckCircle2Icon, XCircleIcon, AlertTriangleIcon } from 'lucide-svelte';

    let { submitRoute, canReview = false } = $props();

    const form = useForm({
        status: '',
    });

    function submit(status: string) {
        let message = '';
        if (status === 'approved') message = 'Apakah Anda yakin ingin MENYETUJUI usulan ini?';
        if (status === 'revision_needed') message = 'Apakah Anda yakin ingin meminta REVISI?';
        if (status === 'rejected') message = 'Apakah Anda yakin ingin MENOLAK usulan ini?';

        if (!confirm(message)) return;

        form.status = status;
        form.post(submitRoute);
    }
</script>

{#if canReview}
    <Card.Root class="border-t-4 border-t-primary">
        <Card.Header>
            <Card.Title>Keputusan Review</Card.Title>
            <Card.Description>Silakan ambil keputusan berdasarkan hasil review Anda.</Card.Description>
        </Card.Header>
        <Card.Content class="flex flex-wrap gap-4">
            <Button variant="default" onclick={() => submit('approved')} disabled={form.processing} class="bg-green-600 hover:bg-green-700">
                <CheckCircle2Icon class="mr-2 h-4 w-4" />
                Setujui
            </Button>

            <Button
                variant="outline"
                onclick={() => submit('revision_needed')}
                disabled={form.processing}
                class="border-yellow-600 text-yellow-600 hover:bg-yellow-50"
            >
                <AlertTriangleIcon class="mr-2 h-4 w-4" />
                Perlu Revisi
            </Button>

            <Button variant="destructive" onclick={() => submit('rejected')} disabled={form.processing}>
                <XCircleIcon class="mr-2 h-4 w-4" />
                Tolak
            </Button>
        </Card.Content>
    </Card.Root>
{/if}
