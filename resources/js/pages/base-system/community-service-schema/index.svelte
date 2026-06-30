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
    import CommunityServiceSchemaSheet from './community-service-schema-sheet.svelte';
    import Heading from '@/components/heading.svelte';
    import * as AlertDialog from '@/components/ui/alert-dialog';
    import { untrack } from 'svelte';
    import { toast } from 'svelte-sonner';

    let { communityServiceSchemas, filters } = $props();

    // Initialize search from filters
    let search = $state(untrack(() => filters.search || ''));
    let sheetOpen = $state(false);
    let selectedSchema: any = $state(null);
    let deleteDialogOpen = $state(false);
    let schemaToDelete: any = $state(null);

    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: 'Skema PKM',
            href: '/master/community-service-schema',
        },
    ];

    const handleSearch = debounce((value: string) => {
        router.get(
            '/master/community-service-schema',
            { search: value },
            {
                preserveState: true,
                replace: true,
                preserveScroll: true,
            },
        );
    }, 300);

    function openCreate() {
        selectedSchema = null;
        sheetOpen = true;
    }

    function openEdit(schema: any) {
        selectedSchema = schema;
        sheetOpen = true;
    }

    function openDeleteDialog(schema: any) {
        schemaToDelete = schema;
        deleteDialogOpen = true;
    }

    function confirmDelete() {
        if (schemaToDelete) {
            router.delete(`/master/community-service-schema/${schemaToDelete.id}`, {
                onFinish: () => {
                    deleteDialogOpen = false;
                    schemaToDelete = null;
                    toast.success('Skema PKM berhasil dihapus');
                },
            });
        }
    }
</script>

<svelte:head>
    <title>Skema PKM</title>
</svelte:head>

<AppLayout {breadcrumbs}>
    <LayoutComposer>
        {#snippet header()}
            <Heading title="Skema PKM" description="Kelola data skema pengabdian kepada masyarakat" />
        {/snippet}

        {#snippet actions()}
            <Button onclick={openCreate}>
                <Plus class="mr-2 h-4 w-4" />
                Tambah Skema PKM
            </Button>
        {/snippet}

        {#snippet filters()}
            <div class="relative w-full max-w-sm">
                <Search class="absolute left-2 top-2.5 h-4 w-4 text-muted-foreground" />
                <Input placeholder="Cari skema PKM..." class="pl-8" bind:value={search} oninput={(e) => handleSearch(e.currentTarget.value)} />
            </div>
        {/snippet}

        <div class="rounded-md border bg-card">
            <Table.Root>
                <Table.Header>
                    <Table.Row>
                        <Table.Head class="w-[50px]">No</Table.Head>
                        <Table.Head>Judul</Table.Head>
                        <Table.Head>Deskripsi</Table.Head>
                        <Table.Head>Dibuat</Table.Head>
                        <Table.Head class="text-right">Aksi</Table.Head>
                    </Table.Row>
                </Table.Header>
                <Table.Body>
                    {#if communityServiceSchemas.data.length === 0}
                        <Table.Row>
                            <Table.Cell colspan={5} class="text-center h-24 text-muted-foreground">Tidak ada data skema PKM.</Table.Cell>
                        </Table.Row>
                    {:else}
                        {#each communityServiceSchemas.data as schema, i (schema.id)}
                            <Table.Row>
                                <Table.Cell>{(communityServiceSchemas.current_page - 1) * communityServiceSchemas.per_page + i + 1}</Table.Cell>
                                <Table.Cell class="font-medium">{schema.title}</Table.Cell>
                                <Table.Cell class="truncate max-w-[300px]">{schema.description || '-'}</Table.Cell>
                                <Table.Cell>{new Date(schema.created_at).toLocaleDateString()}</Table.Cell>
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
                                            <DropdownMenu.Item onclick={() => router.visit(`/master/community-service-schema/${schema.id}`)}
                                                >Detail</DropdownMenu.Item
                                            >
                                            <DropdownMenu.Item onclick={() => openEdit(schema)}>Edit</DropdownMenu.Item>
                                            <DropdownMenu.Separator />
                                            <DropdownMenu.Item onclick={() => openDeleteDialog(schema)} class="text-destructive"
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
            <Pagination meta={communityServiceSchemas} />
        </div>

        <CommunityServiceSchemaSheet bind:open={sheetOpen} selectedCommunityServiceSchema={selectedSchema} />

        <AlertDialog.Root bind:open={deleteDialogOpen}>
            <AlertDialog.Content>
                <AlertDialog.Header>
                    <AlertDialog.Title>Apakah anda yakin?</AlertDialog.Title>
                    <AlertDialog.Description>
                        Aksi ini tidak dapat dibatalkan. Ini akan menghapus skema PKM <strong>{schemaToDelete?.title}</strong> secara permanen.
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
