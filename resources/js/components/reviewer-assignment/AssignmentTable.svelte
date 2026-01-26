<script lang="ts">
    import * as Table from '@/components/ui/table';
    import { Badge } from '@/components/ui/badge';
    import { Button } from '@/components/ui/button';
    import { Input } from '@/components/ui/input';
    import { Edit2Icon, SearchIcon, FilterIcon } from 'lucide-svelte';
    import * as Avatar from '@/components/ui/avatar';
    import * as Tooltip from '@/components/ui/tooltip';
    import * as Select from '@/components/ui/select';
    import ReviewerAssignmentSheet from './ReviewerAssignmentSheet.svelte';
    import { router } from '@inertiajs/svelte';
    import { debounce } from 'lodash-es';

    interface Props {
        submissions: any;
        reviewers: any[];
        assignRouteName: string;
        filters: {
            search?: string;
            status?: string;
        };
    }

    let { submissions, reviewers, assignRouteName, filters }: Props = $props();

    let dialogOpen = $state(false);
    let selectedSubmission = $state<any>(null);

    // Use $state.raw to create a reactive object that can be mutated
    const propSearch = $derived(filters.search || '');
    const propStatus = $derived(filters.status || 'all');

    // Create local writable state for bindings
    let localSearch = $state('');
    let localStatus = $state('all');

    // Sync derived values to local state when they change
    $effect(() => {
        localSearch = propSearch;
        localStatus = propStatus;
    });

    const statusOptions = [
        { value: 'all', label: 'Semua Status' },
        { value: 'need_review', label: 'Need Review' },
        { value: 'approved', label: 'Approved' },
        { value: 'rejected', label: 'Rejected' },
        { value: 'revision_needed', label: 'Revision Needed' },
    ];

    const statusFilterLabel = $derived(statusOptions.find((s) => s.value === localStatus)?.label ?? 'Filter Status');

    // Create debounced function for search
    const debouncedSearch = debounce(() => {
        applyFilters();
    }, 300);

    // Watch for search changes with debounce
    $effect(() => {
        localSearch;
        debouncedSearch();
    });

    // Watch for status filter changes (instant)
    $effect(() => {
        localStatus;
        applyFilters();
    });

    function applyFilters() {
        router.get(
            window.location.pathname,
            {
                search: localSearch || undefined,
                status: localStatus !== 'all' ? localStatus : undefined,
            },
            {
                preserveState: true,
                preserveScroll: true,
                only: ['submissions'],
            },
        );
    }

    function openAssignment(submission: any) {
        selectedSubmission = submission;
        dialogOpen = true;
    }

    function getInitials(name: string) {
        return name
            .split(' ')
            .map((n) => n[0])
            .slice(0, 2)
            .join('')
            .toUpperCase();
    }

    function getStageLabel(stage: string) {
        const labels: Record<string, string> = {
            proposal: 'Usulan Awal',
            progress_report: 'Laporan Kemajuan',
            final_report: 'Laporan Akhir',
        };
        return labels[stage] || stage;
    }

    function getStatusVariant(status: string) {
        const variants: Record<string, any> = {
            need_review: 'default',
            approved: 'secondary',
            rejected: 'destructive',
            revision_needed: 'outline',
        };
        return variants[status] || 'outline';
    }
</script>

<div class="space-y-4">
    <!-- Filters -->
    <div class="flex flex-col sm:flex-row gap-4">
        <div class="relative flex-1">
            <SearchIcon class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" />
            <Input placeholder="Cari berdasarkan judul atau pengusul..." class="pl-9" bind:value={localSearch} />
        </div>
        <Select.Root type="single" bind:value={localStatus}>
            <Select.Trigger class="w-full sm:w-[200px]">
                <FilterIcon class="h-4 w-4 mr-2" />
                {statusFilterLabel}
            </Select.Trigger>
            <Select.Content>
                {#each statusOptions as option}
                    <Select.Item value={option.value} label={option.label}>
                        {option.label}
                    </Select.Item>
                {/each}
            </Select.Content>
        </Select.Root>
    </div>

    <!-- Results Summary -->
    <div class="flex items-center justify-between text-sm text-muted-foreground">
        <span>Menampilkan {submissions.data.length} dari {submissions.total} usulan</span>
    </div>

    <!-- Table -->
    <div class="rounded-md border">
        <Table.Root>
            <Table.Header>
                <Table.Row>
                    <Table.Head class="w-[40%]">Judul</Table.Head>
                    <Table.Head>Pengusul</Table.Head>
                    <Table.Head>Tahap</Table.Head>
                    <Table.Head>Status</Table.Head>
                    <Table.Head>Reviewer</Table.Head>
                    <Table.Head class="text-right w-[100px]">Aksi</Table.Head>
                </Table.Row>
            </Table.Header>
            <Table.Body>
                {#if submissions.data.length === 0}
                    <Table.Row>
                        <Table.Cell colspan={6} class="text-center h-32">
                            <div class="flex flex-col items-center justify-center gap-2 text-muted-foreground">
                                <SearchIcon class="h-8 w-8 opacity-50" />
                                <p class="text-sm">
                                    {localSearch || localStatus !== 'all' ? 'Tidak ada hasil yang sesuai dengan filter' : 'Tidak ada data'}
                                </p>
                            </div>
                        </Table.Cell>
                    </Table.Row>
                {:else}
                    {#each submissions.data as submission}
                        <Table.Row class="group hover:bg-muted/50 transition-colors">
                            <Table.Cell class="font-medium">
                                <div class="max-w-md">
                                    <p class="truncate" title={submission.latest_detail?.title || submission.latest_detail?.final_title}>
                                        {submission.latest_detail?.title || submission.latest_detail?.final_title || 'Judul Tidak Tersedia'}
                                    </p>
                                </div>
                            </Table.Cell>
                            <Table.Cell>
                                <div class="flex items-center gap-2">
                                    <Avatar.Root class="h-8 w-8">
                                        <Avatar.Fallback class="text-xs">
                                            {getInitials(submission.user?.name || 'U')}
                                        </Avatar.Fallback>
                                    </Avatar.Root>
                                    <span class="text-sm">{submission.user?.name}</span>
                                </div>
                            </Table.Cell>
                            <Table.Cell>
                                <Badge variant="outline" class="font-normal">
                                    {getStageLabel(submission.stage)}
                                </Badge>
                            </Table.Cell>
                            <Table.Cell>
                                <Badge variant={getStatusVariant(submission.status)}>
                                    {submission.status.replace('_', ' ').toUpperCase()}
                                </Badge>
                            </Table.Cell>
                            <Table.Cell>
                                <div class="flex items-center gap-2">
                                    {#if submission.reviewers.length === 0}
                                        <div class="flex items-center gap-1.5 text-sm text-muted-foreground">
                                            <div class="h-2 w-2 rounded-full bg-orange-500"></div>
                                            <span class="italic">Belum ditugaskan</span>
                                        </div>
                                    {:else}
                                        <div class="flex -space-x-2">
                                            {#each submission.reviewers.slice(0, 3) as reviewer}
                                                <Tooltip.Root>
                                                    <Tooltip.Trigger>
                                                        <Avatar.Root class="h-8 w-8 border-2 border-background ring-1 ring-border">
                                                            <Avatar.Fallback class="text-xs">
                                                                {getInitials(reviewer.user.name)}
                                                            </Avatar.Fallback>
                                                        </Avatar.Root>
                                                    </Tooltip.Trigger>
                                                    <Tooltip.Content>
                                                        <p class="text-xs">{reviewer.user.name}</p>
                                                        <p class="text-xs text-muted-foreground">{reviewer.user.email}</p>
                                                    </Tooltip.Content>
                                                </Tooltip.Root>
                                            {/each}
                                            {#if submission.reviewers.length > 3}
                                                <Tooltip.Root>
                                                    <Tooltip.Trigger>
                                                        <div
                                                            class="h-8 w-8 rounded-full border-2 border-background ring-1 ring-border bg-muted flex items-center justify-center"
                                                        >
                                                            <span class="text-xs font-medium">+{submission.reviewers.length - 3}</span>
                                                        </div>
                                                    </Tooltip.Trigger>
                                                    <Tooltip.Content>
                                                        <p class="text-xs font-medium mb-1">Reviewer lainnya:</p>
                                                        {#each submission.reviewers.slice(3) as reviewer}
                                                            <p class="text-xs">{reviewer.user.name}</p>
                                                        {/each}
                                                    </Tooltip.Content>
                                                </Tooltip.Root>
                                            {/if}
                                        </div>
                                        <span class="text-xs text-muted-foreground ml-1">
                                            ({submission.reviewers.length})
                                        </span>
                                    {/if}
                                </div>
                            </Table.Cell>
                            <Table.Cell class="text-right">
                                <Button
                                    variant="ghost"
                                    size="sm"
                                    onclick={() => openAssignment(submission)}
                                    class="opacity-0 group-hover:opacity-100 transition-opacity"
                                >
                                    <Edit2Icon class="h-4 w-4" />
                                </Button>
                            </Table.Cell>
                        </Table.Row>
                    {/each}
                {/if}
            </Table.Body>
        </Table.Root>
    </div>

    <!-- Pagination -->
    {#if submissions.links && submissions.links.length > 3}
        <div class="flex items-center justify-between">
            <div class="text-sm text-muted-foreground">
                Halaman {submissions.current_page} dari {submissions.last_page}
            </div>
            <div class="flex gap-2">
                {#each submissions.links as link}
                    {#if link.url}
                        <Button variant={link.active ? 'default' : 'outline'} size="sm" onclick={() => router.visit(link.url)} disabled={!link.url}>
                            {@html link.label}
                        </Button>
                    {/if}
                {/each}
            </div>
        </div>
    {/if}
</div>

{#if selectedSubmission}
    <ReviewerAssignmentSheet bind:open={dialogOpen} submission={selectedSubmission} {reviewers} {assignRouteName} />
{/if}
