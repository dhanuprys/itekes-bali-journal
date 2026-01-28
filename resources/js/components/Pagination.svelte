<script lang="ts">
    import * as Pagination from '@/components/ui/pagination';
    import { ChevronLeft, ChevronRight } from 'lucide-svelte';
    import { router } from '@inertiajs/svelte';

    // Flexible props to handle different Laravel Pagination responses (Cursor, LengthAware, API Resources)
    let { meta = {}, data = {} } = $props();

    // Resolve valid meta and links from various possible structures
    let resolvedMeta = $derived(meta.total ? meta : data.meta || data);

    let currentPageVal = $derived(resolvedMeta.current_page || 1);

    let perPage = $derived(resolvedMeta.per_page || 10);
    let total = $derived(resolvedMeta.total || 0);

    function getPageUrl(pageNumber: number) {
        if (typeof window === 'undefined') return '#';
        const url = new URL(window.location.href);
        url.searchParams.set('page', pageNumber.toString());
        return url.toString();
    }

    function handlePageChange(newPage: number) {
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

{#if total > perPage}
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
