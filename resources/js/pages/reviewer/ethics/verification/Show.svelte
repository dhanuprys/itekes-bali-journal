<script lang="ts">
    import AppLayout from '@/layouts/AppLayout.svelte';
    import LayoutComposer from '@/layouts/LayoutComposer.svelte';
    import Heading from '@/components/Heading.svelte';
    import { type BreadcrumbItem } from '@/types';
    import * as Card from '@/components/ui/card';
    import { Badge } from '@/components/ui/badge';
    import { Button } from '@/components/ui/button';
    import { Textarea } from '@/components/ui/textarea';
    import { Label } from '@/components/ui/label';
    import { Checkbox } from '@/components/ui/checkbox';
    import * as AlertDialog from '@/components/ui/alert-dialog';
    import { router, page } from '@inertiajs/svelte';
    import { FileTextIcon, CheckIcon, XIcon, UserIcon, CheckCircleIcon, XCircleIcon, ClockIcon } from 'lucide-svelte';
    import { toast } from 'svelte-sonner';

    let { submission } = $props();
    const currentUser = $derived($page.props.auth.user);

    const breadcrumbs: BreadcrumbItem[] = [
        { title: 'Reviewer Area', href: '#' },
        { title: 'Etik', href: '#' },
        { title: 'Verifikasi Dokumen', href: '#' },
    ];

    let processing = $state(false);
    let verifyNotes = $state('');
    let hasCheckedDocument = $state(false);
    let confirmDialogTarget = $state<'approved' | 'rejected' | null>(null);

    const output = $derived(submission.latest_output);
    const verifications = $derived(output?.verifications || []);

    // Check if current user has already verified this specific output
    const currentUserVerification = $derived(verifications.find((v: any) => v.user_id === currentUser.id));

    function triggerVerify(status: 'approved' | 'rejected') {
        if (status === 'rejected' && !verifyNotes) {
            toast.error('Komentar wajib diisi jika menolak dokumen.');
            return;
        }
        confirmDialogTarget = status;
    }

    function handleVerify() {
        const status = confirmDialogTarget;
        if (!status) return;

        processing = true;
        router.post(
            route('review.ethics.verification.verify', submission.id),
            {
                status,
                notes: verifyNotes,
            },
            {
                onSuccess: () => {
                    toast.success('Verifikasi berhasil disimpan.');
                },
                onFinish: () => {
                    processing = false;
                    confirmDialogTarget = null;
                },
            },
        );
    }
</script>

<AlertDialog.Root open={!!confirmDialogTarget} onOpenChange={(v) => !v && (confirmDialogTarget = null)}>
    <AlertDialog.Content>
        <AlertDialog.Header>
            <AlertDialog.Title>
                Konfirmasi {confirmDialogTarget === 'approved' ? 'Persetujuan' : 'Penolakan'}
            </AlertDialog.Title>
            <AlertDialog.Description>
                {#if confirmDialogTarget === 'approved'}
                    Apakah Anda yakin ingin menyetujui dokumen ini? Anda menyatakan bahwa dokumen ini telah sesuai dengan standar (LGTM).
                {:else}
                    Apakah Anda yakin ingin menolak dokumen ini? Dokumen akan dikembalikan ke operator untuk direvisi.
                {/if}
            </AlertDialog.Description>
        </AlertDialog.Header>
        <AlertDialog.Footer>
            <AlertDialog.Cancel disabled={processing}>Batal</AlertDialog.Cancel>
            <AlertDialog.Action
                class={confirmDialogTarget === 'approved' ? 'bg-green-600 hover:bg-green-700' : 'bg-red-600 hover:bg-red-700'}
                disabled={processing}
                onclick={(e) => {
                    e.preventDefault();
                    handleVerify();
                }}
            >
                {processing ? 'Menyimpan...' : 'Ya, Lanjutkan'}
            </AlertDialog.Action>
        </AlertDialog.Footer>
    </AlertDialog.Content>
</AlertDialog.Root>

<svelte:head>
    <title>Detail Verifikasi Etik</title>
</svelte:head>

<AppLayout {breadcrumbs}>
    <LayoutComposer>
        {#snippet header()}
            <Heading title="Verifikasi Dokumen Ethical Clearance" description="Periksa dokumen yang telah diunggah operator." />
        {/snippet}

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-6">
                <!-- Document Viewer / Details -->
                <Card.Root>
                    <Card.Header>
                        <Card.Title>Dokumen Ethical Clearance</Card.Title>
                        <Card.Description>Silakan periksa dokumen berikut sebelum memverifikasi.</Card.Description>
                    </Card.Header>
                    <Card.Content>
                        <div class="p-6 border rounded-lg bg-muted/30 text-center space-y-4">
                            <FileTextIcon class="h-16 w-16 mx-auto text-blue-500" />
                            <div>
                                <h3 class="font-medium text-lg">Dokumen EC</h3>
                                <p class="text-sm text-muted-foreground mt-1">Sertifikat / Lembar Ethical Clearance</p>
                            </div>
                            <Button variant="outline" class="gap-2" href={`/storage/${output?.document_path}`} target="_blank">
                                <FileTextIcon class="h-4 w-4" />
                                Buka Dokumen Asli
                            </Button>
                        </div>
                    </Card.Content>
                </Card.Root>

                <!-- Current User Verification Form -->
                {#if !currentUserVerification}
                    <Card.Root class="border-orange-200 shadow-sm pt-0">
                        <Card.Header class="bg-orange-500/10 p-4">
                            <Card.Title class="text-orange-700 flex items-center gap-2">
                                <CheckCircleIcon class="h-5 w-5" />
                                Form Verifikasi Anda
                            </Card.Title>
                        </Card.Header>
                        <Card.Content class="pt-6 space-y-4">
                            <div class="space-y-2">
                                <Label>Catatan Tambahan (Opsional, Wajib jika Menolak)</Label>
                                <Textarea
                                    bind:value={verifyNotes}
                                    placeholder="Masukkan catatan atau alasan jika dokumen ditolak..."
                                    class="min-h-[100px]"
                                />
                            </div>

                            <div class="flex items-center space-x-3 bg-muted/50 p-4 rounded-lg border">
                                <Checkbox id="check-document" bind:checked={hasCheckedDocument} />
                                <Label for="check-document" class="font-medium cursor-pointer leading-snug">
                                    Saya telah membaca, meninjau, dan memeriksa dokumen ini dengan saksama.
                                </Label>
                            </div>

                            <div class="flex gap-3 pt-2">
                                <Button
                                    variant="destructive"
                                    class="flex-1 gap-2"
                                    disabled={processing || !hasCheckedDocument}
                                    onclick={() => triggerVerify('rejected')}
                                >
                                    <XIcon class="h-4 w-4" />
                                    Tolak Dokumen
                                </Button>
                                <Button
                                    class="flex-1 gap-2 bg-green-600 hover:bg-green-700"
                                    disabled={processing || !hasCheckedDocument}
                                    onclick={() => triggerVerify('approved')}
                                >
                                    <CheckIcon class="h-4 w-4" />
                                    Setujui Dokumen
                                </Button>
                            </div>
                        </Card.Content>
                    </Card.Root>
                {:else}
                    <Card.Root class="bg-muted/50">
                        <Card.Content class="pt-6 text-center space-y-3">
                            {#if currentUserVerification.status === 'approved'}
                                <CheckCircleIcon class="h-12 w-12 mx-auto text-green-500" />
                                <div>
                                    <h3 class="font-bold text-lg text-green-600">Anda telah menyetujui dokumen ini</h3>
                                    <p class="text-sm text-muted-foreground mt-1">Verifikasi Anda tersimpan. Menunggu reviewer lain jika ada.</p>
                                </div>
                            {:else}
                                <XCircleIcon class="h-12 w-12 mx-auto text-red-500" />
                                <div>
                                    <h3 class="font-bold text-lg text-red-600">Anda menolak dokumen ini</h3>
                                    <p class="text-sm text-muted-foreground mt-1">Dikembalikan ke operator untuk perbaikan.</p>
                                </div>
                            {/if}
                            {#if currentUserVerification.notes}
                                <div class="mt-4 p-4 bg-background border rounded-lg text-left">
                                    <span class="text-xs font-medium text-muted-foreground uppercase">Catatan Anda:</span>
                                    <p class="text-sm mt-1">{currentUserVerification.notes}</p>
                                </div>
                            {/if}
                        </Card.Content>
                    </Card.Root>
                {/if}
            </div>

            <!-- Sidebar Info -->
            <div class="space-y-6">
                <!-- Application Info -->
                <Card.Root>
                    <Card.Header>
                        <Card.Title>Informasi Pengajuan</Card.Title>
                    </Card.Header>
                    <Card.Content class="space-y-4">
                        <div class="space-y-1">
                            <span class="text-xs text-muted-foreground">Pengaju</span>
                            <div class="font-medium flex items-center gap-2">
                                <UserIcon class="h-4 w-4 text-muted-foreground" />
                                {submission.user?.name}
                            </div>
                        </div>
                        <div class="space-y-1">
                            <span class="text-xs text-muted-foreground">Judul</span>
                            <p class="text-sm font-medium leading-tight">{submission.latest_detail?.title}</p>
                        </div>
                    </Card.Content>
                </Card.Root>

                <!-- Status Reviewer Lain -->
                <Card.Root>
                    <Card.Header>
                        <Card.Title>Status Reviewer</Card.Title>
                        <Card.Description>Status verifikasi dari semua reviewer yang ditugaskan.</Card.Description>
                    </Card.Header>
                    <Card.Content class="space-y-4">
                        {#each submission.reviewers as reviewer}
                            {@const revVerif = verifications.find((v: any) => v.user_id === reviewer.user_id)}
                            <div class="flex items-start justify-between p-3 rounded-lg border bg-muted/20">
                                <div class="space-y-1">
                                    <span class="text-sm font-medium block">
                                        {reviewer.user?.name}
                                        {#if reviewer.user_id === currentUser.id}
                                            <span class="text-xs text-muted-foreground ml-1">(Anda)</span>
                                        {/if}
                                    </span>
                                    {#if revVerif}
                                        {#if revVerif.status === 'approved'}
                                            <Badge variant="outline" class="text-green-600 border-green-200 bg-green-50">Disetujui</Badge>
                                        {:else}
                                            <Badge variant="destructive">Ditolak</Badge>
                                        {/if}
                                        {#if revVerif.notes}
                                            <p class="text-xs text-muted-foreground mt-2 italic border-l-2 pl-2">"{revVerif.notes}"</p>
                                        {/if}
                                    {:else}
                                        <Badge variant="outline" class="text-orange-500 border-orange-200 bg-orange-50">Menunggu</Badge>
                                    {/if}
                                </div>
                                <div>
                                    {#if revVerif}
                                        {#if revVerif.status === 'approved'}
                                            <CheckCircleIcon class="h-5 w-5 text-green-500" />
                                        {:else}
                                            <XCircleIcon class="h-5 w-5 text-red-500" />
                                        {/if}
                                    {:else}
                                        <ClockIcon class="h-5 w-5 text-orange-400" />
                                    {/if}
                                </div>
                            </div>
                        {/each}
                    </Card.Content>
                </Card.Root>
            </div>
        </div>
    </LayoutComposer>
</AppLayout>
