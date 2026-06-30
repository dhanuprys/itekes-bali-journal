<script lang="ts">
    import LayoutComposer from '@/layouts/layout-composer.svelte';
    import AppLayout from '@/layouts/app-layout.svelte';
    import { type BreadcrumbItem } from '@/types';
    import Heading from '@/components/heading.svelte';
    import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
    import { Badge } from '@/components/ui/badge';
    import { Rocket, PenTool, Bug, Wrench, FileMinus } from 'lucide-svelte';
    import { cn } from '@/lib/utils';
    import * as Empty from '@/components/ui/empty';

    let { changelog } = $props();

    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: 'Changelog',
            href: '/changelog',
        },
    ];

    const TYPE_COLORS: Record<string, string> = {
        new: 'bg-green-100 text-green-700 dark:bg-green-500/15 dark:text-green-400 border-green-200 dark:border-green-500/20',
        fix: 'bg-red-100 text-red-700 dark:bg-red-500/15 dark:text-red-400 border-red-200 dark:border-red-500/20',
        change: 'bg-blue-100 text-blue-700 dark:bg-blue-500/15 dark:text-blue-400 border-blue-200 dark:border-blue-500/20',
        maintain: 'bg-gray-100 text-gray-700 dark:bg-gray-500/15 dark:text-gray-400 border-gray-200 dark:border-gray-500/20',
        update: 'bg-gray-100 text-gray-700 dark:bg-gray-500/15 dark:text-gray-400 border-gray-200 dark:border-gray-500/20',
    };

    function getChangeTypeConfig(type: string) {
        switch (type) {
            case 'new':
                return { label: 'New', icon: Rocket, color: TYPE_COLORS.new };
            case 'fix':
                return { label: 'Fix', icon: Bug, color: TYPE_COLORS.fix };
            case 'change':
                return { label: 'Change', icon: Wrench, color: TYPE_COLORS.change };
            case 'maintain':
                return { label: 'Maintain', icon: PenTool, color: TYPE_COLORS.maintain };
            default:
                return { label: 'Update', icon: Rocket, color: TYPE_COLORS.update };
        }
    }
</script>

<svelte:head>
    <title>Changelog</title>
</svelte:head>

<AppLayout {breadcrumbs}>
    <LayoutComposer>
        {#snippet header()}
            <Heading title="Changelog" description="Riwayat pembaruan aplikasi" />
        {/snippet}

        <div class="space-y-6 max-w-4xl">
            {#if changelog.length === 0}
                <Empty.Root class="border border-dashed">
                    <Empty.Header>
                        <Empty.Media variant="icon">
                            <FileMinus class="h-10 w-10 text-muted-foreground opacity-20" />
                        </Empty.Media>
                        <Empty.Title>Tidak ada data changelog</Empty.Title>
                        <Empty.Description>Belum ada riwayat pembaruan yang tercatat.</Empty.Description>
                    </Empty.Header>
                </Empty.Root>
            {:else}
                {#each changelog as release (release.version)}
                    <Card>
                        <CardHeader>
                            <div class="flex items-center justify-between">
                                <div class="space-y-1">
                                    <CardTitle class="text-xl">{release.version}</CardTitle>
                                    <CardDescription>
                                        {new Date(release.date).toLocaleDateString('id-ID', {
                                            weekday: 'long',
                                            day: 'numeric',
                                            month: 'long',
                                            year: 'numeric',
                                        })}
                                    </CardDescription>
                                </div>
                            </div>
                        </CardHeader>
                        <CardContent>
                            <ul class="space-y-3">
                                {#each release.changes as change, i (i)}
                                    {@const config = getChangeTypeConfig(change.type)}
                                    <li class="flex items-start gap-3 text-sm">
                                        <Badge variant="outline" class={cn('shrink-0 mt-0.5 font-medium border-0', config.color)}>
                                            <config.icon class="mr-1 h-3 w-3" />
                                            {config.label}
                                        </Badge>
                                        <span class="text-muted-foreground leading-relaxed pt-0.5">{change.message}</span>
                                    </li>
                                {/each}
                            </ul>
                        </CardContent>
                    </Card>
                {/each}
            {/if}
        </div>
    </LayoutComposer>
</AppLayout>
