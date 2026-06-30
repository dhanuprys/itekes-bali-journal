<script lang="ts">
    import AppearanceTabs from '@/components/appearance-tabs.svelte';
    import HeadingSmall from '@/components/heading-small.svelte';
    import { type BreadcrumbItem } from '@/types';

    import AppLayout from '@/layouts/app-layout.svelte';
    import SettingsLayout from '@/layouts/settings/layout.svelte';

    const breadcrumbItems: BreadcrumbItem[] = [
        {
            title: 'Tampilan',
            href: '/settings/appearance',
        },
    ];
</script>

<svelte:head>
    <title>Tampilan</title>
</svelte:head>

<AppLayout breadcrumbs={breadcrumbItems}>
    <SettingsLayout>
        <div class="space-y-6">
            <HeadingSmall title="Tampilan" description="Update tampilan akun Anda" />
            <AppearanceTabs />
        </div>
    </SettingsLayout>
</AppLayout>
