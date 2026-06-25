<script lang="ts">
    import LayoutComposer from '@/layouts/LayoutComposer.svelte';
    import AppLayout from '@/layouts/AppLayout.svelte';
    import { type BreadcrumbItem } from '@/types';
    import Heading from '@/components/Heading.svelte';
    import AssignmentTable from '@/components/reviewer-assignment/AssignmentTable.svelte';
    import { Button } from '@/components/ui/button';
    import { Sheet } from 'lucide-svelte';

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

        {#snippet actions()}
            <Button href={route('reviewer_assignment.research.export')} class="bg-green-600 hover:bg-green-700 text-white">
                <Sheet class="mr-2 h-4 w-4" />
                Export Rekap (XLSX)
            </Button>
        {/snippet}

        <div class="bg-background">
            <AssignmentTable {submissions} {reviewers} {filters} assignRouteName="reviewer_assignment.research.store" />
        </div>
    </LayoutComposer>
</AppLayout>
