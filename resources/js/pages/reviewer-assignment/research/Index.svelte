<script lang="ts">
    import LayoutComposer from '@/layouts/LayoutComposer.svelte';
    import AppLayout from '@/layouts/AppLayout.svelte';
    import { type BreadcrumbItem } from '@/types';
    import Heading from '@/components/Heading.svelte';
    import AssignmentTable from '@/components/reviewer-assignment/AssignmentTable.svelte';

    let { submissions, reviewers, filters } = $props();

    const breadcrumbs: BreadcrumbItem[] = [
        { title: 'Penugasan Reviewer', href: '#' },
        { title: 'Penelitian', href: '#' },
    ];
</script>

<svelte:head>
    <title>Atur Reviewer Penelitian</title>
</svelte:head>

<AppLayout {breadcrumbs}>
    <LayoutComposer>
        {#snippet header()}
            <Heading title="Atur Reviewer Penelitian" description="Tetapkan reviewer untuk setiap usulan penelitian." />
        {/snippet}

        {#snippet children()}
            <div class="bg-background shadow-sm rounded-lg">
                <AssignmentTable {submissions} {reviewers} {filters} assignRouteName="reviewer_assignment.research.store" />
            </div>
        {/snippet}
    </LayoutComposer>
</AppLayout>
