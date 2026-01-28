<script lang="ts">
    import LayoutComposer from '@/layouts/LayoutComposer.svelte';
    import AppLayout from '@/layouts/AppLayout.svelte';
    import { type BreadcrumbItem, type Notification } from '@/types';
    import Heading from '@/components/Heading.svelte';
    import { Bell, CheckCircle2, FileText, Activity, Info, AlertTriangle, XCircle, CheckCircle } from 'lucide-svelte';
    import { cn } from '@/lib/utils';
    import * as Empty from '@/components/ui/empty';

    let { notifications } = $props<{ notifications: Notification[] }>();

    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: 'Notifikasi',
            href: '/notifications',
        },
    ];

    // Helper to group notifications
    function groupNotifications(notifs: Notification[]) {
        const groups = {
            today: [] as Notification[],
            yesterday: [] as Notification[],
            earlier: [] as Notification[],
        };

        const today = new Date();
        const yesterday = new Date(today.getTime() - 86400000);

        notifs.forEach((n) => {
            const date = new Date(n.created_at);
            if (date.toDateString() === today.toDateString()) {
                groups.today.push(n);
            } else if (date.toDateString() === yesterday.toDateString()) {
                groups.yesterday.push(n);
            } else {
                groups.earlier.push(n);
            }
        });

        return groups;
    }

    const groupedNotifications = $derived(groupNotifications(notifications));

    function getIcon(notification: Notification) {
        if (notification.extra?.type) {
            switch (notification.extra.type) {
                case 'info':
                    return Info;
                case 'warning':
                    return AlertTriangle;
                case 'error':
                    return XCircle;
                case 'success':
                    return CheckCircle;
                default:
                    return Info;
            }
        }
        // Fallback checks
        const content = notification.content.toLowerCase();
        if (content.includes('proposal')) return FileText;
        if (content.includes('laporan')) return Activity;
        return Bell;
    }

    function getIconColor(notification: Notification) {
        if (notification.read_at) return 'bg-muted/50 text-muted-foreground/70';

        if (notification.extra?.type) {
            switch (notification.extra.type) {
                case 'warning':
                    return 'bg-yellow-500/10 text-yellow-600';
                case 'error':
                    return 'bg-red-500/10 text-red-600';
                case 'success':
                    return 'bg-green-500/10 text-green-600';
                case 'info':
                default:
                    return 'bg-blue-500/10 text-blue-600';
            }
        }
        return 'bg-primary/10 text-primary';
    }
</script>

<svelte:head>
    <title>Notifikasi</title>
</svelte:head>

<AppLayout {breadcrumbs}>
    <LayoutComposer>
        {#snippet header()}
            <Heading title="Notifikasi" description="Pantau aktivitas dan pembaruan terbaru." />
        {/snippet}

        <div>
            {#if notifications.length === 0}
                <Empty.Root class="border border-dashed py-12 rounded-lg">
                    <Empty.Header>
                        <Empty.Media variant="icon">
                            <div class="rounded-full bg-muted/50 p-3">
                                <Bell class="h-6 w-6 text-muted-foreground/60" />
                            </div>
                        </Empty.Media>
                        <Empty.Title class="mt-3 text-lg font-medium">Tidak ada notifikasi</Empty.Title>
                        <Empty.Description class="max-w-xs mx-auto text-sm text-muted-foreground">
                            Belum ada aktivitas baru yang perlu Anda pantau saat ini.
                        </Empty.Description>
                    </Empty.Header>
                </Empty.Root>
            {:else}
                <div class="space-y-6">
                    {#each Object.entries(groupedNotifications) as [key, group] (key)}
                        {#if group.length > 0}
                            <div>
                                <h3 class="mb-2 px-2 text-xs font-semibold uppercase tracking-wider text-muted-foreground/70 flex items-center gap-2">
                                    {#if key === 'today'}
                                        Hari Ini
                                    {:else if key === 'yesterday'}
                                        Kemarin
                                    {:else}
                                        Lebih Lama
                                    {/if}
                                    <div class="h-px flex-1 bg-border/40"></div>
                                </h3>

                                <div class="space-y-1">
                                    {#each group as notification (notification.id)}
                                        <!-- svelte-ignore a11y_click_events_have_key_events -->
                                        <!-- svelte-ignore a11y_no_static_element_interactions -->
                                        <div
                                            class={cn(
                                                'group relative flex cursor-pointer gap-3 rounded-lg px-3 py-3 transition-colors hover:bg-muted/60',
                                                !notification.read_at && 'bg-primary/5',
                                            )}
                                            onclick={() => {
                                                if (notification.extra?.url) {
                                                    window.location.href = notification.extra.url;
                                                }
                                            }}
                                        >
                                            <!-- Icon & Content -->
                                            <div class="flex flex-1 items-center gap-3">
                                                <div class="shrink-0">
                                                    <div
                                                        class={cn(
                                                            'flex h-8 w-8 items-center justify-center rounded-full transition-colors',
                                                            getIconColor(notification),
                                                        )}
                                                    >
                                                        {#if !notification.read_at}
                                                            {@const Icon = getIcon(notification)}
                                                            <Icon class="h-4 w-4" />
                                                        {:else}
                                                            <CheckCircle2 class="h-4 w-4" />
                                                        {/if}
                                                    </div>
                                                </div>

                                                <div class="flex flex-1 flex-col justify-center min-w-0">
                                                    <div class="flex items-center justify-between gap-4">
                                                        <p
                                                            class={cn(
                                                                'text-sm truncate',
                                                                !notification.read_at ? 'font-medium text-foreground' : 'text-foreground/70',
                                                            )}
                                                        >
                                                            {notification.content || 'Notifikasi Baru'}
                                                        </p>
                                                        <span class="shrink-0 text-[10px] text-muted-foreground/60">
                                                            {new Date(notification.created_at).toLocaleTimeString('id-ID', {
                                                                hour: '2-digit',
                                                                minute: '2-digit',
                                                            })}
                                                        </span>
                                                    </div>
                                                    <!-- Optional subtitle/extra title if distinct -->
                                                    {#if notification.extra?.title && notification.content !== notification.extra.title}
                                                        <p class="text-xs text-muted-foreground truncate opacity-80 mt-0.5">
                                                            {notification.extra.title}
                                                        </p>
                                                    {/if}
                                                </div>
                                            </div>
                                        </div>
                                    {/each}
                                </div>
                            </div>
                        {/if}
                    {/each}
                </div>
            {/if}
        </div>
    </LayoutComposer>
</AppLayout>
