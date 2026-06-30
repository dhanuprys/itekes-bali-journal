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
    import ResearchTargetSheet from './research-target-sheet.svelte';
    import Heading from '@/components/heading.svelte';
    import * as AlertDialog from '@/components/ui/alert-dialog';
    import { untrack } from 'svelte';
    import { toast } from 'svelte-sonner';

    let { researchTargets, filters } = $props();

    // Initialize search from filters
    let search = $state(untrack(() => filters.search || ''));
    let sheetOpen = $state(false);
    let selectedResearchTarget: any = $state(null);
    let deleteDialogOpen = $state(false);
    let researchTargetToDelete: any = $state(null);

    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: 'Target Riset',
            href: '/master/research-target',
        },
    ];

    const handleSearch = debounce((value: string) => {
        router.get(
            '/master/research-target',
            { search: value },
            {
                preserveState: true,
                replace: true,
                preserveScroll: true,
            },
        );
    }, 300);

    function openCreate() {
        selectedResearchTarget = null;
        sheetOpen = true;
    }

    function openEdit(target: any) {
        selectedResearchTarget = target;
        sheetOpen = true;
    }

    function openDeleteDialog(target: any) {
        researchTargetToDelete = target;
        deleteDialogOpen = true;
    }

    function confirmDelete() {
        if (researchTargetToDelete) {
            router.delete(`/master/research-target/${researchTargetToDelete.id}`, {
                onFinish: () => {
                    deleteDialogOpen = false;
                    researchTargetToDelete = null;
                    toast.success('Target Riset berhasil dihapus');
                },
            });
        }
    }
</script>

<svelte:head>
    <title>Target Riset</title>
</svelte:head>

<AppLayout {breadcrumbs}>
    <LayoutComposer>
        {#snippet header()}
            <Heading title="Target Riset" description="Kelola data target riset" />
        {/snippet}

        {#snippet actions()}
            <Button onclick={openCreate}>
                <Plus class="mr-2 h-4 w-4" />
                Tambah Target Riset
            </Button>
        {/snippet}

        {#snippet filters()}
            <div class="relative w-full max-w-sm">
                <Search class="absolute left-2 top-2.5 h-4 w-4 text-muted-foreground" />
                <Input placeholder="Cari target riset..." class="pl-8" bind:value={search} oninput={(e) => handleSearch(e.currentTarget.value)} />
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
                    {#if researchTargets.data.length === 0}
                        <Table.Row>
                            <Table.Cell colspan={5} class="text-center h-24 text-muted-foreground">Tidak ada data target riset.</Table.Cell>
                        </Table.Row>
                    {:else}
                        {#each researchTargets.data as target, i (target.id)}
                            <Table.Row>
                                <Table.Cell>{(researchTargets.current_page - 1) * researchTargets.per_page + i + 1}</Table.Cell>
                                <Table.Cell class="font-medium">{target.title}</Table.Cell>
                                <Table.Cell class="truncate max-w-[300px]">{target.description || '-'}</Table.Cell>
                                <Table.Cell>{new Date(target.created_at).toLocaleDateString()}</Table.Cell>
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
                                            <DropdownMenu.Item onclick={() => router.visit(`/master/research-target/${target.id}`)}
                                                >Detail</DropdownMenu.Item
                                            >
                                            <DropdownMenu.Item onclick={() => openEdit(target)}>Edit</DropdownMenu.Item>
                                            <DropdownMenu.Separator />
                                            <DropdownMenu.Item onclick={() => openDeleteDialog(target)} class="text-destructive"
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
            <Pagination meta={researchTargets} />
        </div>

        <ResearchTargetSheet bind:open={sheetOpen} {selectedResearchTarget} />

        <AlertDialog.Root bind:open={deleteDialogOpen}>
            <AlertDialog.Content>
                <AlertDialog.Header>
                    <AlertDialog.Title>Apakah anda yakin?</AlertDialog.Title>
                    <AlertDialog.Description>
                        Aksi ini tidak dapat dibatalkan. Ini akan menghapus target riset <strong>{researchTargetToDelete?.title}</strong> secara permanen.
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
