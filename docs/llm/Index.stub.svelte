<script lang="ts">
    import AppLayout from '@/layouts/AppLayout.svelte';
    import { type BreadcrumbItem } from '@/types';

    // export interface BreadcrumbItem {
    //     title: string;
    //     href: string;
    // }
    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: 'Pengguna',
            href: '/users',
        },
    ];
</script>

<svelte:head>
    <title>Pengguna</title>
</svelte:head>

<AppLayout {breadcrumbs}>
    <div class="space-y-4 px-4 pt-4 overflow-x-auto">
        <!-- content goes here -->
    </div>
</AppLayout>
