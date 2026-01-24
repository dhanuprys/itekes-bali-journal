<script lang="ts">
    import * as Dialog from '@/components/ui/dialog';
    import { Button } from '@/components/ui/button';
    import { Input } from '@/components/ui/input';
    import { Label } from '@/components/ui/label';
    import { Checkbox } from '@/components/ui/checkbox';
    import { Badge } from '@/components/ui/badge';
    import { router } from '@inertiajs/svelte';
    import { SearchIcon, CheckIcon, XIcon } from 'lucide-svelte';
    import { ScrollArea } from '@/components/ui/scroll-area';
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

{#if submission}
    <Dialog.Root bind:open>
        <Dialog.Content class="sm:max-w-[600px]">
            <Dialog.Header>
                <Dialog.Title>Atur Reviewer</Dialog.Title>
                <Dialog.Description class="space-y-1">
                    <p>Pilih reviewer untuk usulan:</p>
                    <p class="font-medium text-foreground line-clamp-2">
                        {submission.latest_detail?.title || 'Judul Tidak Tersedia'}
                    </p>
                    <div class="flex items-center gap-2 text-xs">
                        <span>Pengusul: <span class="font-medium">{submission.user?.name || 'Tidak Diketahui'}</span></span>
                        <span>•</span>
                        <Badge variant="outline" class="text-xs">
                            {submission.stage?.replace('_', ' ').toUpperCase() || 'N/A'}
                        </Badge>
                    </div>
                </Dialog.Description>
            </Dialog.Header>

            <div class="py-4 space-y-4">
                <!-- Search and Quick Actions -->
                <div class="flex gap-2">
                    <div class="relative flex-1">
                        <SearchIcon class="absolute left-2.5 top-2.5 h-4 w-4 text-muted-foreground" />
                        <Input placeholder="Cari reviewer..." class="pl-9" bind:value={searchQuery} />
                    </div>
                    <Button variant="outline" size="sm" onclick={selectAll} disabled={filteredReviewers.length === 0}>
                        <CheckIcon class="h-4 w-4 mr-1" />
                        Pilih Semua
                    </Button>
                </div>

                <!-- Reviewer List -->
                <div class="border rounded-md">
                    <ScrollArea class="h-[350px]">
                        {#if filteredReviewers.length === 0}
                            <div class="text-center text-sm text-muted-foreground py-12">
                                <SearchIcon class="h-8 w-8 mx-auto mb-2 opacity-50" />
                                <p>{searchQuery ? 'Reviewer tidak ditemukan.' : 'Tidak ada reviewer tersedia.'}</p>
                            </div>
                        {:else}
                            <div class="p-2 space-y-1">
                                {#each filteredReviewers as reviewer (reviewer.id)}
                                    {@const isSelected = selectedIds.includes(reviewer.id.toString())}
                                    <div
                                        role="button"
                                        tabindex="0"
                                        class="flex items-center gap-3 p-3 rounded-md hover:bg-muted/50 transition-colors cursor-pointer group"
                                        onclick={() => toggleSelection(reviewer.id.toString())}
                                        onkeydown={(e) => {
                                            if (e.key === 'Enter' || e.key === ' ') {
                                                e.preventDefault();
                                                toggleSelection(reviewer.id.toString());
                                            }
                                        }}
                                    >
                                        <Checkbox
                                            id={`reviewer-${reviewer.id}`}
                                            checked={isSelected}
                                            onCheckedChange={() => toggleSelection(reviewer.id.toString())}
                                        />
                                        <Avatar.Root class="h-10 w-10">
                                            <Avatar.Fallback class={isSelected ? 'bg-primary text-primary-foreground' : ''}>
                                                {getInitials(reviewer.name)}
                                            </Avatar.Fallback>
                                        </Avatar.Root>
                                        <div class="flex-1 min-w-0">
                                            <Label
                                                for={`reviewer-${reviewer.id}`}
                                                class="text-sm font-medium leading-none cursor-pointer block truncate"
                                            >
                                                {reviewer.name}
                                            </Label>
                                            <p class="text-xs text-muted-foreground mt-1 truncate">
                                                {reviewer.email}
                                            </p>
                                        </div>
                                        {#if isSelected}
                                            <CheckIcon class="h-4 w-4 text-primary" />
                                        {/if}
                                    </div>
                                {/each}
                            </div>
                        {/if}
                    </ScrollArea>
                </div>

                <!-- Selection Summary -->
                <div class="flex items-center justify-between px-1">
                    <div class="flex items-center gap-2">
                        <Badge variant={selectedIds.length > 0 ? 'default' : 'outline'}>
                            {selectedIds.length} reviewer dipilih
                        </Badge>
                        {#if selectedIds.length > 0 && reviewers.length > 0}
                            <span class="text-xs text-muted-foreground">
                                dari {reviewers.length} tersedia
                            </span>
                        {/if}
                    </div>
                    <Button variant="ghost" size="sm" onclick={deselectAll} disabled={selectedIds.length === 0}>
                        <XIcon class="h-3 w-3 mr-1" />
                        Reset
                    </Button>
                </div>
            </div>

            <Dialog.Footer>
                <Button variant="outline" onclick={() => (open = false)} disabled={processing}>Batal</Button>
                <Button onclick={save} disabled={processing}>
                    {processing ? 'Menyimpan...' : 'Simpan Perubahan'}
                </Button>
            </Dialog.Footer>
        </Dialog.Content>
    </Dialog.Root>
{/if}
