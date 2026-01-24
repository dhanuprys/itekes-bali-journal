<script lang="ts">
    import LayoutComposer from '@/layouts/LayoutComposer.svelte';
    import AppLayout from '@/layouts/AppLayout.svelte';
    import { type BreadcrumbItem } from '@/types';
    import Heading from '@/components/Heading.svelte';
    import * as Card from '@/components/ui/card';
    import { Badge } from '@/components/ui/badge';
    import { Button } from '@/components/ui/button';
    import { Separator } from '@/components/ui/separator';
    import { FileTextIcon } from 'lucide-svelte';
    import CommentSection from '@/components/reviewer/CommentSection.svelte';
    import ReviewActions from '@/components/reviewer/ReviewActions.svelte';

    let { submission, comments } = $props();
    let detail = $derived(submission.latest_detail);
    let canReview = $derived(submission.status === 'need_review');

    const breadcrumbs: BreadcrumbItem[] = [
        { title: 'Reviewer Area', href: '#' },
        { title: 'Pengabdian Masyarakat', href: route('review.community_service.index') },
        { title: 'Review Laporan Akhir', href: '#' },
    ];
</script>

<svelte:head>
    <title>Review Laporan Akhir</title>
</svelte:head>

<AppLayout {breadcrumbs}>
    <LayoutComposer>
        {#snippet header()}
            <div class="flex items-center justify-between">
                <Heading title="Review Laporan Akhir" description="Tinjau laporan akhir pengabdian berikut." />
                <Badge variant={canReview ? 'default' : 'outline'} class="text-sm px-3 py-1">
                    {submission.status.replace('_', ' ').toUpperCase()}
                </Badge>
            </div>
        {/snippet}

        {#snippet children()}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Left Column -->
                <div class="lg:col-span-2 space-y-6">
                    <Card.Root>
                        <Card.Header>
                            <Card.Title>Identitas Laporan</Card.Title>
                        </Card.Header>
                        <Card.Content>
                            <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <div class="sm:col-span-2">
                                    <dt class="text-sm font-medium text-muted-foreground">Judul Akhir</dt>
                                    <dd class="text-lg font-semibold">{detail?.final_title || detail?.title}</dd>
                                </div>

                                <div>
                                    <dt class="text-sm font-medium text-muted-foreground">Ketua Pelaksana</dt>
                                    <dd class="text-base font-semibold">{detail?.final_leader_name || detail?.leader_name}</dd>
                                </div>

                                <div>
                                    <dt class="text-sm font-medium text-muted-foreground">NIDN/NIP</dt>
                                    <dd class="text-base font-semibold">{detail?.leader_nidn}</dd>
                                </div>

                                <div class="sm:col-span-2">
                                    <dt class="text-sm font-medium text-muted-foreground">Anggota Tim</dt>
                                    <dd class="mt-1">
                                        {#if detail?.members && detail.members.length > 0}
                                            <ul class="list-disc list-inside text-sm">
                                                {#each detail.members as member}
                                                    <li>{member.name}</li>
                                                {/each}
                                            </ul>
                                        {:else}
                                            -
                                        {/if}
                                    </dd>
                                </div>
                            </dl>

                            <Separator class="my-6" />

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <h4 class="text-sm font-medium mb-2">Laporan Akhir</h4>
                                    {#if detail?.final_report_path}
                                        <div class="flex items-center gap-3 p-3 border rounded bg-muted/50">
                                            <FileTextIcon class="h-6 w-6 text-primary" />
                                            <div class="flex-1 overflow-hidden">
                                                <p class="text-sm truncate">File Laporan</p>
                                            </div>
                                            <Button href={`/storage/${detail?.final_report_path}`} target="_blank" variant="outline" size="sm"
                                                >Unduh</Button
                                            >
                                        </div>
                                    {:else}
                                        <p class="text-sm text-muted-foreground">Tidak tersedia</p>
                                    {/if}
                                </div>
                                <div>
                                    <h4 class="text-sm font-medium mb-2">Manuskrip</h4>
                                    {#if detail?.manuscript_path}
                                        <div class="flex items-center gap-3 p-3 border rounded bg-muted/50">
                                            <FileTextIcon class="h-6 w-6 text-primary" />
                                            <div class="flex-1 overflow-hidden">
                                                <p class="text-sm truncate">File Manuskrip</p>
                                            </div>
                                            <Button href={`/storage/${detail?.manuscript_path}`} target="_blank" variant="outline" size="sm"
                                                >Unduh</Button
                                            >
                                        </div>
                                    {:else}
                                        <p class="text-sm text-muted-foreground">Tidak tersedia</p>
                                    {/if}
                                </div>
                            </div>
                        </Card.Content>
                    </Card.Root>
                </div>

                <!-- Right Column -->
                <div class="space-y-6">
                    <CommentSection {comments} submitRoute={route('review.community_service.final_report.comment', submission.id)} />

                    <ReviewActions {canReview} submitRoute={route('review.community_service.final_report.change-state', submission.id)} />
                </div>
            </div>
        {/snippet}
    </LayoutComposer>
</AppLayout>
