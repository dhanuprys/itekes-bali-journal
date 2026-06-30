<script lang="ts">
    import HeadingSmall from '@/components/heading-small.svelte';
    import { type BreadcrumbItem } from '@/types';
    import AppLayout from '@/layouts/app-layout.svelte';
    import SettingsLayout from '@/layouts/settings/layout.svelte';
    import { Card, CardContent } from '@/components/ui/card';
    import * as Empty from '@/components/ui/empty';
    import { History } from 'lucide-svelte';
    import Pagination from '@/components/pagination.svelte';

    let { loginLogs } = $props();

    const breadcrumbItems: BreadcrumbItem[] = [
        {
            title: 'Riwayat Login',
            href: '/settings/login-logs',
        },
    ];
</script>

<svelte:head>
    <title>Riwayat Login</title>
</svelte:head>

<AppLayout breadcrumbs={breadcrumbItems}>
    <SettingsLayout isFullWidth={true}>
        <div class="space-y-6">
            <HeadingSmall title="Riwayat Login" description="Pantau aktivitas login ke akun Anda" />

            <Card>
                <CardContent>
                    {#if loginLogs.data.length > 0}
                        <div class="relative w-full overflow-auto">
                            <table class="w-full caption-bottom text-sm">
                                <thead class="[&_tr]:border-b">
                                    <tr class="border-b transition-colors hover:bg-muted/50 data-[state=selected]:bg-muted">
                                        <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground w-[180px]">Waktu</th>
                                        <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground w-[150px]">IP Address</th>
                                        <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground">Perangkat</th>
                                    </tr>
                                </thead>
                                <tbody class="[&_tr:last-child]:border-0">
                                    {#each loginLogs.data as log (log.id)}
                                        <tr class="border-b transition-colors hover:bg-muted/50 data-[state=selected]:bg-muted">
                                            <td class="p-4 align-middle">
                                                <div class="flex flex-col">
                                                    <span class="font-medium">
                                                        {new Date(log.created_at).toLocaleDateString('id-ID', {
                                                            day: 'numeric',
                                                            month: 'long',
                                                            year: 'numeric',
                                                        })}
                                                    </span>
                                                    <span class="text-xs text-muted-foreground">
                                                        {new Date(log.created_at).toLocaleTimeString('id-ID', {
                                                            hour: '2-digit',
                                                            minute: '2-digit',
                                                            hour12: false,
                                                        })} WIB
                                                    </span>
                                                </div>
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
                                <Empty.Description>Belum ada aktivitas login yang tercatat.</Empty.Description>
                            </Empty.Header>
                        </Empty.Root>
                    {/if}
                </CardContent>
            </Card>

            <div class="mt-4">
                <Pagination meta={loginLogs} />
            </div>
        </div>
    </SettingsLayout>
</AppLayout>
