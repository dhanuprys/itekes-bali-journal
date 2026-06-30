<script lang="ts">
    import LayoutComposer from '@/layouts/layout-composer.svelte';
    import Heading from '@/components/heading.svelte';
    import AppLayout from '@/layouts/app-layout.svelte';
    import { Button } from '@/components/ui/button';
    import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
    import { Badge } from '@/components/ui/badge';
    import { ArrowLeft, History } from 'lucide-svelte';
    import { router } from '@inertiajs/svelte';
    import * as Empty from '@/components/ui/empty';

    let { user } = $props();

    let breadcrumbs = $derived([
        {
            title: 'Pengguna',
            href: '/users',
        },
        {
            title: user?.name || 'Detail Pengguna',
            href: '#',
        },
    ]);
</script>

<svelte:head>
    <title>Detail Pengguna - {user?.name || 'Loading...'}</title>
</svelte:head>

<AppLayout {breadcrumbs}>
    <LayoutComposer>
        {#snippet backButton()}
            <Button variant="outline" onclick={() => router.visit('/users')}>
                <ArrowLeft class="mr-2 h-4 w-4" />
                Kembali
            </Button>
        {/snippet}

        {#snippet header()}
            <Heading title={user?.name || 'Detail Pengguna'} description="Detail informasi pengguna" />
        {/snippet}

        <div class="grid gap-6 md:grid-cols-2">
            <Card>
                <CardHeader>
                    <CardTitle>Informasi Dasar</CardTitle>
                </CardHeader>
                <CardContent class="space-y-4">
                    <div class="grid grid-cols-3 gap-4">
                        <div class="text-sm font-medium text-muted-foreground">Nama</div>
                        <div class="col-span-2 text-sm">{user?.name || '-'}</div>
                    </div>
                    <div class="grid grid-cols-3 gap-4">
                        <div class="text-sm font-medium text-muted-foreground">Username</div>
                        <div class="col-span-2 text-sm">{user?.username || '-'}</div>
                    </div>
                    <div class="grid grid-cols-3 gap-4">
                        <div class="text-sm font-medium text-muted-foreground">Email</div>
                        <div class="col-span-2 text-sm">{user?.email || '-'}</div>
                    </div>
                    <div class="grid grid-cols-3 gap-4">
                        <div class="text-sm font-medium text-muted-foreground">Status</div>
                        <div class="col-span-2">
                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium bg-green-100 text-green-800">
                                Active
                            </span>
                        </div>
                    </div>
                    <div class="grid grid-cols-3 gap-4">
                        <div class="text-sm font-medium text-muted-foreground">Bergabung</div>
                        <div class="col-span-2 text-sm">{user?.created_at ? new Date(user.created_at).toLocaleDateString() : '-'}</div>
                    </div>
                </CardContent>
            </Card>

            <Card>
                <CardHeader>
                    <CardTitle>Riwayat Login (Terakhir 10)</CardTitle>
                </CardHeader>
                <CardContent>
                    {#if user?.login_logs && user.login_logs.length > 0}
                        <div class="relative w-full overflow-auto">
                            <table class="w-full caption-bottom text-sm">
                                <thead class="[&_tr]:border-b">
                                    <tr class="border-b transition-colors hover:bg-muted/50 data-[state=selected]:bg-muted">
                                        <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground w-[150px]">Waktu</th>
                                        <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground w-[120px]">IP Address</th>
                                        <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground">Perangkat</th>
                                    </tr>
                                </thead>
                                <tbody class="[&_tr:last-child]:border-0">
                                    {#each user.login_logs as log (log.id)}
                                        <tr class="border-b transition-colors hover:bg-muted/50 data-[state=selected]:bg-muted">
                                            <td class="p-4 align-middle">
                                                {new Date(log.created_at).toLocaleString('id-ID', {
                                                    day: 'numeric',
                                                    month: 'short',
                                                    year: 'numeric',
                                                    hour: '2-digit',
                                                    minute: '2-digit',
                                                })}
                                            </td>
                                            <td class="p-4 align-middle font-mono text-xs">{log.ip_address}</td>
                                            <td class="p-4 align-middle text-xs text-muted-foreground truncate max-w-[200px]" title={log.user_agent}>
                                                {log.user_agent}
                                            </td>
                                        </tr>
                                    {/each}
                                </tbody>
                            </table>
                        </div>
                    {:else}
                        <Empty.Root class="border border-dashed">
                            <Empty.Header>
                                <Empty.Media variant="icon">
                                    <History class="h-10 w-10 text-muted-foreground opacity-20" />
                                </Empty.Media>
                                <Empty.Title>Tidak ada riwayat login</Empty.Title>
                                <Empty.Description>Pengguna belum pernah login atau data belum tercatat.</Empty.Description>
                            </Empty.Header>
                        </Empty.Root>
                    {/if}
                </CardContent>
            </Card>

            <Card>
                <CardHeader>
                    <CardTitle>Role & Perizinan</CardTitle>
                </CardHeader>
                <CardContent class="space-y-4">
                    <div>
                        <div class="text-sm font-medium text-muted-foreground mb-2">Role Saat Ini</div>
                        <div class="flex flex-wrap gap-2">
                            {#each user?.roles || [] as role (role.id)}
                                <Badge variant="secondary">{role.name}</Badge>
                            {/each}
                        </div>
                    </div>

                    {#if user?.permissions && user.permissions.length > 0}
                        <div class="pt-4 border-t">
                            <div class="text-sm font-medium text-muted-foreground mb-2">Izin Khusus</div>
                            <div class="flex flex-wrap gap-2">
                                {#each user.permissions as perm (perm.id)}
                                    <Badge variant="outline">{perm.name}</Badge>
                                {/each}
                            </div>
                        </div>
                    {/if}
                </CardContent>
            </Card>
        </div>
    </LayoutComposer>
</AppLayout>
