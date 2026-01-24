<script lang="ts">
    import LayoutComposer from '@/layouts/LayoutComposer.svelte';
    import AppLayout from '@/layouts/AppLayout.svelte';
    import { type BreadcrumbItem } from '@/types';
    import { Button } from '@/components/ui/button';
    import { Input } from '@/components/ui/input';
    import * as Table from '@/components/ui/table';
    import { Search, Plus, MoreHorizontal } from 'lucide-svelte';
    import Pagination from '@/components/Pagination.svelte';
    import { router } from '@inertiajs/svelte';
    import { debounce } from 'lodash';
    import * as DropdownMenu from '@/components/ui/dropdown-menu';
    import EthicSubjectSheet from './EthicSubjectSheet.svelte';
    import Heading from '@/components/Heading.svelte';
    import * as AlertDialog from '@/components/ui/alert-dialog';
    import { untrack } from 'svelte';
    import { toast } from 'svelte-sonner';

    let { ethicalClearanceSubjects, filters } = $props();

    // Initialize search from filters
    let search = $state(untrack(() => filters.search || ''));
    let sheetOpen = $state(false);
    let selectedSubject: any = $state(null);
    let deleteDialogOpen = $state(false);
    let subjectToDelete: any = $state(null);

    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: 'Subjek Etik',
            href: '/master/ethic-subject',
        },
    ];

    const handleSearch = debounce((value: string) => {
        router.get(
            '/master/ethic-subject',
            { search: value },
            {
                preserveState: true,
                replace: true,
                preserveScroll: true,
            },
        );
    }, 300);

    function openCreate() {
        selectedSubject = null;
        sheetOpen = true;
    }

    function openEdit(subject: any) {
        selectedSubject = subject;
        sheetOpen = true;
    }

    function openDeleteDialog(subject: any) {
        subjectToDelete = subject;
        deleteDialogOpen = true;
    }

    function confirmDelete() {
        if (subjectToDelete) {
            router.delete(`/master/ethic-subject/${subjectToDelete.id}`, {
                onFinish: () => {
                    deleteDialogOpen = false;
                    subjectToDelete = null;
                    toast.success('Subjek Etik berhasil dihapus');
                },
            });
        }
    }
</script>

<svelte:head>
    <title>Subjek Etik</title>
</svelte:head>

<AppLayout {breadcrumbs}>
    <LayoutComposer>
        {#snippet header()}
            <Heading title="Subjek Etik" description="Kelola data subjek etik" />
        {/snippet}

        {#snippet actions()}
            <Button onclick={openCreate}>
                <Plus class="mr-2 h-4 w-4" />
                Tambah Subjek Etik
            </Button>
        {/snippet}

        {#snippet filters()}
            <div class="relative w-full max-w-sm">
                <Search class="absolute left-2 top-2.5 h-4 w-4 text-muted-foreground" />
                <Input placeholder="Cari subjek etik..." class="pl-8" bind:value={search} oninput={(e) => handleSearch(e.currentTarget.value)} />
            </div>
        {/snippet}

        {#snippet children()}
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
                        {#if ethicalClearanceSubjects.data.length === 0}
                            <Table.Row>
                                <Table.Cell colspan={5} class="text-center h-24 text-muted-foreground">Tidak ada data subjek etik.</Table.Cell>
                            </Table.Row>
                        {:else}
                            {#each ethicalClearanceSubjects.data as subject, i}
                                <Table.Row>
                                    <Table.Cell>{(ethicalClearanceSubjects.current_page - 1) * ethicalClearanceSubjects.per_page + i + 1}</Table.Cell>
                                    <Table.Cell class="font-medium">{subject.title}</Table.Cell>
                                    <Table.Cell class="truncate max-w-[300px]">{subject.description || '-'}</Table.Cell>
                                    <Table.Cell>{new Date(subject.created_at).toLocaleDateString()}</Table.Cell>
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
                                                <DropdownMenu.Item onclick={() => router.visit(`/master/ethic-subject/${subject.id}`)}
                                                    >Detail</DropdownMenu.Item
                                                >
                                                <DropdownMenu.Item onclick={() => openEdit(subject)}>Edit</DropdownMenu.Item>
                                                <DropdownMenu.Separator />
                                                <DropdownMenu.Item onclick={() => openDeleteDialog(subject)} class="text-destructive"
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
                <Pagination links={ethicalClearanceSubjects.links} meta={ethicalClearanceSubjects} />
            </div>

            <EthicSubjectSheet bind:open={sheetOpen} selectedEthicalClearanceSubject={selectedSubject} />

            <AlertDialog.Root bind:open={deleteDialogOpen}>
                <AlertDialog.Content>
                    <AlertDialog.Header>
                        <AlertDialog.Title>Apakah anda yakin?</AlertDialog.Title>
                        <AlertDialog.Description>
                            Aksi ini tidak dapat dibatalkan. Ini akan menghapus subjek etik <strong>{subjectToDelete?.title}</strong> secara permanen.
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
        {/snippet}
    </LayoutComposer>
</AppLayout>
