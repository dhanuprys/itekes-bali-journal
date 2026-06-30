<script lang="ts">
    import { Button } from '@/components/ui/button';
    import { useForm } from '@inertiajs/svelte';
    import { CheckCircle2Icon, XCircleIcon, AlertTriangleIcon, ChevronDownIcon } from 'lucide-svelte';
    import * as AlertDialog from '@/components/ui/alert-dialog';
    import * as DropdownMenu from '@/components/ui/dropdown-menu';
    import { Input } from '@/components/ui/input';
    import { Label } from '@/components/ui/field';
    import { toast } from 'svelte-sonner';

    let { submitRoute, canReview = false } = $props();

    const form = useForm({
        status: '',
        notes: '',
    });

    let dialogOpen = $state(false);
    let selectedStatus = $state('');
    let confirmMessage = $state('');
    let lgtmInput = $state('');

    function openConfirm(status: string) {
        selectedStatus = status;
        lgtmInput = ''; // Reset input
        $form.notes = ''; // Reset notes
        if (status === 'approved') confirmMessage = 'Apakah Anda yakin ingin MENYETUJUI usulan ini?';
        if (status === 'revision_needed') confirmMessage = 'Apakah Anda yakin ingin meminta REVISI?';
        if (status === 'rejected') confirmMessage = 'Apakah Anda yakin ingin MENOLAK usulan ini?';
        dialogOpen = true;
    }

    function confirmAction() {
        $form.status = selectedStatus;
        $form.post(submitRoute, {
            onSuccess: () => {
                toast.success('Keputusan review berhasil disimpan.');
                dialogOpen = false;
            },
            onError: () => {
                toast.error('Gagal menyimpan keputusan review.');
                dialogOpen = false;
            },
        });
    }
</script>

{#if canReview}
    <div class="flex items-center">
        <DropdownMenu.Root>
            <DropdownMenu.Trigger>
                {#snippet child({ props })}
                    <Button variant="outline" size="sm" {...props}>
                        Keputusan
                        <ChevronDownIcon class="ml-2 h-4 w-4" />
                    </Button>
                {/snippet}
            </DropdownMenu.Trigger>
            <DropdownMenu.Content align="end">
                <DropdownMenu.Label>Pilih Keputusan</DropdownMenu.Label>
                <DropdownMenu.Separator />
                <DropdownMenu.Item onclick={() => openConfirm('approved')} class="text-green-600 focus:text-green-700 focus:bg-green-50">
                    <CheckCircle2Icon class="mr-2 h-4 w-4" />
                    Setujui
                </DropdownMenu.Item>
                <DropdownMenu.Item onclick={() => openConfirm('revision_needed')} class="text-yellow-600 focus:text-yellow-700 focus:bg-yellow-50">
                    <AlertTriangleIcon class="mr-2 h-4 w-4" />
                    Perlu Revisi
                </DropdownMenu.Item>
                <DropdownMenu.Item onclick={() => openConfirm('rejected')} class="text-destructive focus:text-destructive focus:bg-destructive/10">
                    <XCircleIcon class="mr-2 h-4 w-4" />
                    Tolak
                </DropdownMenu.Item>
            </DropdownMenu.Content>
        </DropdownMenu.Root>
    </div>

    <AlertDialog.Root bind:open={dialogOpen}>
        <AlertDialog.Content>
            <AlertDialog.Header>
                <AlertDialog.Title>Konfirmasi Keputusan</AlertDialog.Title>
                <AlertDialog.Description class="space-y-4 text-left">
                    <p>{confirmMessage}</p>
                    <div class="space-y-2 mt-4 text-left">
                        <Label class="text-left block text-foreground font-semibold">Catatan (Opsional)</Label>
                        <textarea
                            class="flex min-h-[80px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                            placeholder="Tambahkan catatan jika diperlukan..."
                            bind:value={$form.notes}
                        ></textarea>
                    </div>
                    <div class="space-y-2 mt-4 text-left">
                        <Label class="text-left block text-foreground font-semibold">Ketik "LGTM" untuk mengonfirmasi</Label>
                        <Input type="text" placeholder="LGTM" bind:value={lgtmInput} />
                    </div>
                </AlertDialog.Description>
            </AlertDialog.Header>
            <AlertDialog.Footer>
                <AlertDialog.Cancel>Batal</AlertDialog.Cancel>
                <AlertDialog.Action
                    onclick={confirmAction}
                    disabled={lgtmInput !== 'LGTM'}
                    class={lgtmInput !== 'LGTM' ? 'opacity-50 cursor-not-allowed' : ''}>Ya, Lanjutkan</AlertDialog.Action
                >
            </AlertDialog.Footer>
        </AlertDialog.Content>
    </AlertDialog.Root>
{/if}
