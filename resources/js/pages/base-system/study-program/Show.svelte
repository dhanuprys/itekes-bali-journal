<script lang="ts">
    import LayoutComposer from '@/layouts/LayoutComposer.svelte';
    import Heading from '@/components/Heading.svelte';
    import AppLayout from '@/layouts/AppLayout.svelte';
    import { Button } from '@/components/ui/button';
    import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
    import { ArrowLeft } from 'lucide-svelte';
    import { router } from '@inertiajs/svelte';

    let { studyProgram } = $props();

    let breadcrumbs = $derived([
        {
            title: 'Program Studi',
            href: '/study-program',
        },
        {
            title: studyProgram?.name || 'Detail Program Studi',
            href: '#',
        },
    ]);
</script>

<svelte:head>
    <title>Detail Program Studi - {studyProgram?.name || 'Loading...'}</title>
</svelte:head>

<AppLayout {breadcrumbs}>
    <LayoutComposer>
        {#snippet backButton()}
            <Button variant="outline" onclick={() => router.visit('/study-program')}>
                <ArrowLeft class="mr-2 h-4 w-4" />
                Kembali
            </Button>
        {/snippet}

        {#snippet header()}
            <Heading title={studyProgram?.name || 'Detail Program Studi'} description="Detail informasi program studi" />
        {/snippet}

        <div class="grid gap-6 md:grid-cols-2">
            <Card>
                <CardHeader>
                    <CardTitle>Informasi Dasar</CardTitle>
                </CardHeader>
                <CardContent class="space-y-4">
                    <div class="grid grid-cols-3 gap-4">
                        <div class="text-sm font-medium text-muted-foreground">Nama</div>
                        <div class="col-span-2 text-sm">{studyProgram?.name || '-'}</div>
                    </div>
                    <div class="grid grid-cols-3 gap-4">
                        <div class="text-sm font-medium text-muted-foreground">Dibuat</div>
                        <div class="col-span-2 text-sm">
                            {studyProgram?.created_at ? new Date(studyProgram.created_at).toLocaleDateString() : '-'}
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>
    </LayoutComposer>
</AppLayout>
