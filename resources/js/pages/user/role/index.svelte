<script lang="ts">
    import LayoutComposer from '@/layouts/layout-composer.svelte';
    import AppLayout from '@/layouts/app-layout.svelte';
    import { type BreadcrumbItem } from '@/types';
    import { Button } from '@/components/ui/button';
    import { Input } from '@/components/ui/input';
    import * as Table from '@/components/ui/table';
    import { Search, Plus, MoreHorizontal } from 'lucide-svelte';
    import Pagination from '@/components/pagination.svelte';
    import { router } from '@inertiajs/svelte';
    import { debounce } from 'lodash';
    import * as DropdownMenu from '@/components/ui/dropdown-menu';
    import RoleSheet from './role-sheet.svelte';
    import Heading from '@/components/heading.svelte';
    import * as AlertDialog from '@/components/ui/alert-dialog';
    import { untrack } from 'svelte';
    import { toast } from 'svelte-sonner';

    let { roles, filters, permissions = [] } = $props();

    // Initialize search from filters
    let search = $state(untrack(() => filters.search || ''));
    let sheetOpen = $state(false);
    let selectedRole: any = $state(null);
    let deleteDialogOpen = $state(false);
    let roleToDelete: any = $state(null);

    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: 'Roles',
            href: '/roles',
        },
    ];

    const handleSearch = debounce((value: string) => {
        router.get(
            '/roles',
            { search: value },
            {
                preserveState: true,
                replace: true,
                preserveScroll: true,
            },
        );
    }, 300);

    function openCreate() {
        selectedRole = null;
        sheetOpen = true;
    }

    function openEdit(role: any) {
        selectedRole = role;
        sheetOpen = true;
    }

    function openDeleteDialog(role: any) {
        roleToDelete = role;
        deleteDialogOpen = true;
    }

    function confirmDelete() {
        if (roleToDelete) {
            router.delete(`/roles/${roleToDelete.id}`, {
                onFinish: () => {
                    deleteDialogOpen = false;
                    roleToDelete = null;
                    toast.success('Role berhasil dihapus');
                },
            });
        }
    }
</script>

<svelte:head>
    <title>Roles</title>
</svelte:head>

<AppLayout {breadcrumbs}>
    <LayoutComposer>
        {#snippet header()}
            <Heading title="Roles" description="Kelola role dan perizinan sistem" />
        {/snippet}

        {#snippet actions()}
            <Button onclick={openCreate}>
                <Plus class="mr-2 h-4 w-4" />
                Tambah Role
            </Button>
        {/snippet}

        {#snippet filters()}
            <div class="relative w-full max-w-sm">
                <Search class="absolute left-2 top-2.5 h-4 w-4 text-muted-foreground" />
                <Input placeholder="Cari role..." class="pl-8" bind:value={search} oninput={(e) => handleSearch(e.currentTarget.value)} />
            </div>
        {/snippet}

        <div class="rounded-md border bg-card">
            <Table.Root>
                <Table.Header>
                    <Table.Row>
                        <Table.Head class="w-[50px]">No</Table.Head>
                        <Table.Head>Nama</Table.Head>
                        <Table.Head>Jumlah Izin</Table.Head>
                        <Table.Head>Dibuat</Table.Head>
                        <Table.Head class="text-right">Aksi</Table.Head>
                    </Table.Row>
                </Table.Header>
                <Table.Body>
                    {#if roles.data.length === 0}
                        <Table.Row>
                            <Table.Cell colspan={5} class="text-center h-24 text-muted-foreground">Tidak ada data role.</Table.Cell>
                        </Table.Row>
                    {:else}
                        {#each roles.data as role, i (role.id)}
                            <Table.Row>
                                <Table.Cell>{(roles.current_page - 1) * roles.per_page + i + 1}</Table.Cell>
                                <Table.Cell class="font-medium">{role.name}</Table.Cell>
                                <Table.Cell>{role.permissions_count} Izin</Table.Cell>
                                <Table.Cell>{new Date(role.created_at).toLocaleDateString()}</Table.Cell>
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
                                            <DropdownMenu.Item onclick={() => router.visit(`/roles/${role.id}`)}>Detail</DropdownMenu.Item>
                                            {#if !role.is_preserved}
                                                <DropdownMenu.Item onclick={() => openEdit(role)}>Edit</DropdownMenu.Item>
                                                <DropdownMenu.Separator />
                                                <DropdownMenu.Item onclick={() => openDeleteDialog(role)} class="text-destructive"
                                                    >Hapus</DropdownMenu.Item
                                                >
                                            {/if}
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
            <Pagination meta={roles} />
        </div>

        <RoleSheet bind:open={sheetOpen} {selectedRole} {permissions} />

        <AlertDialog.Root bind:open={deleteDialogOpen}>
            <AlertDialog.Content>
                <AlertDialog.Header>
                    <AlertDialog.Title>Apakah anda yakin?</AlertDialog.Title>
                    <AlertDialog.Description>
                        Aksi ini tidak dapat dibatalkan. Ini akan menghapus role <strong>{roleToDelete?.name}</strong> secara permanen.
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
