<script lang="ts">
    import Heading from '@/components/Heading.svelte';
    import AppLayout from '@/layouts/AppLayout.svelte';
    import { Button } from '@/components/ui/button';
    import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
    import { Badge } from '@/components/ui/badge';
    import { ArrowLeft } from 'lucide-svelte';
    import { router } from '@inertiajs/svelte';

    let { permission } = $props();

    let breadcrumbs = $derived([
        {
            title: 'Permissions',
            href: '/permissions',
        },
        {
            title: permission?.name || 'Detail Permission',
            href: '#',
        },
    ]);
</script>

<svelte:head>
    <title>Detail Permission - {permission?.name || 'Loading...'}</title>
</svelte:head>

<AppLayout {breadcrumbs}>
    <div class="px-4 py-6">
        <div class="mb-6">
            <Button variant="outline" onclick={() => router.visit('/permissions')} class="mb-4">
                <ArrowLeft class="mr-2 h-4 w-4" />
                Kembali
            </Button>
            <Heading title={permission?.name || 'Detail Permission'} description="Detail informasi permission dan role terkait" />
        </div>

        <div class="grid gap-6 md:grid-cols-2">
            <Card>
                <CardHeader>
                    <CardTitle>Informasi Dasar</CardTitle>
                </CardHeader>
                <CardContent class="space-y-4">
                    <div class="grid grid-cols-3 gap-4">
                        <div class="text-sm font-medium text-muted-foreground">Nama Permission</div>
                        <div class="col-span-2 text-sm">{permission?.name || '-'}</div>
                    </div>
                    <div class="grid grid-cols-3 gap-4">
                        <div class="text-sm font-medium text-muted-foreground">Guard Name</div>
                        <div class="col-span-2 text-sm">{permission?.guard_name || '-'}</div>
                    </div>
                    <div class="grid grid-cols-3 gap-4">
                        <div class="text-sm font-medium text-muted-foreground">Dibuat</div>
                        <div class="col-span-2 text-sm">{permission?.created_at ? new Date(permission.created_at).toLocaleDateString() : '-'}</div>
                    </div>
                </CardContent>
            </Card>

            <Card>
                <CardHeader>
                    <CardTitle>Role Terkait</CardTitle>
                </CardHeader>
                <CardContent>
                    {#if permission?.roles && permission.roles.length > 0}
                        <div class="flex flex-wrap gap-2">
                            {#each permission.roles as role (role.id)}
                                <Badge variant="secondary">{role.name}</Badge>
                            {/each}
                        </div>
                    {:else}
                        <p class="text-sm text-muted-foreground">Belum ada role yang memiliki izin ini.</p>
                    {/if}
                </CardContent>
            </Card>
        </div>
    </div>
</AppLayout>
