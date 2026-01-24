<script lang="ts">
    import LayoutComposer from '@/layouts/LayoutComposer.svelte';
    import AppLayout from '@/layouts/AppLayout.svelte';
    import { type BreadcrumbItem } from '@/types';
    import Heading from '@/components/Heading.svelte';
    import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
    import { Bell, CheckCircle2, Clock } from 'lucide-svelte';
    import { cn } from '@/lib/utils';
    import * as Empty from '@/components/ui/empty';

    let { notifications } = $props();

    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: 'Notifikasi',
            href: '/notifications',
        },
    ];
</script>

<svelte:head>
    <title>Notifikasi</title>
</svelte:head>

<AppLayout {breadcrumbs}>
    <LayoutComposer>
        {#snippet header()}
            <Heading title="Notifikasi" description="Riwayat notifikasi terbaru" />
        {/snippet}

        {#snippet children()}
            <div class="space-y-4">
                {#if notifications.length === 0}
                    <Empty.Root class="border border-dashed">
                        <Empty.Header>
                            <Empty.Media variant="icon">
                                <Bell class="h-10 w-10 text-muted-foreground opacity-20" />
                            </Empty.Media>
                            <Empty.Title>Tidak ada notifikasi</Empty.Title>
                            <Empty.Description>Anda belum memiliki notifikasi terbaru saat ini.</Empty.Description>
                        </Empty.Header>
                    </Empty.Root>
                {:else}
                    <div class="grid gap-4">
                        {#each notifications as notification}
                            <Card class={cn('transition-all duration-200 hover:shadow-md', !notification.read_at && 'border-l-4 border-l-primary')}>
                                <CardHeader class="flex flex-row items-center gap-4 space-y-0 pb-2">
                                    <div
                                        class={cn(
                                            'flex h-8 w-8 items-center justify-center rounded-full',
                                            notification.read_at ? 'bg-muted text-muted-foreground' : 'bg-primary/10 text-primary',
                                        )}
                                    >
                                        {#if notification.read_at}
                                            <CheckCircle2 class="h-4 w-4" />
                                        {:else}
                                            <Bell class="h-4 w-4" />
                                        {/if}
                                    </div>
                                    <div class="flex flex-1 flex-col gap-1">
                                        <div class="flex items-center justify-between">
                                            <CardTitle class="text-base font-medium">
                                                {notification.extra?.title || 'Notifikasi Baru'}
                                            </CardTitle>
                                            <div class="flex items-center text-xs text-muted-foreground">
                                                <Clock class="mr-1 h-3 w-3" />
                                                {new Date(notification.created_at).toLocaleString('id-ID', {
                                                    day: 'numeric',
                                                    month: 'long',
                                                    year: 'numeric',
                                                    hour: '2-digit',
                                                    minute: '2-digit',
                                                })}
                                            </div>
                                        </div>
                                    </div>
                                </CardHeader>
                                <CardContent class="pl-14">
                                    <p class="text-sm text-foreground/90 leading-relaxed">
                                        {notification.content || '-'}
                                    </p>
                                </CardContent>
                            </Card>
                        {/each}
                    </div>
                {/if}
            </div>
        {/snippet}
    </LayoutComposer>
</AppLayout>
