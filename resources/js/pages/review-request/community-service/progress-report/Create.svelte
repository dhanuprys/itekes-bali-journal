<script lang="ts">
    import LayoutComposer from '@/layouts/LayoutComposer.svelte';
    import AppLayout from '@/layouts/AppLayout.svelte';
    import { type BreadcrumbItem } from '@/types';
    import Heading from '@/components/Heading.svelte';
    import ProgressReportForm from '@/components/review-request/ProgressReportForm.svelte';
    import { useForm } from '@inertiajs/svelte';
    import { untrack } from 'svelte';
    import { Button } from '@/components/ui/button';
    import { toast } from 'svelte-sonner';
    import { uploadState } from '@/stores/upload-state.svelte';

    let { submission, detail } = $props();

    const breadcrumbs: BreadcrumbItem[] = [
        { title: 'Permintaan Review', href: '#' },
        { title: 'Pengabdian Masyarakat', href: '#' },
        { title: 'Laporan Kemajuan', href: route('apply.community_service.progress_report.index') },
        { title: 'Buat Laporan', href: '#' },
    ];

    // Initialize form with proposal data
    const form = useForm(untrack(() => ({
        submission_id: submission.id,
        final_title: detail.final_title || detail.title,
        final_leader_name: detail.final_leader_name || detail.leader_name,
        members: detail.members ? detail.members.map((m: any) => ({ name: m.name })) : [],
        progress_report_path: '',
        manuscript_path: '',
    })));

    function submit() {
        $form.post(route('apply.community_service.progress_report.store'), {
            onSuccess: () => {
                toast.success('Laporan kemajuan berhasil disimpan.');
            },
            onError: () => {
                toast.error('Gagal menyimpan laporan. Periksa kembali input Anda.');
            },
        });
    }
</script>

<svelte:head>
    <title>Buat Laporan Kemajuan - Pengabdian</title>
</svelte:head>

<AppLayout {breadcrumbs}>
    <LayoutComposer>
        {#snippet header()}
            <Heading title="Buat Laporan Kemajuan" description="Isi formulir laporan kemajuan/akhir pengabdian." />
        {/snippet}

        <form
            onsubmit={(e) => {
                e.preventDefault();
                submit();
            }}
        >
            <ProgressReportForm bind:form={$form} data={{}} type="community-service" mode="create" />

            <div class="mt-6 flex justify-end gap-3">
                <Button variant="outline" href={route('apply.community_service.progress_report.index')}>Batal</Button>
                <Button type="submit" disabled={$form.processing || uploadState.isUploading}>
                    {#if $form.processing}
                        Menyimpan...
                    {:else}
                        Simpan Laporan
                    {/if}
                </Button>
            </div>
        </form>
    </LayoutComposer>
</AppLayout>
