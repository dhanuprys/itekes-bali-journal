<script lang="ts">
    import LayoutComposer from '@/layouts/LayoutComposer.svelte';
    import AppLayout from '@/layouts/AppLayout.svelte';
    import { type BreadcrumbItem } from '@/types';
    import Heading from '@/components/Heading.svelte';
    import * as Card from '@/components/ui/card';
    import { Badge } from '@/components/ui/badge';
    import { Button } from '@/components/ui/button';
    import { getStatusConfig } from '@/lib/review-status';
    import { Edit, DownloadIcon, FileTextIcon } from 'lucide-svelte';

    let { submission } = $props();
    let detail = $derived(submission.latest_detail);
    let files = $derived(detail?.files ?? []);

    const breadcrumbs: BreadcrumbItem[] = [
        { title: 'Permintaan Review', href: '#' },
        { title: 'Etik', href: '#' },
        { title: 'Proposal', href: route('apply.ethics.proposal.index') },
        { title: 'Detail', href: '#' },
    ];

    let statusConfig = $derived(getStatusConfig(submission.status));

    function getCategoryLabel(category: string) {
        return category === 'clinical' ? 'Etik Klinik / Uji Coba Hewan' : 'Etik Non Klinis';
    }
</script>

<svelte:head>
    <title>Detail Pengajuan Etik</title>
</svelte:head>

<AppLayout {breadcrumbs}>
    <LayoutComposer>
        {#snippet header()}
            <Heading title="Detail Pengajuan Etik" description="Informasi lengkap pengajuan ethical clearance." />
        {/snippet}

        {#snippet actions()}
            <div class="flex items-center gap-2">
                {#if submission.status === 'revision_needed'}
                    <Button href={route('apply.ethics.proposal.edit', submission.id)} size="sm" class="gap-2">
                        <Edit class="h-4 w-4" />
                        Revisi Pengajuan
                    </Button>
                {/if}
                <Badge variant="outline" class={'px-3 py-1 gap-2 flex items-center ' + statusConfig.color}>
                    <statusConfig.icon class="h-4 w-4" />
                    {statusConfig.label}
                </Badge>
            </div>
        {/snippet}

        <div class="space-y-6">
            <!-- Submission Info -->
            <Card.Root>
                <Card.Header>
                    <Card.Title>Informasi Pengajuan</Card.Title>
                </Card.Header>
                <Card.Content>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-muted-foreground">Kategori</p>
                            <p class="font-medium">{getCategoryLabel(submission.category)}</p>
                        </div>
                        <div>
                            <p class="text-sm text-muted-foreground">Tanggal Pengajuan</p>
                            <p class="font-medium">{new Date(submission.created_at).toLocaleDateString('id-ID', { year: 'numeric', month: 'long', day: 'numeric' })}</p>
                        </div>
                        <div>
                            <p class="text-sm text-muted-foreground">Status</p>
                            <Badge variant={submission.status === 'approved' ? 'default' : submission.status === 'rejected' ? 'destructive' : 'secondary'}>
                                {submission.status.replace('_', ' ').toUpperCase()}
                            </Badge>
                        </div>
                        <div>
                            <p class="text-sm text-muted-foreground">Tahap</p>
                            <p class="font-medium">{submission.stage === 'proposal' ? 'Proposal' : 'Output'}</p>
                        </div>
                    </div>
                </Card.Content>
            </Card.Root>

            <!-- Payment Info -->
            <Card.Root>
                <Card.Header>
                    <Card.Title>Informasi Pemohon & Pembayaran</Card.Title>
                </Card.Header>
                <Card.Content>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-muted-foreground">Status Pemohon</p>
                            <p class="font-medium">{submission.is_student ? 'Mahasiswa ITEKES Bali' : 'Publik / Umum'}</p>
                        </div>
                        {#if submission.is_student}
                            <div>
                                <p class="text-sm text-muted-foreground">NIM</p>
                                <p class="font-medium">{submission.student_nim ?? '-'}</p>
                            </div>
                            <div>
                                <p class="text-sm text-muted-foreground">Nama Wali</p>
                                <p class="font-medium">{submission.wali_name ?? '-'}</p>
                            </div>
                        {/if}
                        <div class="md:col-span-2 mt-2">
                            <p class="text-sm text-muted-foreground mb-1">Bukti Transfer</p>
                            {#if submission.payment_proof_path}
                                <a
                                    href={'/storage/' + submission.payment_proof_path}
                                    target="_blank"
                                    class="inline-flex items-center gap-1.5 rounded-md border border-input bg-background px-3 py-1.5 text-xs font-medium hover:bg-accent hover:text-accent-foreground transition-colors shrink-0"
                                >
                                    <FileTextIcon class="h-3.5 w-3.5" />
                                    Lihat Bukti Transfer
                                </a>
                            {:else}
                                <p class="text-sm italic text-muted-foreground">Tidak ada bukti transfer.</p>
                            {/if}
                        </div>
                    </div>
                </Card.Content>
            </Card.Root>

            <!-- Uploaded Documents -->
            <Card.Root>
                <Card.Header>
                    <Card.Title>Dokumen yang Diunggah</Card.Title>
                    <Card.Description>Daftar dokumen yang telah Anda unggah untuk pengajuan ini.</Card.Description>
                </Card.Header>
                <Card.Content>
                    {#if files.length === 0}
                        <p class="text-sm text-muted-foreground">Tidak ada dokumen.</p>
                    {:else}
                        <div class="space-y-2">
                            {#each files as file (file.id)}
                                <div class="flex items-center justify-between border rounded-lg p-3">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <FileTextIcon class="h-5 w-5 text-muted-foreground shrink-0" />
                                        <div class="min-w-0">
                                            <p class="font-medium text-sm truncate">{file.original_name || file.template_key}</p>
                                            <p class="text-xs text-muted-foreground">{file.template_key.replace(/_/g, ' ')}</p>
                                        </div>
                                    </div>
                                    <a
                                        href={'/storage/' + file.file_path}
                                        target="_blank"
                                        class="inline-flex items-center gap-1.5 rounded-md bg-primary/10 px-3 py-1.5 text-xs font-medium text-primary hover:bg-primary/20 transition-colors shrink-0"
                                    >
                                        <DownloadIcon class="h-3.5 w-3.5" />
                                        Unduh
                                    </a>
                                </div>
                            {/each}
                        </div>
                    {/if}
                </Card.Content>
            </Card.Root>

            <!-- Comments -->
            <Card.Root>
                <Card.Header>
                    <Card.Title>Riwayat Review</Card.Title>
                </Card.Header>
                <Card.Content>
                    {#if detail?.comments && detail.comments.length > 0}
                        <div class="space-y-3">
                            {#each detail.comments as comment (comment.id)}
                                <div class="border rounded-lg p-3">
                                    <div class="flex items-center justify-between mb-1">
                                        <p class="font-medium text-sm">{comment.user?.name ?? 'Unknown'}</p>
                                        <p class="text-xs text-muted-foreground">{new Date(comment.created_at).toLocaleString('id-ID')}</p>
                                    </div>
                                    <p class="text-sm text-muted-foreground">{comment.content}</p>
                                </div>
                            {/each}
                        </div>
                    {:else}
                        <p class="text-muted-foreground text-sm">Belum ada riwayat review.</p>
                    {/if}
                </Card.Content>
            </Card.Root>
        </div>
    </LayoutComposer>
</AppLayout>
