<script lang="ts">
    import * as Sheet from '@/components/ui/sheet';
    import { Button } from '@/components/ui/button';
    import { Input } from '@/components/ui/input';
    import { useForm } from '@inertiajs/svelte';
    import { onMount, untrack } from 'svelte';
    import { Field, FieldLabel, FieldError } from '@/components/ui/field';
    import { toast } from 'svelte-sonner';
    let { open = $bindable(false), selectedUser = null, roles = [] } = $props();

    let form = useForm({
        name: '',
        username: '',
        email: '',
        password: '',
        password_confirmation: '',
        roles: [] as string[],
    });

    let isEdit = $derived(!!selectedUser);

    $effect(() => {
        if (open) {
            untrack(() => {
                if (selectedUser) {
                    // Populate form with user data
                    $form.name = selectedUser.name;
                    $form.username = selectedUser.username;
                    $form.email = selectedUser.email;
                    $form.roles = selectedUser.roles.map((r: any) => r.name);
                    $form.password = '';
                    $form.password_confirmation = '';
                } else {
                    $form.reset();
                    $form.roles = []; // Explicitly clear roles on create
                }
                $form.clearErrors();
            });
        }
    });

    function submit(e: Event) {
        e.preventDefault();
        if (isEdit) {
            $form.put(`/users/${selectedUser.id}`, {
                onSuccess: () => {
                    open = false;
                    $form.reset();
                    toast.success('Pengguna berhasil diperbarui');
                },
            });
        } else {
            $form.post('/users', {
                onSuccess: () => {
                    open = false;
                    $form.reset();
                    toast.success('Pengguna berhasil ditambahkan');
                },
            });
        }
    }
</script>

<Sheet.Root bind:open>
    <Sheet.Content class="sm:max-w-[500px] overflow-y-auto p-6">
        <Sheet.Header>
            <Sheet.Title>{isEdit ? 'Edit Pengguna' : 'Tambah Pengguna'}</Sheet.Title>
            <Sheet.Description>
                {isEdit ? 'Perbarui informasi pengguna di sini.' : 'Isi formulir untuk menambahkan pengguna baru.'}
            </Sheet.Description>
        </Sheet.Header>

        <form onsubmit={submit} class="space-y-4 py-4">
            <Field>
                <FieldLabel>Nama Lengkap</FieldLabel>
                <Input id="name" bind:value={$form.name} placeholder="John Doe" />
                {#if $form.errors.name}
                    <FieldError>{$form.errors.name}</FieldError>
                {/if}
            </Field>

            <Field>
                <FieldLabel>Username</FieldLabel>
                <Input id="username" bind:value={$form.username} placeholder="johndoe" />
                {#if $form.errors.username}
                    <FieldError>{$form.errors.username}</FieldError>
                {/if}
            </Field>

            <Field>
                <FieldLabel>Email</FieldLabel>
                <Input id="email" type="email" bind:value={$form.email} placeholder="john@example.com" />
                {#if $form.errors.email}
                    <FieldError>{$form.errors.email}</FieldError>
                {/if}
            </Field>

            <Field>
                <FieldLabel>Role</FieldLabel>
                <div class="flex flex-col gap-2 border rounded-md p-3">
                    {#each roles as role}
                        <label class="flex items-center space-x-2">
                            <input
                                type="checkbox"
                                class="rounded border-gray-300 text-primary shadow-sm focus:ring-primary"
                                value={role.value}
                                checked={$form.roles.includes(role.value)}
                                onchange={(e) => {
                                    if (e.currentTarget.checked) {
                                        $form.roles = [...$form.roles, role.value];
                                    } else {
                                        $form.roles = $form.roles.filter((r) => r !== role.value);
                                    }
                                }}
                            />
                            <span class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70"
                                >{role.label}</span
                            >
                        </label>
                    {/each}
                </div>
                {#if $form.errors.roles}
                    <FieldError>{$form.errors.roles}</FieldError>
                {/if}
            </Field>

            <Field>
                <FieldLabel>{isEdit ? 'Password (Kosongkan jika tidak ubah)' : 'Password'}</FieldLabel>
                <Input id="password" type="password" bind:value={$form.password} />
                {#if $form.errors.password}
                    <FieldError>{$form.errors.password}</FieldError>
                {/if}
            </Field>

            <Field>
                <FieldLabel>Konfirmasi Password</FieldLabel>
                <Input id="password_confirmation" type="password" bind:value={$form.password_confirmation} />
            </Field>

            <div class="flex justify-end pt-4">
                <Button type="submit" disabled={$form.processing}>
                    {$form.processing ? 'Menyimpan...' : 'Simpan'}
                </Button>
            </div>
        </form>
    </Sheet.Content>
</Sheet.Root>
