<script lang="ts">
    import * as Sheet from '@/components/ui/sheet';
    import { Button } from '@/components/ui/button';
    import { Input } from '@/components/ui/input';
    import { useForm } from '@inertiajs/svelte';
    import { onMount, untrack } from 'svelte';
    import { Field, FieldLabel, FieldError } from '@/components/ui/field';
    import { toast } from 'svelte-sonner';
    import Label from '@/components/ui/label/label.svelte';

    let { open = $bindable(false), selectedRole = null, permissions = [] } = $props();

    let form = useForm({
        name: '',
        permissions: [] as string[],
    });

    let isEdit = $derived(!!selectedRole);

    $effect(() => {
        if (open) {
            untrack(() => {
                if (selectedRole) {
                    $form.name = selectedRole.name;
                    $form.permissions = selectedRole.permissions ? selectedRole.permissions.map((p: any) => p.name) : [];
                } else {
                    $form.reset();
                    $form.permissions = [];
                }
                $form.clearErrors();
            });
        }
    });

    function submit(e: Event) {
        e.preventDefault();
        if (isEdit) {
            $form.put(`/roles/${selectedRole.id}`, {
                onSuccess: () => {
                    open = false;
                    $form.reset();
                    toast.success('Role berhasil diperbarui');
                },
            });
        } else {
            $form.post('/roles', {
                onSuccess: () => {
                    open = false;
                    $form.reset();
                    toast.success('Role berhasil ditambahkan');
                },
            });
        }
    }
</script>

<Sheet.Root bind:open>
    <Sheet.Content class="sm:max-w-[500px] overflow-y-auto p-0 gap-0">
        <Sheet.Header class="p-6 pb-2">
            <Sheet.Title>{isEdit ? 'Edit Role' : 'Tambah Role'}</Sheet.Title>
            <Sheet.Description>
                {isEdit ? 'Perbarui informasi role di sini.' : 'Isi formulir untuk menambahkan role baru.'}
            </Sheet.Description>
        </Sheet.Header>

        <form onsubmit={submit} class="space-y-4 p-6 pt-2">
            <Field>
                <FieldLabel>Nama Role</FieldLabel>
                <Input id="name" bind:value={$form.name} placeholder="Nama Role" />
                {#if $form.errors.name}
                    <FieldError>{$form.errors.name}</FieldError>
                {/if}
            </Field>

            <Field>
                <FieldLabel>Permissions</FieldLabel>
                <div class="flex flex-col gap-2 border rounded-md p-3 max-h-[300px] overflow-y-auto">
                    {#each permissions as permission}
                        <label class="flex items-center space-x-2">
                            <input
                                type="checkbox"
                                class="rounded border-gray-300 text-primary shadow-sm focus:ring-primary"
                                value={permission.value}
                                checked={$form.permissions.includes(permission.value)}
                                onchange={(e) => {
                                    if (e.currentTarget.checked) {
                                        $form.permissions = [...$form.permissions, permission.value];
                                    } else {
                                        $form.permissions = $form.permissions.filter((p) => p !== permission.value);
                                    }
                                }}
                            />
                            <span class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70"
                                >{permission.label}</span
                            >
                        </label>
                    {/each}
                </div>
                {#if $form.errors.permissions}
                    <FieldError>{$form.errors.permissions}</FieldError>
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
