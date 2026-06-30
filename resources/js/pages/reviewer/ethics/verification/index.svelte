<script lang="ts">
    import AppLayout from '@/layouts/app-layout.svelte';
    import LayoutComposer from '@/layouts/layout-composer.svelte';
    import Heading from '@/components/heading.svelte';
    import { type BreadcrumbItem } from '@/types';
    import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
    import { Button } from '@/components/ui/button';
    import { router, page } from '@inertiajs/svelte';
    import { FileTextIcon, EyeIcon } from 'lucide-svelte';
    import Badge from '@/components/ui/badge/badge.svelte';

    let { submissions } = $props();

    const breadcrumbs: BreadcrumbItem[] = [
        { title: 'Reviewer Area', href: '#' },
        { title: 'Etik', href: '#' },
        { title: 'Menunggu Verifikasi', href: '#' },
    ];

    const currentUser = $derived($page.props.auth.user);
</script>

<svelte:head>
    <title>Menunggu Verifikasi Etik</title>
</svelte:head>

<AppLayout {breadcrumbs}>
    <LayoutComposer>
        {#snippet header()}
            <Heading title="Menunggu Verifikasi Etik" description="Daftar pengajuan ethical clearance yang menunggu verifikasi Anda." />
        {/snippet}

        <div class="bg-background shadow-sm rounded-lg border">
            <Table>
                <TableHeader>
                    <TableRow>
                        <TableHead>Judul & Pengaju</TableHead>
                        <TableHead>Dokumen</TableHead>
                        <TableHead>Status Verifikasi</TableHead>
                        <TableHead class="w-[100px]">Aksi</TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    {#if submissions.data.length === 0}
                        <TableRow>
                            <TableCell colspan={4} class="text-center py-8 text-muted-foreground"
                                >Tidak ada dokumen yang menunggu verifikasi.</TableCell
                            >
                        </TableRow>
                    {:else}
                        {#each submissions.data as submission}
                            {@const output = submission.latest_output}
                            <TableRow>
                                <TableCell>
                                    <div class="font-medium">{submission.latest_detail?.title || 'N/A'}</div>
                                    <div class="text-xs text-muted-foreground mt-1">
                                        Oleh: {submission.user?.name || 'N/A'}
                                    </div>
                                    <div class="text-xs text-muted-foreground">
                                        Tanggal: {new Date(submission.created_at).toLocaleDateString('id-ID')}
                                    </div>
                                </TableCell>
                                <TableCell>
                                    <div class="flex items-center gap-2">
                                        <FileTextIcon class="h-4 w-4 text-blue-500" />
                                        <a
                                            href={`/storage/${output?.document_path}`}
                                            target="_blank"
                                            class="text-sm text-blue-600 hover:underline"
                                            onclick={(e) => e.stopPropagation()}
                                        >
                                            Lihat Dokumen
                                        </a>
                                    </div>
                                </TableCell>
                                <TableCell>
                                    {#if output?.verifications?.find((v: any) => v.user_id === currentUser.id)}
                                        <Badge variant="outline" class="text-green-600 border-green-600 bg-green-50">Telah Anda Verifikasi</Badge>
                                    {:else}
                                        <Badge variant="outline" class="text-orange-500 border-orange-500">Menunggu Verifikasi Anda</Badge>
                                    {/if}
                                </TableCell>
                                <TableCell>
                                    <Button
                                        variant="outline"
                                        size="sm"
                                        class="gap-2"
                                        onclick={() => router.visit(route('review.ethics.verification.show', submission.id))}
                                    >
                                        <EyeIcon class="h-4 w-4" />
                                        Detail
                                    </Button>
                                </TableCell>
                            </TableRow>
                        {/each}
                    {/if}
                </TableBody>
            </Table>
        </div>
    </LayoutComposer>
</AppLayout>
