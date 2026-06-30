<script lang="ts">
    import LayoutComposer from '@/layouts/layout-composer.svelte';
    import AppLayout from '@/layouts/app-layout.svelte';
    import { type BreadcrumbItem } from '@/types';
    import Heading from '@/components/heading.svelte';
    import FinalReportForm from '@/components/review-request/final-report-form.svelte';
    import RevisionCommentSheet from '@/components/review-request/revision-comment-sheet.svelte';
    import * as Alert from '@/components/ui/alert';
    import { useForm } from '@inertiajs/svelte';
    import { untrack } from 'svelte';
    import { Button } from '@/components/ui/button';
    import { toast } from 'svelte-sonner';
    import { uploadState } from '@/stores/upload-state.svelte';

    let { submission, detail } = $props();

    const breadcrumbs: BreadcrumbItem[] = [
        { title: 'Permintaan Review', href: '#' },
        { title: 'Pengabdian Masyarakat', href: '#' },
        { title: 'Laporan Akhir', href: route('apply.community_service.final_report.index') },
        { title: 'Revisi', href: '#' },
    ];

    const form = useForm(
        untrack(() => ({
            submission_id: submission.id,
            final_report_path: detail.final_report_path || '',
            manuscript_path: detail.manuscript_path || '',
            supplementary_path: detail.supplementary_path || '',
            notes: detail.notes || '',
        })),
    );

    function submit() {
        if (uploadState.isUploading) return;
        $form.post(route('apply.community_service.final_report.revise', submission.id), {
            onSuccess: () => {
                toast.success('Revisi laporan berhasil dikirim.');
            },
            onError: () => {
                toast.error('Gagal mengirim revisi. Periksa kembali input Anda.');
            },
        });
    }
</script>

<svelte:head>
    <title>Revisi Laporan Akhir - Pengabdian Masyarakat</title>
</svelte:head>

<AppLayout {breadcrumbs}>
    <LayoutComposer>
        {#snippet header()}
            <Heading title="Revisi Laporan Akhir" description="Perbarui laporan akhir Anda berdasarkan masukan reviewer." />
        {/snippet}

        {#snippet actions()}
            <RevisionCommentSheet comments={detail?.comments} />
        {/snippet}

        {#if submission.status === 'revision_needed'}
            <div class="mb-6">
                <Alert.Root variant="destructive">
                    <Alert.Title>Perhatian</Alert.Title>
                    <Alert.Description>
                        Laporan Anda memerlukan revisi. Silakan cek komentar reviewer melalui tombol di atas kanan dan unggah versi perbaikan.
                    </Alert.Description>
                </Alert.Root>
            </div>
        {/if}

        <form
            onsubmit={(e) => {
                e.preventDefault();
                submit();
            }}
        >
            <FinalReportForm bind:form={$form} type="community_service" mode="revise" />

            <div class="mt-6 flex justify-end gap-3">
                <Button variant="outline" href={route('apply.community_service.final_report.index')}>Batal</Button>
                <Button type="submit" disabled={$form.processing || uploadState.isUploading}>
                    {#if $form.processing}
                        Menyimpan...
                    {:else}
                        Kirim Revisi
                    {/if}
                </Button>
            </div>
        </form>
    </LayoutComposer>
</AppLayout>
