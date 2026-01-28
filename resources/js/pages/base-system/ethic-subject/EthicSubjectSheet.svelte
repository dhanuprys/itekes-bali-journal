<script lang="ts">
    import * as Sheet from '@/components/ui/sheet';
    import { Button } from '@/components/ui/button';
    import { Input } from '@/components/ui/input';
    import { useForm } from '@inertiajs/svelte';
    import { untrack } from 'svelte';
    import { Field, FieldLabel, FieldError } from '@/components/ui/field';
    import { toast } from 'svelte-sonner';
    import { Textarea } from '@/components/ui/textarea';

    let { open = $bindable(false), selectedEthicalClearanceSubject = null } = $props();

    let form = useForm({
        title: '',
        description: '',
    });

    let isEdit = $derived(!!selectedEthicalClearanceSubject);

    $effect(() => {
        if (open) {
            untrack(() => {
                if (selectedEthicalClearanceSubject) {
                    $form.title = selectedEthicalClearanceSubject.title;
                    $form.description = selectedEthicalClearanceSubject.description || '';
                } else {
                    $form.reset();
                }
                $form.clearErrors();
            });
        }
    });

    function submit(e: Event) {
        e.preventDefault();
        if (isEdit) {
            $form.put(`/master/ethic-subject/${selectedEthicalClearanceSubject.id}`, {
                onSuccess: () => {
                    open = false;
                    $form.reset();
                    toast.success('Subjek Etik berhasil diperbarui');
                },
            });
        } else {
            $form.post('/master/ethic-subject', {
                onSuccess: () => {
                    open = false;
                    $form.reset();
                    toast.success('Subjek Etik berhasil ditambahkan');
                },
            });
        }
    }
</script>

<Sheet.Root bind:open>
    <Sheet.Content class="sm:max-w-[500px] overflow-y-auto p-0 gap-0">
        <Sheet.Header class="p-6 pb-2">
            <Sheet.Title>{isEdit ? 'Edit Subjek Etik' : 'Tambah Subjek Etik'}</Sheet.Title>
            <Sheet.Description>
                {isEdit ? 'Perbarui informasi subjek etik di sini.' : 'Isi formulir untuk menambahkan subjek etik baru.'}
            </Sheet.Description>
        </Sheet.Header>

        <form onsubmit={submit} class="space-y-4 p-6 pt-2">
            <Field>
                <FieldLabel>Judul</FieldLabel>
                <Input id="title" bind:value={$form.title} placeholder="Judul Subjek Etik" />
                {#if $form.errors.title}
                    <FieldError>{$form.errors.title}</FieldError>
                {/if}
            </Field>

            <Field>
                <FieldLabel>Deskripsi</FieldLabel>
                <Textarea id="description" bind:value={$form.description} placeholder="Deskripsi (Opsional)" />
                {#if $form.errors.description}
                    <FieldError>{$form.errors.description}</FieldError>
                {/if}
            </Field>

            <div class="flex justify-end pt-4">
                <Button type="submit" disabled={$form.processing}>
                    {$form.processing ? 'Menyimpan...' : 'Simpan'}
                </Button>
            </div>
        </form>
    </Sheet.Content>
</Sheet.Root>
