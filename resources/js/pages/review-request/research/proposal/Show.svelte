<script lang="ts">
    import LayoutComposer from '@/layouts/LayoutComposer.svelte';
    import AppLayout from '@/layouts/AppLayout.svelte';
    import { type BreadcrumbItem } from '@/types';
    import Heading from '@/components/Heading.svelte';
    import * as Card from '@/components/ui/card';
    import { Badge } from '@/components/ui/badge';
    import { Button } from '@/components/ui/button';
    import { getStatusConfig } from '@/lib/review-status';
    import SubmissionDetailCard from '@/components/reviewer/SubmissionDetailCard.svelte';
    import { Edit } from 'lucide-svelte';

    let { submission } = $props();

    // Derived from submission.latest_detail
    let detail = $derived(submission.latest_detail);

    const breadcrumbs: BreadcrumbItem[] = [
        { title: 'Permintaan Review', href: '#' },
        { title: 'Penelitian', href: '#' },
        { title: 'Proposal', href: '/apply/research/proposal' },
        { title: 'Detail', href: '#' },
    ];

    let statusConfig = $derived(getStatusConfig(submission.status));
</script>

<svelte:head>
    <title>Detail Proposal</title>
</svelte:head>

<AppLayout {breadcrumbs}>
    <LayoutComposer>
        {#snippet header()}
            <Heading title="Detail Proposal" description="Informasi lengkap proposal penelitian." />
        {/snippet}

        {#snippet actions()}
            <div class="flex items-center gap-2">
                {#if submission.status === 'revision_needed'}
                    <Button href={route('apply.research.proposal.edit', submission.id)} size="sm" class="gap-2">
                        <Edit class="h-4 w-4" />
                        Revisi Proposal
                    </Button>
                {/if}
                <Badge variant="outline" class={'px-3 py-1 gap-2 flex items-center ' + statusConfig.color}>
                    <statusConfig.icon class="h-4 w-4" />
                    {statusConfig.label}
                </Badge>
            </div>
        {/snippet}

        {#snippet children()}
            <div class="space-y-6">
                <Card.Root>
                    <Card.Content class="pt-6">
                        <SubmissionDetailCard {detail} type="research" stage="proposal" />
                    </Card.Content>
                </Card.Root>

                <!-- Placeholder for Review History or Comments -->
                <Card.Root>
                    <Card.Header>
                        <Card.Title>Riwayat Review</Card.Title>
                    </Card.Header>
                    <Card.Content>
                        <p class="text-muted-foreground text-sm">Belum ada riwayat review.</p>
                    </Card.Content>
                </Card.Root>
            </div>
        {/snippet}
    </LayoutComposer>
</AppLayout>
