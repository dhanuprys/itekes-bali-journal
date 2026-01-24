<script lang="ts">
    import LayoutComposer from '@/layouts/LayoutComposer.svelte';
    import AppLayout from '@/layouts/AppLayout.svelte';
    import { type BreadcrumbItem } from '@/types';
    import Heading from '@/components/Heading.svelte';
    import { useForm } from '@inertiajs/svelte';
    import { Button } from '@/components/ui/button';
    import ProposalForm from '@/components/review-request/ProposalForm.svelte';

    let { studyPrograms = [], communityServiceSchemas = [], communityServiceTargets = [] } = $props();

    const breadcrumbs: BreadcrumbItem[] = [
        { title: 'Permintaan Review', href: '#' },
        { title: 'Pengabdian Masyarakat', href: '#' },
        { title: 'Proposal', href: '/apply/community-service/proposal' },
        { title: 'Buat Baru', href: '#' },
    ];

    const form = useForm({
        leader_name: '',
        leader_nidn: '',
        study_program_id: '',
        title: '',
        budget: null,
        community_service_schema_id: '',
        community_service_target_id: '',
        proposal_file: null as File | null,
        members: [] as { name: string }[],
    });

    function submit() {
        $form.post(route('apply.community_service.proposal.store'), {
            forceFormData: true,
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

        {#snippet children()}
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
                    mode="create"
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
                            Simpan Proposal
                        {/if}
                    </Button>
                </div>
            </form>
        {/snippet}
    </LayoutComposer>
</AppLayout>
