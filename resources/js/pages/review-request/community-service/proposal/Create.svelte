<script lang="ts">
    import LayoutComposer from '@/layouts/LayoutComposer.svelte';
    import AppLayout from '@/layouts/AppLayout.svelte';
    import { type BreadcrumbItem } from '@/types';
    import Heading from '@/components/Heading.svelte';
    import { useForm } from '@inertiajs/svelte';
    import { Button } from '@/components/ui/button';
    import ProposalForm from '@/components/review-request/ProposalForm.svelte';

    import { uploadState } from '@/stores/upload-state.svelte';
    import { toast } from 'svelte-sonner';

    let { studyPrograms = [], communityServiceTargets = [], schemas = [] } = $props();

    const breadcrumbs: BreadcrumbItem[] = [
        { title: 'Permintaan Review', href: '#' },
        { title: 'Pengabdian Masyarakat', href: '#' },
        { title: 'Proposal', href: '/apply/community-service/proposal' },
        { title: 'Buat Baru', href: '#' },
    ];

    const form = useForm({
        leader_name: '',
        leader_nidn: '',
        leader_nuptk: '',
        study_program_id: '',
        community_service_schema_id: '',
        title: '',
        budget: null,

        community_service_target_id: '',
        proposal_path: '', // Changed from proposal_file
        members: [] as { name: string }[],
    });

    function submit() {
        if (uploadState.isUploading) return;
        $form.post(route('apply.community_service.proposal.store'), {
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
    <title>Buat Proposal Pengabdian</title>
</svelte:head>

<AppLayout {breadcrumbs}>
    <LayoutComposer>
        {#snippet header()}
            <Heading title="Buat Proposal Baru" description="Isi form berikut untuk mengajukan proposal pengabdian masyarakat." />
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
                type="community-service"
                mode="create"
                data={{
                    studyPrograms,
                    targets: communityServiceTargets,
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
