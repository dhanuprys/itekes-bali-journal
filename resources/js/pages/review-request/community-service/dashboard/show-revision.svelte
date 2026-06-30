<script lang="ts">
    import LayoutComposer from '@/layouts/layout-composer.svelte';
    import AppLayout from '@/layouts/app-layout.svelte';
    import { type BreadcrumbItem } from '@/types';
    import Heading from '@/components/heading.svelte';
    import * as Card from '@/components/ui/card';
    import { Button } from '@/components/ui/button';
    import { router } from '@inertiajs/svelte';
    import SubmissionDetailCard from '@/components/reviewer/submission-detail-card.svelte';

    let { submission, detail } = $props();

    let breadcrumbs: BreadcrumbItem[] = $derived([
        { title: 'Permintaan Review', href: '#' },
        { title: 'Pengabdian Masyarakat', href: '#' },
        { title: 'Riwayat Revisi', href: route('apply.community_service.revisions', submission.id) },
        { title: 'Detail Revisi', href: '#' },
    ]);

    function formatDate(dateString: string) {
        return new Date(dateString).toLocaleDateString('id-ID', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
        });
    }
</script>

<svelte:head>
    <title>Detail Revisi - {detail.title || 'Submission'}</title>
</svelte:head>

<AppLayout {breadcrumbs}>
    <LayoutComposer>
        {#snippet header()}
            <Heading title="Detail Revisi" description="Informasi lengkap revisi submission." />
        {/snippet}

        {#snippet actions()}
            <Button variant="outline" onclick={() => router.visit(route('apply.community_service.revisions', submission.id))}>
                Kembali ke Riwayat
            </Button>
        {/snippet}

        <div class="space-y-6">
            <Card.Root>
                <Card.Content class="pt-6">
                    <SubmissionDetailCard {detail} type="community-service" stage="final_report" />
                </Card.Content>
            </Card.Root>

            {#if detail.reviewers && detail.reviewers.length > 0}
                <Card.Root>
                    <Card.Header>
                        <Card.Title>Reviewer Terafiliasi</Card.Title>
                        <Card.Description>Reviewer yang ditugaskan untuk revisi ini.</Card.Description>
                    </Card.Header>
                    <Card.Content>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            {#each detail.reviewers as reviewer (reviewer.id)}
                                <div class="flex items-center gap-3 p-3 border rounded-lg bg-card shadow-sm">
                                    <div class="h-10 w-10 rounded-full bg-primary/10 flex items-center justify-center text-primary font-semibold">
                                        {reviewer.user?.name?.charAt(0) || 'R'}
                                    </div>
                                    <div>
                                        <p class="font-medium">{reviewer.user?.name || 'Reviewer'}</p>
                                        <p class="text-xs text-muted-foreground">{reviewer.user?.email || '-'}</p>
                                    </div>
                                </div>
                            {/each}
                        </div>
                    </Card.Content>
                </Card.Root>
            {/if}

            {#if detail.comments && detail.comments.length > 0}
                <Card.Root>
                    <Card.Header>
                        <Card.Title>Komentar Reviewer</Card.Title>
                        <Card.Description>Komentar dan umpan balik dari reviewer</Card.Description>
                    </Card.Header>
                    <Card.Content>
                        <div class="space-y-4">
                            {#each detail.comments as comment (comment.id)}
                                <div class="border rounded-lg p-4 bg-muted/30">
                                    <div class="flex items-start justify-between mb-2">
                                        <div>
                                            <p class="font-medium">{comment.user?.name || 'Reviewer'}</p>
                                            <p class="text-xs text-muted-foreground">
                                                {formatDate(comment.created_at)}
                                            </p>
                                        </div>
                                    </div>
                                    <p class="text-sm whitespace-pre-wrap">{comment.content}</p>
                                </div>
                            {/each}
                        </div>
                    </Card.Content>
                </Card.Root>
            {/if}
        </div>
    </LayoutComposer>
</AppLayout>
