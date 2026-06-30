<script lang="ts">
    import * as Pagination from '@/components/ui/pagination';
    import { ChevronLeft, ChevronRight } from 'lucide-svelte';
    import { router } from '@inertiajs/svelte';

    // We accept `links` (from Laravel's paginator.links, though we might not use it directly if we use Shadcn's calculator)
    // and `meta` (which is usually the paginator object itself or the meta key in API resources).
    let { meta = {}, data = {} } = $props();

    // Helper to extract the core pagination fields safely
    let resolvedMeta = $derived.by(() => {
        // If 'meta' has 'total' (LengthAwarePaginator root), use it.
        if (meta && typeof meta.total !== 'undefined') {
            return meta;
        }
        // If 'data' is passed (sometimes API resources wrap it in data: { ... }, meta: { ... })
        if (data && data.meta) {
            return data.meta;
        }
        if (data && typeof data.total !== 'undefined') {
            return data;
        }
        return {};
    });

    let currentPageVal = $derived(resolvedMeta.current_page || 1);
    let perPage = $derived(resolvedMeta.per_page || 15);
    let total = $derived(resolvedMeta.total || 0);

    // If total is 0 or less than perPage, usually we don't show pagination,
    // but sometimes we might want to show if there's at least 1 page?
    // Standard practice: hide if total <= perPage or empty.
    let showPagination = $derived(total > perPage);

    function getPageUrl(pageNumber: number) {
        if (typeof window === 'undefined') return '';
        const url = new URL(window.location.href);
        url.searchParams.set('page', pageNumber.toString());
        return url.toString();
    }

    function handlePageChange(newPage: number) {
        // Prevent redundant requests
        if (newPage === currentPageVal) return;

        router.get(
            getPageUrl(newPage),
            {},
            {
                preserveState: true,
                preserveScroll: true,
            },
        );
    }
</script>

{#if showPagination}
    <Pagination.Root count={total} {perPage} page={currentPageVal} onPageChange={handlePageChange}>
        {#snippet children({ pages, currentPage })}
            <Pagination.Content>
                <Pagination.Item>
                    <Pagination.PrevButton>
                        <ChevronLeft class="h-4 w-4" />
                        <span class="hidden sm:block">Previous</span>
                    </Pagination.PrevButton>
                </Pagination.Item>

                {#each pages as page (page.key)}
                    {#if page.type === 'ellipsis'}
                        <Pagination.Item>
                            <Pagination.Ellipsis />
                        </Pagination.Item>
                    {:else}
                        <Pagination.Item>
                            <Pagination.Link {page} isActive={currentPage === page.value}>
                                {page.value}
                            </Pagination.Link>
                        </Pagination.Item>
                    {/if}
                {/each}

                <Pagination.Item>
                    <Pagination.NextButton>
                        <span class="hidden sm:block">Next</span>
                        <ChevronRight class="h-4 w-4" />
                    </Pagination.NextButton>
                </Pagination.Item>
            </Pagination.Content>
        {/snippet}
    </Pagination.Root>
{/if}
