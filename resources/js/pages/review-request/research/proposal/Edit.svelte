<script lang="ts">
    import LayoutComposer from '@/layouts/LayoutComposer.svelte';
    import AppLayout from '@/layouts/AppLayout.svelte';
    import { type BreadcrumbItem } from '@/types';
    import Heading from '@/components/Heading.svelte';
    import { useForm } from '@inertiajs/svelte';
    import { Button } from '@/components/ui/button';
    import * as Alert from '@/components/ui/alert';
    import ProposalForm from '@/components/review-request/ProposalForm.svelte';
    import RevisionCommentSheet from '@/components/review-request/RevisionCommentSheet.svelte';
    import { uploadState } from '@/stores/upload-state.svelte';

    let { submission, detail, studyPrograms = [], researchSchemas = [], researchTargets = [] } = $props();

    const breadcrumbs: BreadcrumbItem[] = [
        { title: 'Permintaan Review', href: '#' },
        { title: 'Penelitian', href: '#' },
        { title: 'Proposal', href: '/apply/research/proposal' },
        { title: 'Revisi', href: '#' },
    ];

    const form = useForm({
        leader_name: detail.leader_name || '',
        leader_nidn: detail.leader_nidn || '',
        study_program_id: detail.study_program_id || '',
        title: detail.title || '',
        budget: detail.budget || null,
        research_schema_id: detail.research_schema_id || '',
        research_target_id: detail.research_target_id || '',
        proposal_path: detail.proposal_path || '',
        members: detail.members ? detail.members.map((m: any) => ({ name: m.name })) : [],
    });

    function submit() {
        if (uploadState.isUploading) return;
        $form.post(route('apply.research.proposal.revise', submission.id), {
            forceFormData: true,
        });
    }
</script>

<svelte:head>
    <title>Revisi Proposal</title>
</svelte:head>

<AppLayout {breadcrumbs}>
    <LayoutComposer>
        {#snippet header()}
            <div class="flex items-center justify-between">
                <Heading title="Revisi Proposal" description="Perbarui proposal Anda berdasarkan masukan reviewer." />
                <RevisionCommentSheet comments={detail?.comments} />
            </div>
        {/snippet}

        {#snippet children()}
            {#if submission.status === 'revision_needed'}
                <div class="mb-6">
                    <Alert.Root variant="destructive">
                        <Alert.Title>Perhatian</Alert.Title>
                        <Alert.Description>
                            Proposal Anda memerlukan revisi. Silakan cek komentar reviewer melalui tombol di atas kanan dan unggah versi perbaikan.
                        </Alert.Description>
                    </Alert.Root>
                </div>
            {/if}

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
                    mode="revise"
                    data={{
                        studyPrograms,
                        schemas: researchSchemas,
                        targets: researchTargets,
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
                            Kirim Revisi
                        {/if}
                    </Button>
                </div>
            </form>
        {/snippet}
    </LayoutComposer>
</AppLayout>
