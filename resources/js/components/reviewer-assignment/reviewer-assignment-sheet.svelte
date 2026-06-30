<script lang="ts">
    import * as Sheet from '@/components/ui/sheet';
    import { Button } from '@/components/ui/button';
    import { Input } from '@/components/ui/input';
    import { Label } from '@/components/ui/label';
    import { Checkbox } from '@/components/ui/checkbox';
    import { Badge } from '@/components/ui/badge';
    import { router } from '@inertiajs/svelte';
    import { SearchIcon, UserIcon } from 'lucide-svelte';
    import * as Avatar from '@/components/ui/avatar';

    interface Reviewer {
        id: number;
        name: string;
        email: string;
    }

    interface SubmissionReviewer {
        user_id: number;
        user: {
            name: string;
            email: string;
        };
    }

    interface Submission {
        id: number;
        stage: string;
        latest_detail?: {
            title?: string;
        };
        user?: {
            name?: string;
        };
        reviewers: SubmissionReviewer[];
    }

    interface Props {
        open?: boolean;
        submission?: Submission | null;
        reviewers?: Reviewer[];
        assignRouteName: string;
    }

    let { open = $bindable(false), submission = null, reviewers = [], assignRouteName }: Props = $props();

    let searchQuery = $state('');
    let selectedIds = $state<string[]>([]);
    let processing = $state(false);

    // Initialize selectedIds when dialog opens and submission is available
    $effect(() => {
        if (open && submission && submission.reviewers) {
            selectedIds = submission.reviewers.map((r) => r.user_id.toString());
        } else if (!open) {
            // Reset search when dialog closes
            searchQuery = '';
        }
    });

    const filteredReviewers = $derived(
        (reviewers || []).filter(
            (r) =>
                searchQuery === '' ||
                r.name.toLowerCase().includes(searchQuery.toLowerCase()) ||
                r.email.toLowerCase().includes(searchQuery.toLowerCase()),
        ),
    );

    function toggleSelection(id: string) {
        if (selectedIds.includes(id)) {
            selectedIds = selectedIds.filter((i) => i !== id);
        } else {
            selectedIds = [...selectedIds, id];
        }
    }

    function selectAll() {
        selectedIds = filteredReviewers.map((r) => r.id.toString());
    }

    function deselectAll() {
        selectedIds = [];
    }

    function save() {
        if (!submission) return;

        processing = true;
        router.post(
            route(assignRouteName, submission.id),
            {
                reviewers: selectedIds,
            },
            {
                onFinish: () => {
                    processing = false;
                    open = false;
                },
                preserveScroll: true,
            },
        );
    }

    function getInitials(name: string) {
        if (!name) return 'U';
        return name
            .split(' ')
            .map((n) => n[0])
            .filter(Boolean)
            .slice(0, 2)
            .join('')
            .toUpperCase();
    }
</script>

<Sheet.Root bind:open>
    <Sheet.Content class="sm:max-w-[500px] flex flex-col p-0 gap-0">
        <Sheet.Header class="p-6 pb-2 border-b-0">
            <Sheet.Title>Atur Reviewer</Sheet.Title>
            <Sheet.Description>Kelola penugasan reviewer untuk usulan ini.</Sheet.Description>
        </Sheet.Header>

        {#if submission}
            <div class="flex-1 overflow-y-auto">
                <div class="p-6 pt-2 space-y-6">
                    <!-- Submission Details -->
                    <div class="space-y-3 p-4 bg-muted/40 rounded-lg border">
                        <div class="space-y-1">
                            <h4 class="text-sm font-medium text-muted-foreground">Judul Usulan</h4>
                            <p class="text-sm font-medium leading-relaxed">
                                {submission.latest_detail?.title || 'Judul Tidak Tersedia'}
                            </p>
                        </div>
                        <div class="flex items-center gap-4 text-xs">
                            <div class="flex items-center gap-1.5 min-w-0">
                                <UserIcon class="h-3.5 w-3.5 text-muted-foreground" />
                                <span class="truncate">{submission.user?.name || 'Tidak Diketahui'}</span>
                            </div>
                            <Badge variant="outline" class="text-[10px] h-5 px-2">
                                {submission.stage?.replace('_', ' ').toUpperCase() || 'N/A'}
                            </Badge>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <Label class="text-base">Pilih Reviewer</Label>
                            <div class="flex gap-2">
                                <Button variant="ghost" size="sm" onclick={selectAll} disabled={filteredReviewers.length === 0} class="h-8 text-xs">
                                    Pilih Semua
                                </Button>
                                <Button
                                    variant="ghost"
                                    size="sm"
                                    onclick={deselectAll}
                                    disabled={selectedIds.length === 0}
                                    class="h-8 text-xs text-muted-foreground hover:text-foreground"
                                >
                                    Reset
                                </Button>
                            </div>
                        </div>

                        <!-- Search -->
                        <div class="relative">
                            <SearchIcon class="absolute left-3 top-2.5 h-4 w-4 text-muted-foreground" />
                            <Input placeholder="Cari reviewer..." class="pl-9" bind:value={searchQuery} />
                        </div>

                        <!-- Reviewer List -->
                        <div class="space-y-1">
                            {#if filteredReviewers.length === 0}
                                <div class="text-center text-sm text-muted-foreground py-8 border rounded-lg border-dashed">
                                    <SearchIcon class="h-8 w-8 mx-auto mb-2 opacity-50" />
                                    <p>{searchQuery ? 'Reviewer tidak ditemukan.' : 'Tidak ada reviewer tersedia.'}</p>
                                </div>
                            {:else}
                                {#each filteredReviewers as reviewer (reviewer.id)}
                                    {@const isSelected = selectedIds.includes(reviewer.id.toString())}
                                    <div
                                        role="button"
                                        tabindex="0"
                                        class="flex items-start gap-3 p-3 rounded-lg border transition-all duration-200 cursor-pointer group hover:bg-muted/50 {isSelected
                                            ? 'bg-primary/5 border-primary/50'
                                            : 'border-transparent bg-card shadow-sm'}"
                                        onclick={() => toggleSelection(reviewer.id.toString())}
                                        onkeydown={(e) => {
                                            if (e.key === 'Enter' || e.key === ' ') {
                                                e.preventDefault();
                                                toggleSelection(reviewer.id.toString());
                                            }
                                        }}
                                    >
                                        <Checkbox id={`reviewer-${reviewer.id}`} checked={isSelected} class="mt-1 pointer-events-none" />
                                        <div class="flex-1 flex items-start gap-3">
                                            <Avatar.Root class="h-9 w-9 border">
                                                <Avatar.Fallback class={isSelected ? 'bg-primary/10 text-primary' : ''}>
                                                    {getInitials(reviewer.name)}
                                                </Avatar.Fallback>
                                            </Avatar.Root>
                                            <div class="space-y-0.5">
                                                <Label class="text-sm font-medium cursor-pointer block pointer-events-none">
                                                    {reviewer.name}
                                                </Label>
                                                <p class="text-xs text-muted-foreground">
                                                    {reviewer.email}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                {/each}
                            {/if}
                        </div>
                    </div>
                </div>
            </div>

            <div class="p-6 border-t bg-background mt-auto">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-sm text-muted-foreground">
                        {selectedIds.length} reviewer dipilih
                    </span>
                </div>
                <div class="flex gap-3">
                    <Button variant="outline" class="flex-1" onclick={() => (open = false)} disabled={processing}>Batal</Button>
                    <Button class="flex-1" onclick={save} disabled={processing}>
                        {processing ? 'Menyimpan...' : 'Simpan Perubahan'}
                    </Button>
                </div>
            </div>
        {/if}
    </Sheet.Content>
</Sheet.Root>
