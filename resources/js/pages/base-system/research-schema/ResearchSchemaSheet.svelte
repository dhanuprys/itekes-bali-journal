<script lang="ts">
    import * as Sheet from '@/components/ui/sheet';
    import { Button } from '@/components/ui/button';
    import { Input } from '@/components/ui/input';
    import { useForm } from '@inertiajs/svelte';
    import { onMount, untrack } from 'svelte';
    import { Field, FieldLabel, FieldError } from '@/components/ui/field';
    import { toast } from 'svelte-sonner';
    import { Textarea } from '@/components/ui/textarea';

    let { open = $bindable(false), selectedResearchSchema = null } = $props();

    let form = useForm({
        title: '',
        description: '',
    });

    let isEdit = $derived(!!selectedResearchSchema);

    $effect(() => {
        if (open) {
            untrack(() => {
                if (selectedResearchSchema) {
                    $form.title = selectedResearchSchema.title;
                    $form.description = selectedResearchSchema.description || '';
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
            $form.put(`/master/research-schema/${selectedResearchSchema.id}`, {
                onSuccess: () => {
                    open = false;
                    $form.reset();
                    toast.success('Skema Riset berhasil diperbarui');
                },
            });
        } else {
            $form.post('/master/research-schema', {
                onSuccess: () => {
                    open = false;
                    $form.reset();
                    toast.success('Skema Riset berhasil ditambahkan');
                },
            });
        }
    }
</script>

<Sheet.Root bind:open>
    <Sheet.Content class="sm:max-w-[500px] overflow-y-auto p-0 gap-0">
        <Sheet.Header class="p-6 pb-2">
            <Sheet.Title>{isEdit ? 'Edit Skema Riset' : 'Tambah Skema Riset'}</Sheet.Title>
            <Sheet.Description>
                {isEdit ? 'Perbarui informasi skema riset di sini.' : 'Isi formulir untuk menambahkan skema riset baru.'}
            </Sheet.Description>
        </Sheet.Header>

        <form onsubmit={submit} class="space-y-4 p-6 pt-2">
            <Field>
                <FieldLabel>Judul</FieldLabel>
                <Input id="title" bind:value={$form.title} placeholder="Judul Skema Riset" />
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
