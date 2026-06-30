<script lang="ts">
    import LayoutComposer from '@/layouts/layout-composer.svelte';
    import Heading from '@/components/heading.svelte';
    import AppLayout from '@/layouts/app-layout.svelte';
    import { Button } from '@/components/ui/button';
    import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
    import { Badge } from '@/components/ui/badge';
    import { ArrowLeft, Pencil } from 'lucide-svelte';
    import { router } from '@inertiajs/svelte';
    import RoleSheet from './role-sheet.svelte';

    let { role, permissions = [] } = $props();
    let sheetOpen = $state(false);

    let breadcrumbs = $derived([
        {
            title: 'Roles',
            href: '/roles',
        },
        {
            title: role?.name || 'Detail Role',
            href: '#',
        },
    ]);
</script>

<svelte:head>
    <title>Detail Role - {role?.name || 'Loading...'}</title>
</svelte:head>

<AppLayout {breadcrumbs}>
    <LayoutComposer>
        {#snippet backButton()}
            <Button variant="outline" onclick={() => router.visit('/roles')}>
                <ArrowLeft class="mr-2 h-4 w-4" />
                Kembali
            </Button>
        {/snippet}

        {#snippet header()}
            <Heading title={role?.name || 'Detail Role'} description="Detail informasi role dan perizinan" />
        {/snippet}

        {#snippet actions()}
            {#if !role.is_preserved}
                <Button onclick={() => (sheetOpen = true)}>
                    <Pencil class="mr-2 h-4 w-4" />
                    Edit Role
                </Button>
            {/if}
        {/snippet}

        <div class="grid gap-6 md:grid-cols-2">
            <Card>
                <CardHeader>
                    <CardTitle>Informasi Dasar</CardTitle>
                </CardHeader>
                <CardContent class="space-y-4">
                    <div class="grid grid-cols-3 gap-4">
                        <div class="text-sm font-medium text-muted-foreground">Nama Role</div>
                        <div class="col-span-2 text-sm">{role?.name || '-'}</div>
                    </div>
                    <div class="grid grid-cols-3 gap-4">
                        <div class="text-sm font-medium text-muted-foreground">Guard Name</div>
                        <div class="col-span-2 text-sm font-mono bg-muted p-1 rounded inline-block">{role?.guard_name || '-'}</div>
                    </div>
                    <div class="grid grid-cols-3 gap-4">
                        <div class="text-sm font-medium text-muted-foreground">Dibuat</div>
                        <div class="col-span-2 text-sm">{role?.created_at ? new Date(role.created_at).toLocaleDateString() : '-'}</div>
                    </div>
                </CardContent>
            </Card>

            <Card>
                <CardHeader>
                    <CardTitle>Daftar Izin (Permissions)</CardTitle>
                </CardHeader>
                <CardContent>
                    {#if role?.permissions && role.permissions.length > 0}
                        <div class="flex flex-wrap gap-2">
                            {#each role.permissions as perm (perm.id)}
                                <Badge variant="secondary">{perm.name}</Badge>
                            {/each}
                        </div>
                    {:else}
                        <p class="text-sm text-muted-foreground">Tidak ada izin yang diberikan untuk role ini.</p>
                    {/if}
                </CardContent>
            </Card>
        </div>

        <RoleSheet bind:open={sheetOpen} selectedRole={role} {permissions} />
    </LayoutComposer>
</AppLayout>
