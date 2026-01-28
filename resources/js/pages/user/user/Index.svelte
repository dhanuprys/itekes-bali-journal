<script lang="ts">
    import AppLayout from '@/layouts/AppLayout.svelte';
    import { type BreadcrumbItem } from '@/types';
    import { Button } from '@/components/ui/button';
    import { Input } from '@/components/ui/input';
    import * as Table from '@/components/ui/table';
    import * as Select from '@/components/ui/select';
    import { Search, Plus, MoreHorizontal } from 'lucide-svelte';
    import Pagination from '@/components/Pagination.svelte';
    import { router } from '@inertiajs/svelte';
    import { debounce } from 'lodash';
    import * as DropdownMenu from '@/components/ui/dropdown-menu';
    import UserSheet from './UserSheet.svelte';
    import Heading from '@/components/Heading.svelte';
    import * as AlertDialog from '@/components/ui/alert-dialog';
    import { untrack } from 'svelte';
    import { toast } from 'svelte-sonner';

    let { users, filters, roles = [] } = $props();

    // Initialize search from filters, ignoring future filter updates for this local state
    let search = $state(untrack(() => filters.search || ''));
    let roleFilter = $state(untrack(() => filters.role || ''));
    let sheetOpen = $state(false);
    let selectedUser: any = $state(null);
    let deleteDialogOpen = $state(false);
    let userToDelete: any = $state(null);

    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: 'Pengguna',
            href: '/users',
        },
    ];

    const handleSearch = debounce((value: string) => {
        router.get(
            '/users',
            { search: value, role: roleFilter },
            {
                preserveState: true,
                replace: true,
                preserveScroll: true,
            },
        );
    }, 300);

    function handleRoleChange(value: string) {
        roleFilter = value;
        router.get(
            '/users',
            { search: search, role: value },
            {
                preserveState: true,
                replace: true,
                preserveScroll: true,
            },
        );
    }

    function openCreate() {
        selectedUser = null;
        sheetOpen = true;
    }

    function openEdit(user: any) {
        selectedUser = user;
        sheetOpen = true;
    }

    function openDeleteDialog(user: any) {
        userToDelete = user;
        deleteDialogOpen = true;
    }

    function confirmDelete() {
        if (userToDelete) {
            router.delete(`/users/${userToDelete.id}`, {
                onFinish: () => {
                    deleteDialogOpen = false;
                    userToDelete = null;
                    toast.success('Pengguna berhasil dihapus');
                },
            });
        }
    }
    import LayoutComposer from '@/layouts/LayoutComposer.svelte';
</script>

<svelte:head>
    <title>Pengguna</title>
</svelte:head>

<AppLayout {breadcrumbs}>
    <LayoutComposer>
        {#snippet header()}
            <Heading title="Pengguna" description="Kelola data pengguna sistem" />
        {/snippet}

        {#snippet actions()}
            <Button onclick={openCreate}>
                <Plus class="mr-2 h-4 w-4" />
                Tambah Pengguna
            </Button>
        {/snippet}

        {#snippet filters()}
            <div class="relative w-full max-w-sm">
                <Search class="absolute left-2 top-2.5 h-4 w-4 text-muted-foreground" />
                <Input placeholder="Cari pengguna..." class="pl-8" bind:value={search} oninput={(e) => handleSearch(e.currentTarget.value)} />
            </div>
            <div class="w-[200px]">
                <Select.Root type="single" value={roleFilter} onValueChange={handleRoleChange}>
                    <Select.Trigger>
                        {roles.find((r) => r.value === roleFilter)?.label || 'Semua Role'}
                    </Select.Trigger>
                    <Select.Content>
                        <Select.Item value="" label="Semua Role">Semua Role</Select.Item>
                        {#each roles as role (role.value)}
                            <Select.Item value={role.value} label={role.label}>{role.label}</Select.Item>
                        {/each}
                    </Select.Content>
                </Select.Root>
            </div>
        {/snippet}

        <div class="rounded-md border bg-card">
            <Table.Root>
                <Table.Header>
                    <Table.Row>
                        <Table.Head class="w-[50px]">No</Table.Head>
                        <Table.Head>Nama</Table.Head>
                        <Table.Head>Username</Table.Head>
                        <Table.Head>Email</Table.Head>
                        <Table.Head>Roles</Table.Head>
                        <Table.Head>Dibuat</Table.Head>
                        <Table.Head class="text-right">Aksi</Table.Head>
                    </Table.Row>
                </Table.Header>
                <Table.Body>
                    {#if users.data.length === 0}
                        <Table.Row>
                            <Table.Cell colspan={7} class="text-center h-24 text-muted-foreground">Tidak ada data pengguna.</Table.Cell>
                        </Table.Row>
                    {:else}
                        {#each users.data as user, i (user.id)}
                            <Table.Row>
                                <Table.Cell>{(users.current_page - 1) * users.per_page + i + 1}</Table.Cell>
                                <Table.Cell class="font-medium">{user.name}</Table.Cell>
                                <Table.Cell>{user.username || '-'}</Table.Cell>
                                <Table.Cell>{user.email}</Table.Cell>
                                <Table.Cell>
                                    <div class="flex flex-wrap gap-1">
                                        {#each user.roles as role (role.name)}
                                            <span
                                                class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 border-transparent bg-secondary text-secondary-foreground hover:bg-secondary/80"
                                            >
                                                {role.name}
                                            </span>
                                        {/each}
                                    </div>
                                </Table.Cell>
                                <Table.Cell>{new Date(user.created_at).toLocaleDateString()}</Table.Cell>
                                <Table.Cell class="text-right">
                                    <DropdownMenu.Root>
                                        <DropdownMenu.Trigger
                                            class="inline-flex h-8 w-8 items-center justify-center rounded-md border border-input bg-background text-sm font-medium hover:bg-accent hover:text-accent-foreground"
                                        >
                                            <span class="sr-only">Open menu</span>
                                            <MoreHorizontal class="h-4 w-4" />
                                        </DropdownMenu.Trigger>
                                        <DropdownMenu.Content align="end">
                                            <DropdownMenu.Label>Aksi</DropdownMenu.Label>
                                            <DropdownMenu.Item onclick={() => router.visit(`/users/${user.id}`)}>Detail</DropdownMenu.Item>
                                            <DropdownMenu.Item onclick={() => openEdit(user)}>Edit</DropdownMenu.Item>
                                            <DropdownMenu.Separator />
                                            <DropdownMenu.Item onclick={() => openDeleteDialog(user)} class="text-destructive"
                                                >Hapus</DropdownMenu.Item
                                            >
                                        </DropdownMenu.Content>
                                    </DropdownMenu.Root>
                                </Table.Cell>
                            </Table.Row>
                        {/each}
                    {/if}
                </Table.Body>
            </Table.Root>
        </div>

        <div class="mt-4">
            <Pagination links={users.links} meta={users} />
        </div>

        <UserSheet bind:open={sheetOpen} {selectedUser} {roles} />

        <AlertDialog.Root bind:open={deleteDialogOpen}>
            <AlertDialog.Content>
                <AlertDialog.Header>
                    <AlertDialog.Title>Apakah anda yakin?</AlertDialog.Title>
                    <AlertDialog.Description>
                        Aksi ini tidak dapat dibatalkan. Ini akan menghapus pengguna <strong>{userToDelete?.name}</strong> secara permanen.
                    </AlertDialog.Description>
                </AlertDialog.Header>
                <AlertDialog.Footer>
                    <AlertDialog.Cancel>Batal</AlertDialog.Cancel>
                    <AlertDialog.Action class="bg-destructive text-destructive-foreground hover:bg-destructive/90" onclick={confirmDelete}
                        >Hapus</AlertDialog.Action
                    >
                </AlertDialog.Footer>
            </AlertDialog.Content>
        </AlertDialog.Root>
    </LayoutComposer>
</AppLayout>
