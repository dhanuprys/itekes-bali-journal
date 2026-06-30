<script lang="ts">
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
    import StudyProgramSheet from './study-program-sheet.svelte';
    import Heading from '@/components/heading.svelte';
    import * as AlertDialog from '@/components/ui/alert-dialog';
    import { untrack } from 'svelte';
    import { toast } from 'svelte-sonner';

    let { studyPrograms, filters } = $props();

    // Initialize search from filters
    let search = $state(untrack(() => filters.search || ''));
    let sheetOpen = $state(false);
    let selectedStudyProgram: any = $state(null);
    let deleteDialogOpen = $state(false);
    let studyProgramToDelete: any = $state(null);

    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: 'Program Studi',
            href: '/study-program',
        },
    ];

    const handleSearch = debounce((value: string) => {
        router.get(
            '/study-program',
            { search: value },
            {
                preserveState: true,
                replace: true,
                preserveScroll: true,
            },
        );
    }, 300);

    function openCreate() {
        selectedStudyProgram = null;
        sheetOpen = true;
    }

    function openEdit(studyProgram: any) {
        selectedStudyProgram = studyProgram;
        sheetOpen = true;
    }

    function openDeleteDialog(studyProgram: any) {
        studyProgramToDelete = studyProgram;
        deleteDialogOpen = true;
    }

    function confirmDelete() {
        if (studyProgramToDelete) {
            router.delete(`/study-program/${studyProgramToDelete.id}`, {
                onFinish: () => {
                    deleteDialogOpen = false;
                    studyProgramToDelete = null;
                    toast.success('Program Studi berhasil dihapus');
                },
            });
        }
    }
</script>

<svelte:head>
    <title>Program Studi</title>
</svelte:head>

<AppLayout {breadcrumbs}>
    <div class="px-4 py-6">
        <div class="flex items-center justify-between mb-6">
            <Heading title="Program Studi" description="Kelola data program studi" />
            <Button onclick={openCreate}>
                <Plus class="mr-2 h-4 w-4" />
                Tambah Program Studi
            </Button>
        </div>

        <div class="flex items-center space-x-2 mb-4">
            <div class="relative w-full max-w-sm">
                <Search class="absolute left-2 top-2.5 h-4 w-4 text-muted-foreground" />
                <Input placeholder="Cari program studi..." class="pl-8" bind:value={search} oninput={(e) => handleSearch(e.currentTarget.value)} />
            </div>
        </div>

        <div class="rounded-md border bg-card">
            <Table.Root>
                <Table.Header>
                    <Table.Row>
                        <Table.Head class="w-[50px]">No</Table.Head>
                        <Table.Head>Nama</Table.Head>
                        <Table.Head>Dibuat</Table.Head>
                        <Table.Head class="text-right">Aksi</Table.Head>
                    </Table.Row>
                </Table.Header>
                <Table.Body>
                    {#if studyPrograms.data.length === 0}
                        <Table.Row>
                            <Table.Cell colspan={4} class="text-center h-24 text-muted-foreground">Tidak ada data program studi.</Table.Cell>
                        </Table.Row>
                    {:else}
                        {#each studyPrograms.data as studyProgram, i (studyProgram.id)}
                            <Table.Row>
                                <Table.Cell>{(studyPrograms.current_page - 1) * studyPrograms.per_page + i + 1}</Table.Cell>
                                <Table.Cell class="font-medium">{studyProgram.name}</Table.Cell>
                                <Table.Cell>{new Date(studyProgram.created_at).toLocaleDateString()}</Table.Cell>
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
                                            <DropdownMenu.Item onclick={() => router.visit(`/study-program/${studyProgram.id}`)}
                                                >Detail</DropdownMenu.Item
                                            >
                                            <DropdownMenu.Item onclick={() => openEdit(studyProgram)}>Edit</DropdownMenu.Item>
                                            <DropdownMenu.Separator />
                                            <DropdownMenu.Item onclick={() => openDeleteDialog(studyProgram)} class="text-destructive"
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
            <Pagination meta={studyPrograms} />
        </div>

        <StudyProgramSheet bind:open={sheetOpen} {selectedStudyProgram} />

        <AlertDialog.Root bind:open={deleteDialogOpen}>
            <AlertDialog.Content>
                <AlertDialog.Header>
                    <AlertDialog.Title>Apakah anda yakin?</AlertDialog.Title>
                    <AlertDialog.Description>
                        Aksi ini tidak dapat dibatalkan. Ini akan menghapus program studi <strong>{studyProgramToDelete?.name}</strong> secara permanen.
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
    </div>
</AppLayout>
