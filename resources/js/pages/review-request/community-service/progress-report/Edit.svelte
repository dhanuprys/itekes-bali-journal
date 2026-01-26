<script lang="ts">
    import LayoutComposer from '@/layouts/LayoutComposer.svelte';
    import AppLayout from '@/layouts/AppLayout.svelte';
    import { type BreadcrumbItem } from '@/types';
    import Heading from '@/components/Heading.svelte';
    import { useForm } from '@inertiajs/svelte';
    import { Button } from '@/components/ui/button';
    import * as Alert from '@/components/ui/alert';
    import ProgressReportForm from '@/components/review-request/ProgressReportForm.svelte';
    import RevisionCommentSheet from '@/components/review-request/RevisionCommentSheet.svelte';
    import { uploadState } from '@/stores/upload-state.svelte';
    import { toast } from 'svelte-sonner';

    let { submission, detail, schemas } = $props();

    const breadcrumbs: BreadcrumbItem[] = [
        { title: 'Permintaan Review', href: '#' },
        { title: 'Pengabdian Masyarakat', href: '#' },
        { title: 'Laporan Kemajuan', href: '/apply/community-service/progress-report' },
        { title: 'Revisi', href: '#' },
    ];

    const form = useForm({
        submission_id: submission.id, // Keep submission_id for reference
        leader_nidn: detail.leader_nidn || '',
        final_leader_name: detail.final_leader_name || '',
        final_title: detail.final_title || '',
        members: detail.members ? detail.members.map((m: any) => ({ name: m.name })) : [],
        schema_id: detail.community_service_schema_id || '',
        final_report_path: detail.final_report_path || '',
        manuscript_path: detail.manuscript_path || '',
    });

    function submit() {
        if (uploadState.isUploading) return;
        $form.post(route('apply.community_service.progress_report.revise', submission.id), {
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
    <title>Revisi Laporan Kemajuan</title>
</svelte:head>

<AppLayout {breadcrumbs}>
    <LayoutComposer>
        {#snippet header()}
            <div class="flex items-center justify-between">
                <Heading title="Revisi Laporan Kemajuan" description="Perbarui laporan Anda berdasarkan masukan reviewer." />
            </div>
        {/snippet}

        {#snippet actions()}
            <RevisionCommentSheet comments={detail?.comments} />
        {/snippet}

        {#snippet children()}
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
                <ProgressReportForm bind:form={$form} data={{ schemas }} type="community-service" mode="revise" />

                <div class="mt-6 flex justify-end gap-3">
                    <Button variant="outline" href={route('apply.community_service.progress_report.index')}>Batal</Button>
                    <Button type="submit" disabled={$form.processing || uploadState.isUploading}>
                        {#if $form.processing}
                            Menyimpan...
                        {:else}
                            Kirim Revisi
                        {/if}
                    </Button>
                </div>
            </form>
        {/snippet}
    </LayoutComposer>
</AppLayout>
