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

    let { submission, detail, studyPrograms = [], communityServiceSchemas = [], communityServiceTargets = [] } = $props();

    const breadcrumbs: BreadcrumbItem[] = [
        { title: 'Permintaan Review', href: '#' },
        { title: 'Pengabdian Masyarakat', href: '#' },
        { title: 'Proposal', href: '/apply/community-service/proposal' },
        { title: 'Revisi', href: '#' },
    ];

    const form = useForm({
        leader_name: detail.leader_name || '',
        leader_nidn: detail.leader_nidn || '',
        study_program_id: detail.study_program_id || '',
        title: detail.title || '',
        budget: detail.budget || null,
        community_service_schema_id: detail.community_service_schema_id || '',
        community_service_target_id: detail.community_service_target_id || '',
        proposal_file: null as File | null,
        members: detail.members ? detail.members.map((m: any) => ({ name: m.name })) : [],
    });

    function submit() {
        $form.post(route('apply.community_service.proposal.revise', submission.id), {
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
                    form={$form}
                    type="community-service"
                    mode="revise"
                    data={{
                        studyPrograms,
                        schemas: communityServiceSchemas,
                        targets: communityServiceTargets,
                    }}
                />

                <div class="flex justify-end">
                    <Button type="submit" disabled={$form.processing}>
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
