<script lang="ts">
    import LayoutComposer from '@/layouts/layout-composer.svelte';
    import AppLayout from '@/layouts/app-layout.svelte';
    import { type BreadcrumbItem } from '@/types';
    import { Input } from '@/components/ui/input';
    import { Search } from 'lucide-svelte';
    import Pagination from '@/components/pagination.svelte';
    import { router } from '@inertiajs/svelte';
    import { debounce } from 'lodash';
    import { untrack } from 'svelte';
    import Heading from '@/components/heading.svelte';
    import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
    import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
    import * as Empty from '@/components/ui/empty';
    import { FileMinus } from 'lucide-svelte';

    let { logs, filters } = $props();

    // Initialize search from filters
    let search = $state(untrack(() => filters.search || ''));

    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: 'User Logs',
            href: '/user-logs',
        },
    ];

    const handleSearch = debounce((value: string) => {
        router.get(
            '/user-logs',
            { search: value },
            {
                preserveState: true,
                replace: true,
                preserveScroll: true,
            },
        );
    }, 300);

    function getInitials(name: string) {
        return name
            .split(' ')
            .map((n) => n[0])
            .slice(0, 2)
            .join('')
            .toUpperCase();
    }
</script>

<svelte:head>
    <title>User Logs</title>
</svelte:head>

<AppLayout {breadcrumbs}>
    <LayoutComposer>
        {#snippet header()}
            <Heading title="User Logs" description="Riwayat aktivitas pengguna sistem" />
        {/snippet}

        {#snippet filters()}
            <div class="relative w-full max-w-sm">
                <Search class="absolute left-2 top-2.5 h-4 w-4 text-muted-foreground" />
                <Input placeholder="Cari log..." class="pl-8" bind:value={search} oninput={(e) => handleSearch(e.currentTarget.value)} />
            </div>
        {/snippet}

        <div class="space-y-4">
            <div class="space-y-4">
                {#if logs.data.length === 0}
                    <Empty.Root class="border border-dashed">
                        <Empty.Header>
                            <Empty.Media variant="icon">
                                <FileMinus class="h-10 w-10 text-muted-foreground opacity-20" />
                            </Empty.Media>
                            <Empty.Title>Tidak ada data log</Empty.Title>
                            <Empty.Description>Belum ada riwayat aktivitas pengguna yang tercatat.</Empty.Description>
                        </Empty.Header>
                    </Empty.Root>
                {:else}
                    <div class="grid gap-4 md:grid-cols-1">
                        {#each logs.data as log (log.id)}
                            <Card class="transition-shadow hover:shadow-md">
                                <CardHeader class="flex flex-row items-center gap-4 space-y-0 pb-2">
                                    <Avatar class="h-10 w-10">
                                        <AvatarImage
                                            src={`https://ui-avatars.com/api/?name=${log.user?.name}&background=random`}
                                            alt={log.user?.name}
                                        />
                                        <AvatarFallback>{getInitials(log.user?.name || 'Unknown')}</AvatarFallback>
                                    </Avatar>
                                    <div class="flex flex-1 flex-col">
                                        <div class="flex items-center justify-between">
                                            <CardTitle class="text-base font-medium">
                                                {log.user?.name || 'Unknown User'}
                                            </CardTitle>
                                            <span class="text-xs text-muted-foreground">
                                                {new Date(log.created_at).toLocaleString('id-ID', {
                                                    day: 'numeric',
                                                    month: 'long',
                                                    year: 'numeric',
                                                    hour: '2-digit',
                                                    minute: '2-digit',
                                                })}
                                            </span>
                                        </div>
                                        <CardDescription class="text-xs">
                                            {log.user?.email || '-'}
                                        </CardDescription>
                                    </div>
                                </CardHeader>
                                <CardContent>
                                    <p class="text-sm text-foreground/90">{log.comment}</p>
                                </CardContent>
                            </Card>
                        {/each}
                    </div>
                {/if}
            </div>

            <div class="mt-4">
                <Pagination meta={logs} />
            </div>
        </div>
    </LayoutComposer>
</AppLayout>
