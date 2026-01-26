<script lang="ts">
    import * as Sheet from '@/components/ui/sheet';
    import { Button } from '@/components/ui/button';
    import { Input } from '@/components/ui/input';
    import { useForm } from '@inertiajs/svelte';
    import { onMount, untrack } from 'svelte';
    import { Field, FieldLabel, FieldError } from '@/components/ui/field';
    import { toast } from 'svelte-sonner';
    import { Textarea } from '@/components/ui/textarea';

    let { open = $bindable(false), selectedCommunityServiceTarget = null } = $props();

    let form = useForm({
        title: '',
        description: '',
    });

    let isEdit = $derived(!!selectedCommunityServiceTarget);

    $effect(() => {
        if (open) {
            untrack(() => {
                if (selectedCommunityServiceTarget) {
                    $form.title = selectedCommunityServiceTarget.title;
                    $form.description = selectedCommunityServiceTarget.description || '';
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
            $form.put(`/master/community-service-target/${selectedCommunityServiceTarget.id}`, {
                onSuccess: () => {
                    open = false;
                    $form.reset();
                    toast.success('Target PKM berhasil diperbarui');
                },
            });
        } else {
            $form.post('/master/community-service-target', {
                onSuccess: () => {
                    open = false;
                    $form.reset();
                    toast.success('Target PKM berhasil ditambahkan');
                },
            });
        }
    }
</script>

<Sheet.Root bind:open>
    <Sheet.Content class="sm:max-w-[500px] overflow-y-auto p-0 gap-0">
        <Sheet.Header class="p-6 pb-2">
            <Sheet.Title>{isEdit ? 'Edit Target PKM' : 'Tambah Target PKM'}</Sheet.Title>
            <Sheet.Description>
                {isEdit ? 'Perbarui informasi target PKM di sini.' : 'Isi formulir untuk menambahkan target PKM baru.'}
            </Sheet.Description>
        </Sheet.Header>

        <form onsubmit={submit} class="space-y-4 p-6 pt-2">
            <Field>
                <FieldLabel>Judul</FieldLabel>
                <Input id="title" bind:value={$form.title} placeholder="Judul Target PKM" />
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
