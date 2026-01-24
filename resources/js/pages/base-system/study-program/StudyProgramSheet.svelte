<script lang="ts">
    import * as Sheet from '@/components/ui/sheet';
    import { Button } from '@/components/ui/button';
    import { Input } from '@/components/ui/input';
    import { useForm } from '@inertiajs/svelte';
    import { onMount, untrack } from 'svelte';
    import { Field, FieldLabel, FieldError } from '@/components/ui/field';
    import { toast } from 'svelte-sonner';

    let { open = $bindable(false), selectedStudyProgram = null } = $props();

    let form = useForm({
        name: '',
    });

    let isEdit = $derived(!!selectedStudyProgram);

    $effect(() => {
        if (open) {
            untrack(() => {
                if (selectedStudyProgram) {
                    $form.name = selectedStudyProgram.name;
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
            $form.put(`/study-program/${selectedStudyProgram.id}`, {
                onSuccess: () => {
                    open = false;
                    $form.reset();
                    toast.success('Program Studi berhasil diperbarui');
                },
            });
        } else {
            $form.post('/study-program', {
                onSuccess: () => {
                    open = false;
                    $form.reset();
                    toast.success('Program Studi berhasil ditambahkan');
                },
            });
        }
    }
</script>

<Sheet.Root bind:open>
    <Sheet.Content class="sm:max-w-[425px] overflow-y-auto p-6">
        <Sheet.Header>
            <Sheet.Title>{isEdit ? 'Edit Program Studi' : 'Tambah Program Studi'}</Sheet.Title>
            <Sheet.Description>
                {isEdit ? 'Perbarui informasi program studi di sini.' : 'Isi formulir untuk menambahkan program studi baru.'}
            </Sheet.Description>
        </Sheet.Header>

        <form onsubmit={submit} class="space-y-4 py-4">
            <Field>
                <FieldLabel>Nama Program Studi</FieldLabel>
                <Input id="name" bind:value={$form.name} placeholder="Nama Program Studi" />
                {#if $form.errors.name}
                    <FieldError>{$form.errors.name}</FieldError>
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
