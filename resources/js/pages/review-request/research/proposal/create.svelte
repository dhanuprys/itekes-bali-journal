<script lang="ts">
    import LayoutComposer from '@/layouts/layout-composer.svelte';
    import AppLayout from '@/layouts/app-layout.svelte';
    import { type BreadcrumbItem } from '@/types';
    import Heading from '@/components/heading.svelte';
    import { useForm } from '@inertiajs/svelte';
    import { Button } from '@/components/ui/button';
    import ProposalForm from '@/components/review-request/proposal-form.svelte';

    import { toast } from 'svelte-sonner';
    import { uploadState } from '@/stores/upload-state.svelte';

    let { studyPrograms = [], researchTargets = [], schemas = [] } = $props();

    const breadcrumbs: BreadcrumbItem[] = [
        { title: 'Permintaan Review', href: '#' },
        { title: 'Penelitian', href: '#' },
        { title: 'Proposal', href: '/apply/research/proposal' },
        { title: 'Buat Baru', href: '#' },
    ];

    const form = useForm({
        leader_name: '',
        leader_nidn: '',
        leader_nuptk: '',
        study_program_id: '',
        research_schema_id: '',
        title: '',
        budget: null,

        research_target_id: '',
        proposal_path: '', // Changed from proposal_file
        members: [] as { name: string }[],
    });

    function submit() {
        if (uploadState.isUploading) return;
        $form.post(route('apply.research.proposal.store'), {
            forceFormData: true,
            onSuccess: () => {
                toast.success('Proposal berhasil disimpan.');
            },
            onError: () => {
                toast.error('Gagal menyimpan proposal. Periksa kembali input Anda.');
            },
        });
    }
</script>

<svelte:head>
    <title>Buat Proposal Baru</title>
</svelte:head>

<AppLayout {breadcrumbs}>
    <LayoutComposer>
        {#snippet header()}
            <Heading title="Buat Proposal Baru" description="Isi form berikut untuk mengajukan proposal penelitian baru." />
        {/snippet}

        <form
            onsubmit={(e) => {
                e.preventDefault();
                submit();
            }}
            class="space-y-6"
        >
            <ProposalForm
                bind:form={$form}
                type="research"
                mode="create"
                data={{
                    studyPrograms,
                    targets: researchTargets, // This is mapped to data.targets in ProposalForm
                    schemas,
                }}
            />

            <div class="flex justify-end items-center gap-4">
                {#if uploadState.isUploading}
                    <span class="text-sm text-muted-foreground animate-pulse">Mengunggah file...</span>
                {/if}
                <Button type="submit" disabled={$form.processing || uploadState.isUploading}>
                    {#if $form.processing}
                        Menyimpan...
                    {:else}
                        Simpan Proposal
                    {/if}
                </Button>
            </div>
        </form>
    </LayoutComposer>
</AppLayout>
