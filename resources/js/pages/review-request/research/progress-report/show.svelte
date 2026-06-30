<script lang="ts">
    import LayoutComposer from '@/layouts/layout-composer.svelte';
    import AppLayout from '@/layouts/app-layout.svelte';
    import { type BreadcrumbItem } from '@/types';
    import Heading from '@/components/heading.svelte';
    import { Button } from '@/components/ui/button';
    import * as Card from '@/components/ui/card';
    import { Badge } from '@/components/ui/badge';
    import { getStatusConfig } from '@/lib/review-status';
    import SubmissionDetailCard from '@/components/reviewer/submission-detail-card.svelte';
    import { Edit } from 'lucide-svelte';

    let { submission } = $props();
    let detail = $derived(submission.latest_detail);

    const breadcrumbs: BreadcrumbItem[] = [
        { title: 'Permintaan Review', href: '#' },
        { title: 'Penelitian', href: '#' },
        { title: 'Laporan Kemajuan', href: route('apply.research.progress_report.index') },
        { title: 'Detail', href: '#' },
    ];

    let statusConfig = $derived(getStatusConfig(submission.status));
</script>

<svelte:head>
    <title>Detail Laporan - {detail?.final_title || 'Laporan'}</title>
</svelte:head>

<AppLayout {breadcrumbs}>
    <LayoutComposer>
        {#snippet header()}
            <Heading title="Detail Laporan" description="Informasi lengkap laporan kemajuan/akhir." />
        {/snippet}

        {#snippet actions()}
            <div class="flex items-center gap-2">
                {#if submission.status === 'revision_needed'}
                    <Button href={route('apply.research.progress_report.edit', submission.id)} size="sm" class="gap-2">
                        <Edit class="h-4 w-4" />
                        Revisi Laporan
                    </Button>
                {/if}
                <Badge variant="outline" class={'px-3 py-1 gap-2 flex items-center ' + statusConfig.color}>
                    <statusConfig.icon class="h-4 w-4" />
                    {statusConfig.label}
                </Badge>
            </div>
        {/snippet}

        <Card.Root>
            <Card.Content class="pt-6">
                <SubmissionDetailCard {detail} type="research" stage="progress_report" />
            </Card.Content>
        </Card.Root>
    </LayoutComposer>
</AppLayout>
