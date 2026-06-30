<script lang="ts">
    import LayoutComposer from '@/layouts/layout-composer.svelte';
    import AppLayout from '@/layouts/app-layout.svelte';
    import { type BreadcrumbItem } from '@/types';
    import Heading from '@/components/heading.svelte';
    import FinalReportForm from '@/components/review-request/final-report-form.svelte';
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
        { title: 'Buat Laporan', href: '#' },
    ];

    const form = useForm(
        untrack(() => ({
            submission_id: submission.id,
            final_report_path: '',
            manuscript_path: '',
            supplementary_path: '',
            notes: '',
        })),
    );

    function submit() {
        $form.post(route('apply.community_service.final_report.store'), {
            onSuccess: () => {
                toast.success('Laporan akhir berhasil disimpan.');
            },
            onError: () => {
                toast.error('Gagal menyimpan laporan. Periksa kembali input Anda.');
            },
        });
    }
</script>

<svelte:head>
    <title>Buat Laporan Akhir - Pengabdian Masyarakat</title>
</svelte:head>

<AppLayout {breadcrumbs}>
    <LayoutComposer>
        {#snippet header()}
            <Heading title="Buat Laporan Akhir" description="Unggah file laporan akhir pengabdian masyarakat." />
        {/snippet}

        <form
            onsubmit={(e) => {
                e.preventDefault();
                submit();
            }}
        >
            <FinalReportForm bind:form={$form} type="community_service" mode="create" />

            <div class="mt-6 flex justify-end gap-3">
                <Button variant="outline" href={route('apply.community_service.final_report.index')}>Batal</Button>
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
