<script lang="ts">
    import LayoutComposer from '@/layouts/layout-composer.svelte';
    import AppLayout from '@/layouts/app-layout.svelte';
    import { type BreadcrumbItem } from '@/types';
    import Heading from '@/components/heading.svelte';
    import * as Card from '@/components/ui/card';
    import { Button } from '@/components/ui/button';
    import { router } from '@inertiajs/svelte';
    import { FileTextIcon, UploadIcon, CheckCircleIcon } from 'lucide-svelte';

    let { proposalCount = 0, waitForOutputCount = 0, outputCompletedCount = 0, verificationCount = 0 } = $props();

    const breadcrumbs: BreadcrumbItem[] = [
        { title: 'Reviewer Area', href: '#' },
        { title: 'Etik', href: '#' },
        { title: 'Dashboard', href: '#' },
    ];
</script>

<svelte:head>
    <title>Dashboard Reviewer Etik</title>
</svelte:head>

<AppLayout {breadcrumbs}>
    <LayoutComposer>
        {#snippet header()}
            <Heading title="Dashboard Reviewer Etik" description="Ringkasan pengajuan ethical clearance." />
        {/snippet}

        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <Card.Root class="cursor-pointer hover:shadow-md transition-shadow" onclick={() => router.visit(route('review.ethics.proposal.index'))}>
                <Card.Header class="flex flex-row items-center justify-between space-y-0 pb-2">
                    <Card.Title class="text-sm font-medium">Proposal untuk Ditinjau</Card.Title>
                    <FileTextIcon class="h-4 w-4 text-muted-foreground" />
                </Card.Header>
                <Card.Content>
                    <div class="text-2xl font-bold">{proposalCount}</div>
                    <p class="text-xs text-muted-foreground">pengajuan di tahap proposal</p>
                </Card.Content>
            </Card.Root>

            <Card.Root
                class="cursor-pointer hover:shadow-md transition-shadow"
                onclick={() => router.visit(route('review.ethics.wait_for_output.index'))}
            >
                <Card.Header class="flex flex-row items-center justify-between space-y-0 pb-2">
                    <Card.Title class="text-sm font-medium">Menunggu Upload EC</Card.Title>
                    <UploadIcon class="h-4 w-4 text-muted-foreground" />
                </Card.Header>
                <Card.Content>
                    <div class="text-2xl font-bold">{waitForOutputCount}</div>
                    <p class="text-xs text-muted-foreground">pengajuan menunggu dokumen EC</p>
                </Card.Content>
            </Card.Root>

            <Card.Root
                class="cursor-pointer hover:shadow-md transition-shadow"
                onclick={() => router.visit(route('review.ethics.verification.index'))}
            >
                <Card.Header class="flex flex-row items-center justify-between space-y-0 pb-2">
                    <Card.Title class="text-sm font-medium">Menunggu Verifikasi</Card.Title>
                    <CheckCircleIcon class="h-4 w-4 text-orange-500" />
                </Card.Header>
                <Card.Content>
                    <div class="text-2xl font-bold text-orange-500">{verificationCount}</div>
                    <p class="text-xs text-muted-foreground">pengajuan menunggu verifikasi Anda</p>
                </Card.Content>
            </Card.Root>

            <Card.Root class="cursor-pointer hover:shadow-md transition-shadow" onclick={() => router.visit(route('review.ethics.output.index'))}>
                <Card.Header class="flex flex-row items-center justify-between space-y-0 pb-2">
                    <Card.Title class="text-sm font-medium">Lembar Etik Disahkan</Card.Title>
                    <CheckCircleIcon class="h-4 w-4 text-green-600" />
                </Card.Header>
                <Card.Content>
                    <div class="text-2xl font-bold text-green-600">{outputCompletedCount}</div>
                    <p class="text-xs text-muted-foreground">pengajuan yang telah diterbitkan EC</p>
                </Card.Content>
            </Card.Root>
        </div>
    </LayoutComposer>
</AppLayout>
