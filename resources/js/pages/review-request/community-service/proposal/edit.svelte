<script lang="ts">
    import LayoutComposer from '@/layouts/layout-composer.svelte';
    import AppLayout from '@/layouts/app-layout.svelte';
    import { type BreadcrumbItem } from '@/types';
    import Heading from '@/components/heading.svelte';
    import { useForm } from '@inertiajs/svelte';
    import { untrack } from 'svelte';
    import { Button } from '@/components/ui/button';
    import * as Alert from '@/components/ui/alert';
    import ProposalForm from '@/components/review-request/proposal-form.svelte';
    import RevisionCommentSheet from '@/components/review-request/revision-comment-sheet.svelte';
    import { uploadState } from '@/stores/upload-state.svelte';
    import { toast } from 'svelte-sonner';

    let { submission, detail, studyPrograms = [], communityServiceTargets = [], schemas = [] } = $props();

    const breadcrumbs: BreadcrumbItem[] = [
        { title: 'Permintaan Review', href: '#' },
        { title: 'Pengabdian Masyarakat', href: '#' },
        { title: 'Proposal', href: '/apply/community-service/proposal' },
        { title: 'Revisi', href: '#' },
    ];

    const form = useForm(
        untrack(() => ({
            leader_name: detail.leader_name || '',
            leader_nidn: detail.leader_nidn || '',
            leader_nuptk: detail.leader_nuptk || '',
            study_program_id: detail.study_program_id || '',
            community_service_schema_id: detail.community_service_schema_id || '',
            title: detail.title || '',
            budget: detail.budget || null,

            community_service_target_id: detail.community_service_target_id || '',
            proposal_path: detail.proposal_path || '',
            members: detail.members ? detail.members.map((m: any) => ({ name: m.name })) : [],
        })),
    );

    function submit() {
        if (uploadState.isUploading) return;
        $form.post(route('apply.community_service.proposal.revise', submission.id), {
            forceFormData: true,
            onSuccess: () => {
                toast.success('Revisi proposal berhasil dikirim.');
            },
            onError: () => {
                toast.error('Gagal mengirim revisi. Periksa kembali input Anda.');
            },
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
            </div>
        {/snippet}

        {#snippet actions()}
            <RevisionCommentSheet comments={detail?.comments} />
        {/snippet}

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
                type="community-service"
                mode="revise"
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
                        Kirim Revisi
                    {/if}
                </Button>
            </div>
        </form>
    </LayoutComposer>
</AppLayout>
