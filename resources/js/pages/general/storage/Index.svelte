<script lang="ts">
    import AppLayout from '@/layouts/AppLayout.svelte';
    import LayoutComposer from '@/layouts/LayoutComposer.svelte';
    import { type BreadcrumbItem } from '@/types';
    import Heading from '@/components/Heading.svelte';
    import * as Card from '@/components/ui/card';
    import { Button } from '@/components/ui/button';
    import * as Table from '@/components/ui/table';
    import { Badge } from '@/components/ui/badge';
    import {
        FileTextIcon,
        DownloadIcon,
        HardDriveIcon,
        ImageIcon,
        FileArchiveIcon,
        FileIcon,
        FilesIcon,
        CheckCircleIcon,
        InfoIcon,
        CopyIcon,
    } from 'lucide-svelte';
    import Pagination from '@/components/Pagination.svelte';
    import { Alert, AlertTitle, AlertDescription } from '@/components/ui/alert';
    import * as AlertDialog from '@/components/ui/alert-dialog';
    import { toast } from 'svelte-sonner';

    let { files, stats } = $props();

    let fileToDownload = $state<any>(null);

    const breadcrumbs: BreadcrumbItem[] = [
        { title: 'Dashboard', href: route('dashboard') },
        { title: 'File yang diunggah', href: '#' },
    ];

    function formatBytes(bytes: number, decimals = 2) {
        if (!+bytes) return '0 Bytes';
        const k = 1024;
        const dm = decimals < 0 ? 0 : decimals;
        const sizes = ['Bytes', 'KiB', 'MiB', 'GiB', 'TiB', 'PiB', 'EiB', 'ZiB', 'YiB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return `${parseFloat((bytes / Math.pow(k, i)).toFixed(dm))} ${sizes[i]}`;
    }

    function formatDate(dateString: string) {
        return new Date(dateString).toLocaleDateString('id-ID', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
        });
    }

    function getFileIcon(mimeType: string) {
        if (!mimeType) return FileIcon;
        if (mimeType.startsWith('image/')) return ImageIcon;
        if (mimeType === 'application/pdf') return FileTextIcon;
        if (mimeType.includes('zip') || mimeType.includes('compressed') || mimeType.includes('tar') || mimeType.includes('rar')) {
            return FileArchiveIcon;
        }
        return FileIcon;
    }

    function copyFileLink(filePath: string) {
        const url = `${window.location.origin}/storage/${filePath}`;
        navigator.clipboard
            .writeText(url)
            .then(() => {
                toast.success('Link disalin ke clipboard!');
            })
            .catch(() => {
                toast.error('Gagal menyalin link');
            });
    }

    function handleDownloadClick(file: any) {
        fileToDownload = file;
    }

    function confirmDownload() {
        if (fileToDownload) {
            const link = document.createElement('a');
            link.href = `/storage/${fileToDownload.file_path}`;
            link.download = fileToDownload.file_name;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            toast.success('File sedang diunduh...');
            fileToDownload = null;
        }
    }
</script>

<svelte:head>
    <title>File yang diunggah</title>
</svelte:head>

<AppLayout {breadcrumbs}>
    <LayoutComposer>
        {#snippet header()}
            <Heading title="File yang diunggah" description="Lihat daftar file yang pernah anda unggah" />
        {/snippet}

        {#snippet children()}
            <div class="grid gap-4 md:grid-cols-3 mb-4">
                <Card.Root>
                    <Card.Header class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <Card.Title class="text-sm font-medium">Total File</Card.Title>
                        <FilesIcon class="h-4 w-4 text-muted-foreground" />
                    </Card.Header>
                    <Card.Content>
                        <div class="text-2xl font-bold">{stats.count}</div>
                        <p class="text-xs text-muted-foreground">File telah diunggah</p>
                    </Card.Content>
                </Card.Root>
                <Card.Root>
                    <Card.Header class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <Card.Title class="text-sm font-medium">Total Ukuran</Card.Title>
                        <HardDriveIcon class="h-4 w-4 text-muted-foreground" />
                    </Card.Header>
                    <Card.Content>
                        <div class="text-2xl font-bold">{formatBytes(stats.usage)}</div>
                        <p class="text-xs text-muted-foreground">Ruang penyimpanan digunakan</p>
                    </Card.Content>
                </Card.Root>
                <Card.Root>
                    <Card.Header class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <Card.Title class="text-sm font-medium">File Digunakan</Card.Title>
                        <CheckCircleIcon class="h-4 w-4 text-muted-foreground" />
                    </Card.Header>
                    <Card.Content>
                        <div class="text-2xl font-bold">{stats.used_count}</div>
                        <p class="text-xs text-muted-foreground">File terlampir pada pengajuan</p>
                    </Card.Content>
                </Card.Root>
            </div>

            <Alert class="mb-4">
                <InfoIcon class="h-4 w-4" />
                <AlertTitle>Informasi Penyimpanan</AlertTitle>
                <AlertDescription>
                    File yang terlihat pudar menandakan file tersebut tidak lagi digunakan dan dijadwalkan untuk dihapus otomatis oleh sistem.
                </AlertDescription>
            </Alert>

            <Card.Root class="py-0">
                <Card.Content class="p-0">
                    <Table.Root>
                        <Table.Header>
                            <Table.Row>
                                <Table.Head class="w-[50%]">Nama File</Table.Head>
                                <Table.Head>Tipe</Table.Head>
                                <Table.Head>Ukuran</Table.Head>
                                <Table.Head>Diunggah Pada</Table.Head>
                                <Table.Head class="text-right">Aksi</Table.Head>
                            </Table.Row>
                        </Table.Header>
                        <Table.Body>
                            {#if files.data.length > 0}
                                {#each files.data as file}
                                    {@const Icon = getFileIcon(file.mime_type)}
                                    <Table.Row class="hover:bg-muted/40 transition-colors group {file.is_used ? '' : 'opacity-30'}">
                                        <Table.Cell class="py-3">
                                            <div class="flex items-center gap-3">
                                                <div class="h-9 w-9 rounded-lg bg-primary/10 flex items-center justify-center text-primary">
                                                    <Icon class="h-4 w-4" />
                                                </div>
                                                <div class="flex flex-col">
                                                    <span class="font-medium text-sm mb-1 truncate max-w-[400px]" title={file.file_name}>
                                                        {file.file_name}
                                                    </span>
                                                    <div class="flex items-center gap-2">
                                                        <Badge variant="secondary" class="text-[10px] px-1 py-0 h-4 font-normal rounded-sm">
                                                            {file.action?.replace(/_/g, ' ') || 'Unknown'}
                                                        </Badge>
                                                    </div>
                                                </div>
                                            </div>
                                        </Table.Cell>
                                        <Table.Cell class="py-2">
                                            <div class="flex items-center">
                                                <Badge variant="outline" class="font-mono text-[10px] uppercase h-5">
                                                    {file.mime_type?.split('/').pop() || 'FILE'}
                                                </Badge>
                                            </div>
                                        </Table.Cell>
                                        <Table.Cell class="font-mono text-xs py-2">
                                            {formatBytes(file.file_size)}
                                        </Table.Cell>
                                        <Table.Cell class="text-muted-foreground text-xs py-2">
                                            {formatDate(file.created_at)}
                                        </Table.Cell>
                                        <Table.Cell class="text-right py-2">
                                            <div class="flex items-center justify-end gap-1">
                                                <Button
                                                    variant="ghost"
                                                    size="icon"
                                                    onclick={() => copyFileLink(file.file_path)}
                                                    title="Salin Link"
                                                    class="h-8 w-8 text-muted-foreground hover:text-foreground"
                                                >
                                                    <CopyIcon class="h-4 w-4" />
                                                </Button>
                                                <Button
                                                    variant="ghost"
                                                    size="icon"
                                                    onclick={() => handleDownloadClick(file)}
                                                    title="Download"
                                                    class="h-8 w-8 text-muted-foreground hover:text-foreground"
                                                >
                                                    <DownloadIcon class="h-4 w-4" />
                                                </Button>
                                            </div>
                                        </Table.Cell>
                                    </Table.Row>
                                {/each}
                            {:else}
                                <Table.Row>
                                    <Table.Cell colspan={5} class="text-center py-10 text-muted-foreground">
                                        <div class="flex flex-col items-center gap-2">
                                            <HardDriveIcon class="h-8 w-8 opacity-50" />
                                            <p>No files uploaded yet.</p>
                                        </div>
                                    </Table.Cell>
                                </Table.Row>
                            {/if}
                        </Table.Body>
                    </Table.Root>
                </Card.Content>
                {#if files.links && files.links.length > 3}
                    <div class="p-4 border-t">
                        <Pagination links={files.links} />
                    </div>
                {/if}
            </Card.Root>
        {/snippet}
    </LayoutComposer>
</AppLayout>

<AlertDialog.Root bind:open={fileToDownload}>
    <AlertDialog.Content>
        <AlertDialog.Header>
            <AlertDialog.Title>Konfirmasi Unduh File</AlertDialog.Title>
            <AlertDialog.Description>Apakah Anda yakin ingin mengunduh file ini?</AlertDialog.Description>
        </AlertDialog.Header>
        {#if fileToDownload}
            <div class="space-y-2 py-4">
                <div class="flex items-start gap-2">
                    <span class="text-sm font-medium text-muted-foreground min-w-[80px]">Nama File:</span>
                    <span class="text-sm flex-1 break-all">{fileToDownload.file_name}</span>
                </div>
                <div class="flex items-start gap-2">
                    <span class="text-sm font-medium text-muted-foreground min-w-[80px]">Ukuran:</span>
                    <span class="text-sm">{formatBytes(fileToDownload.file_size)}</span>
                </div>
            </div>
        {/if}
        <AlertDialog.Footer>
            <AlertDialog.Cancel onclick={() => (fileToDownload = null)}>Batal</AlertDialog.Cancel>
            <AlertDialog.Action onclick={confirmDownload}>Unduh</AlertDialog.Action>
        </AlertDialog.Footer>
    </AlertDialog.Content>
</AlertDialog.Root>
