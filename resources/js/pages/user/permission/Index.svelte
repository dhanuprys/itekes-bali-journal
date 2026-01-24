<script lang="ts">
    import LayoutComposer from '@/layouts/LayoutComposer.svelte';
    import AppLayout from '@/layouts/AppLayout.svelte';
    import { type BreadcrumbItem } from '@/types';
    import { Input } from '@/components/ui/input';
    import * as Table from '@/components/ui/table';
    import { Search, MoreHorizontal } from 'lucide-svelte';
    import Pagination from '@/components/Pagination.svelte';
    import { router } from '@inertiajs/svelte';
    import { debounce } from 'lodash';
    import * as DropdownMenu from '@/components/ui/dropdown-menu';
    import Heading from '@/components/Heading.svelte';
    import { untrack } from 'svelte';

    let { permissions, filters } = $props();

    // Initialize search from filters
    let search = $state(untrack(() => filters.search || ''));

    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: 'Permissions',
            href: '/permissions',
        },
    ];

    const handleSearch = debounce((value: string) => {
        router.get(
            '/permissions',
            { search: value },
            {
                preserveState: true,
                replace: true,
                preserveScroll: true,
            },
        );
    }, 300);
</script>

<svelte:head>
    <title>Permissions</title>
</svelte:head>

<AppLayout {breadcrumbs}>
    <LayoutComposer>
        {#snippet header()}
            <Heading title="Permissions" description="Daftar semua perizinan dalam sistem" />
        {/snippet}

        {#snippet filters()}
            <div class="relative w-full max-w-sm">
                <Search class="absolute left-2 top-2.5 h-4 w-4 text-muted-foreground" />
                <Input placeholder="Cari permission..." class="pl-8" bind:value={search} oninput={(e) => handleSearch(e.currentTarget.value)} />
            </div>
        {/snippet}

        {#snippet children()}
            <div class="rounded-md border bg-card">
                <Table.Root>
                    <Table.Header>
                        <Table.Row>
                            <Table.Head class="w-[50px]">No</Table.Head>
                            <Table.Head>Nama</Table.Head>
                            <Table.Head>Guard</Table.Head>
                            <Table.Head>Dibuat</Table.Head>
                            <Table.Head class="text-right">Aksi</Table.Head>
                        </Table.Row>
                    </Table.Header>
                    <Table.Body>
                        {#if permissions.data.length === 0}
                            <Table.Row>
                                <Table.Cell colspan={5} class="text-center h-24 text-muted-foreground">Tidak ada data permission.</Table.Cell>
                            </Table.Row>
                        {:else}
                            {#each permissions.data as permission, i}
                                <Table.Row>
                                    <Table.Cell>{(permissions.current_page - 1) * permissions.per_page + i + 1}</Table.Cell>
                                    <Table.Cell class="font-medium">{permission.name}</Table.Cell>
                                    <Table.Cell>{permission.guard_name}</Table.Cell>
                                    <Table.Cell>{new Date(permission.created_at).toLocaleDateString()}</Table.Cell>
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
                                                <DropdownMenu.Item onclick={() => router.visit(`/permissions/${permission.id}`)}
                                                    >Detail</DropdownMenu.Item
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
                <Pagination links={permissions.links} meta={permissions} />
            </div>
        {/snippet}
    </LayoutComposer>
</AppLayout>
