<script lang="ts">
    import LayoutComposer from '@/layouts/LayoutComposer.svelte';
    import AppLayout from '@/layouts/AppLayout.svelte';
    import { type BreadcrumbItem } from '@/types';
    import Heading from '@/components/Heading.svelte';
    import { useForm } from '@inertiajs/svelte';
    import { Button } from '@/components/ui/button';
    import * as Card from '@/components/ui/card';
    import * as RadioGroup from '@/components/ui/radio-group';
    import Label from '@/components/ui/label/label.svelte';
    import FileUpload from '@/components/FileUpload.svelte';
    import { StorageUploadAction } from '@/data/storage-upload';
    import { toast } from 'svelte-sonner';
    import { uploadState } from '@/stores/upload-state.svelte';
    import { DownloadIcon, FileTextIcon, CheckCircle2Icon, XCircleIcon } from 'lucide-svelte';

    const breadcrumbs: BreadcrumbItem[] = [
        { title: 'Permintaan Review', href: '#' },
        { title: 'Etik', href: '#' },
        { title: 'Proposal', href: route('apply.ethics.proposal.index') },
        { title: 'Buat Pengajuan', href: '#' },
    ];

    // --- Template definitions (hardcoded for rich UI) ---

    interface TemplateFile {
        key: string;
        name: string;
        description: string;
        path: string;
        required: boolean;
    }

    const clinicalTemplates: TemplateFile[] = [
        {
            key: 'surat_pengantar_mahasiswa',
            name: '1. Surat Pengantar (Mahasiswa Itekes Bali)',
            description: 'Surat pengantar untuk mahasiswa Itekes Bali.',
            path: '/assets/templates/form-ajuan-untuk-etik-klinik-atau-uji-coba-hewan/1. SURAT PENGANTAR (Untuk MAHASISWA Itekes Bali).docx',
            required: true,
        },
        {
            key: 'surat_pengantar_umum',
            name: '1. Surat Pengantar (Umum)',
            description: 'Surat pengantar untuk pengajuan dari luar Itekes Bali.',
            path: '/assets/templates/form-ajuan-untuk-etik-klinik-atau-uji-coba-hewan/1. SURAT PENGANTAR (Untuk Umum).docx',
            required: false,
        },
        {
            key: 'form_pengajuan_etik',
            name: '2. Form Pengajuan Etik',
            description: 'Formulir pengajuan etik yang harus diisi lengkap.',
            path: '/assets/templates/form-ajuan-untuk-etik-klinik-atau-uji-coba-hewan/2. revisi_2  FORM PENGAJUAN ETIK.docx',
            required: true,
        },
        {
            key: 'informed_consent',
            name: '3. Informed Consent',
            description: 'Dokumen persetujuan informed consent.',
            path: '/assets/templates/form-ajuan-untuk-etik-klinik-atau-uji-coba-hewan/3. Revisi_3  INFORMED CONSENT.docx',
            required: true,
        },
        {
            key: 'protokol_penelitian',
            name: '4. Protokol Penelitian Etik',
            description: 'Protokol penelitian etik Itekes Bali.',
            path: '/assets/templates/form-ajuan-untuk-etik-klinik-atau-uji-coba-hewan/4  PROTOKOL PENELITIAN ETIK ITEKES BALI.docx',
            required: true,
        },
        {
            key: 'surat_pernyataan_peneliti',
            name: '5. Surat Pernyataan Peneliti',
            description: 'Surat pernyataan dari peneliti.',
            path: '/assets/templates/form-ajuan-untuk-etik-klinik-atau-uji-coba-hewan/5 SURAT PERNYATAAN  PENELITI.docx',
            required: true,
        },
        {
            key: 'surat_pernyataan_tidak_riset',
            name: '6. Surat Pernyataan Tidak Melakukan Riset Sebelum EC',
            description: 'Pernyataan bahwa peneliti belum melakukan riset sebelum mendapat EC.',
            path: '/assets/templates/form-ajuan-untuk-etik-klinik-atau-uji-coba-hewan/6. SURAT PERNYATAAN TIDAK MELAKUKAN RISET SEBELUM EC.docx',
            required: true,
        },
        {
            key: 'cv_peneliti_pembimbing',
            name: '7. CV Peneliti dan CV Pembimbing',
            description: 'Curriculum Vitae peneliti dan pembimbing.',
            path: '/assets/templates/form-ajuan-untuk-etik-klinik-atau-uji-coba-hewan/7 CV PENELITI dan CV PEMBIMBING.docx',
            required: true,
        },
    ];

    const nonClinicalTemplates: TemplateFile[] = [
        {
            key: 'surat_pengantar_mahasiswa',
            name: '1. Surat Pengantar (Mahasiswa Itekes Bali)',
            description: 'Surat pengantar untuk mahasiswa Itekes Bali.',
            path: '/assets/templates/form-ajuan-untuk-etik-non-klinis/1. SURAT PENGANTAR (Untuk MAHASISWA Itekes Bali).docx',
            required: true,
        },
        {
            key: 'surat_pengantar_umum',
            name: '1. Surat Pengantar (Umum)',
            description: 'Surat pengantar untuk pengajuan dari luar Itekes Bali.',
            path: '/assets/templates/form-ajuan-untuk-etik-non-klinis/1. SURAT PENGANTAR (Untuk Umum).docx',
            required: false,
        },
        {
            key: 'form_pengajuan_etik',
            name: '2. Form Pengajuan Etik',
            description: 'Formulir pengajuan etik yang harus diisi lengkap.',
            path: '/assets/templates/form-ajuan-untuk-etik-non-klinis/2. revisi_2  FORM PENGAJUAN ETIK.docx',
            required: true,
        },
        {
            key: 'informed_consent',
            name: '3. Informed Consent',
            description: 'Dokumen persetujuan informed consent.',
            path: '/assets/templates/form-ajuan-untuk-etik-non-klinis/3. Revisi_3  INFORMED CONSENT.docx',
            required: true,
        },
        {
            key: 'protokol_penelitian',
            name: '4. Protokol Penelitian Etik',
            description: 'Protokol penelitian etik Itekes Bali.',
            path: '/assets/templates/form-ajuan-untuk-etik-non-klinis/4  PROTOKOL PENELITIAN ETIK ITEKES BALI.docx',
            required: true,
        },
        {
            key: 'surat_pernyataan_peneliti',
            name: '5. Surat Pernyataan Peneliti',
            description: 'Surat pernyataan dari peneliti.',
            path: '/assets/templates/form-ajuan-untuk-etik-non-klinis/5 SURAT PERNYATAAN  PENELITI.docx',
            required: true,
        },
        {
            key: 'surat_pernyataan_tidak_riset',
            name: '6. Surat Pernyataan Tidak Melakukan Riset Sebelum EC',
            description: 'Pernyataan bahwa peneliti belum melakukan riset sebelum mendapat EC.',
            path: '/assets/templates/form-ajuan-untuk-etik-non-klinis/6. SURAT PERNYATAAN TIDAK MELAKUKAN RISET SEBELUM EC.docx',
            required: true,
        },
        {
            key: 'cv_peneliti_pembimbing',
            name: '7. CV Peneliti dan CV Pembimbing',
            description: 'Curriculum Vitae peneliti dan pembimbing.',
            path: '/assets/templates/form-ajuan-untuk-etik-non-klinis/7 CV PENELITI dan CV PEMBIMBING.docx',
            required: true,
        },
        {
            key: 'form_etik_hewan_coba',
            name: 'Form Etik Hewan Coba',
            description: 'Formulir etik khusus untuk penelitian hewan coba.',
            path: '/assets/templates/form-ajuan-untuk-etik-non-klinis/FORM ETIK HEWAN COBA ITEKES.doc',
            required: false,
        },
        {
            key: 'lembar_perlakuan',
            name: 'Lembar Perlakuan Sebelum, Selama, dan Sesudah Penelitian',
            description: 'Lembar pencatatan perlakuan subjek penelitian.',
            path: '/assets/templates/form-ajuan-untuk-etik-non-klinis/Lembar Perlakuan Sebelum, Selama, dan Sesudah Penelitian_ITEKES.docx',
            required: false,
        },
    ];

    // --- State ---

    let selectedCategory = $state<string>('');
    let templates = $derived(selectedCategory === 'clinical' ? clinicalTemplates : selectedCategory === 'non_clinical' ? nonClinicalTemplates : []);

    // Track uploaded files per template_key
    let uploadedFiles = $state<Record<string, { file_path: string; original_name: string }>>({});

    function handleFileUploaded(key: string, filePath: string, originalName: string) {
        uploadedFiles[key] = { file_path: filePath, original_name: originalName };
    }

    // Check if all required templates are uploaded
    let allRequiredUploaded = $derived(() => {
        if (!templates.length) return false;
        return templates.filter((t) => t.required).every((t) => uploadedFiles[t.key]?.file_path);
    });

    let hasAnyUpload = $derived(() => {
        return Object.keys(uploadedFiles).length > 0;
    });

    const form = useForm({
        category: '',
        files: [] as any[],
    });

    function submit() {
        if (uploadState.isUploading) return;
        if (!selectedCategory) {
            toast.error('Silakan pilih kategori terlebih dahulu.');
            return;
        }
        if (!allRequiredUploaded()) {
            toast.error('Silakan unggah semua dokumen yang wajib (bertanda *).');
            return;
        }

        const filesPayload = Object.entries(uploadedFiles).map(([key, val]) => ({
            template_key: key,
            file_path: val.file_path,
            original_name: val.original_name,
        }));

        $form.category = selectedCategory;
        $form.files = filesPayload;

        $form.post(route('apply.ethics.proposal.store'), {
            onSuccess: () => {
                toast.success('Pengajuan etik berhasil dikirim.');
            },
            onError: () => {
                toast.error('Gagal mengirim pengajuan. Periksa kembali dokumen Anda.');
            },
        });
    }
</script>

<svelte:head>
    <title>Buat Pengajuan Etik</title>
</svelte:head>

<AppLayout {breadcrumbs}>
    <LayoutComposer>
        {#snippet header()}
            <Heading title="Buat Pengajuan Etik" description="Pilih kategori, unduh template, isi, dan unggah kembali dokumen yang diperlukan." />
        {/snippet}

        <div class="space-y-6">
            <!-- Step 1: Category Selection -->
            <Card.Root>
                <Card.Header>
                    <Card.Title>Langkah 1: Pilih Kategori</Card.Title>
                    <Card.Description>Pilih jenis pengajuan ethical clearance Anda.</Card.Description>
                </Card.Header>
                <Card.Content>
                    <RadioGroup.Root
                        bind:value={selectedCategory}
                        onValueChange={() => {
                            uploadedFiles = {};
                        }}
                    >
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <label
                                class="relative flex cursor-pointer items-start gap-3 rounded-lg border p-4 transition-colors {selectedCategory ===
                                'clinical'
                                    ? 'border-primary bg-primary/5 ring-1 ring-primary'
                                    : 'hover:bg-muted/50'}"
                            >
                                <RadioGroup.Item value="clinical" class="mt-0.5" />
                                <div>
                                    <p class="font-semibold">Etik Klinik atau Uji Coba Hewan</p>
                                    <p class="text-sm text-muted-foreground mt-1">
                                        Untuk penelitian yang melibatkan subjek manusia atau hewan percobaan.
                                    </p>
                                </div>
                            </label>
                            <label
                                class="relative flex cursor-pointer items-start gap-3 rounded-lg border p-4 transition-colors {selectedCategory ===
                                'non_clinical'
                                    ? 'border-primary bg-primary/5 ring-1 ring-primary'
                                    : 'hover:bg-muted/50'}"
                            >
                                <RadioGroup.Item value="non_clinical" class="mt-0.5" />
                                <div>
                                    <p class="font-semibold">Etik Non Klinis</p>
                                    <p class="text-sm text-muted-foreground mt-1">
                                        Untuk penelitian yang tidak melibatkan subjek manusia atau hewan secara langsung.
                                    </p>
                                </div>
                            </label>
                        </div>
                    </RadioGroup.Root>
                </Card.Content>
            </Card.Root>

            {#if selectedCategory}
                <!-- Step 2: Download Templates -->
                <Card.Root>
                    <Card.Header>
                        <Card.Title>Langkah 2: Unduh Template</Card.Title>
                        <Card.Description>Unduh template berikut, isi sesuai petunjuk, lalu unggah kembali pada langkah selanjutnya.</Card.Description
                        >
                    </Card.Header>
                    <Card.Content>
                        <div class="space-y-2">
                            {#each templates as template (template.key)}
                                <div class="flex items-center justify-between border rounded-lg p-3 hover:bg-muted/50 transition-colors">
                                    <div class="flex items-center gap-3 flex-1 min-w-0">
                                        <FileTextIcon class="h-5 w-5 text-muted-foreground shrink-0" />
                                        <div class="min-w-0">
                                            <p class="font-medium text-sm truncate">
                                                {template.name}
                                                {#if template.required}
                                                    <span class="text-destructive">*</span>
                                                {/if}
                                            </p>
                                            <p class="text-xs text-muted-foreground">{template.description}</p>
                                        </div>
                                    </div>
                                    <a
                                        href={template.path}
                                        download
                                        class="inline-flex items-center gap-1.5 rounded-md bg-primary/10 px-3 py-1.5 text-xs font-medium text-primary hover:bg-primary/20 transition-colors shrink-0"
                                    >
                                        <DownloadIcon class="h-3.5 w-3.5" />
                                        Unduh
                                    </a>
                                </div>
                            {/each}
                        </div>
                        <p class="text-xs text-muted-foreground mt-3">
                            Dokumen bertanda <span class="text-destructive font-medium">*</span> wajib diunggah.
                        </p>
                    </Card.Content>
                </Card.Root>

                <!-- Step 3: Upload Completed Files -->
                <Card.Root>
                    <Card.Header>
                        <Card.Title>Langkah 3: Unggah Dokumen</Card.Title>
                        <Card.Description
                            >Unggah dokumen yang telah diisi. Format yang diterima: PDF, DOC, DOCX. Maksimal 4MB per file.</Card.Description
                        >
                    </Card.Header>
                    <Card.Content>
                        <div class="space-y-4">
                            {#each templates as template (template.key)}
                                <div class="border rounded-lg p-4 space-y-2">
                                    <div class="flex items-center justify-between">
                                        <Label class="font-medium">
                                            {template.name}
                                            {#if template.required}
                                                <span class="text-destructive">*</span>
                                            {/if}
                                        </Label>
                                        {#if uploadedFiles[template.key]?.file_path}
                                            <span class="flex items-center gap-1 text-xs text-green-600">
                                                <CheckCircle2Icon class="h-3.5 w-3.5" />
                                                Terunggah
                                            </span>
                                        {:else if template.required}
                                            <span class="flex items-center gap-1 text-xs text-muted-foreground">
                                                <XCircleIcon class="h-3.5 w-3.5" />
                                                Belum diunggah
                                            </span>
                                        {/if}
                                    </div>
                                    <FileUpload
                                        action={StorageUploadAction.ETHICS_PROPOSAL}
                                        bind:value={
                                            () => uploadedFiles[template.key]?.file_path ?? '',
                                            (v) => {
                                                if (!uploadedFiles[template.key]) {
                                                    uploadedFiles[template.key] = { file_path: v || '', original_name: '' };
                                                } else {
                                                    uploadedFiles[template.key].file_path = v || '';
                                                }
                                            }
                                        }
                                        bind:fileName={
                                            () => uploadedFiles[template.key]?.original_name ?? null,
                                            (v) => {
                                                if (!uploadedFiles[template.key]) {
                                                    uploadedFiles[template.key] = { file_path: '', original_name: v || '' };
                                                } else {
                                                    uploadedFiles[template.key].original_name = v || '';
                                                }
                                            }
                                        }
                                        accept=".pdf,.doc,.docx"
                                        label="Pilih file atau seret ke sini"
                                    />
                                </div>
                            {/each}
                        </div>
                    </Card.Content>
                </Card.Root>

                <!-- Submit -->
                <div class="flex justify-end items-center gap-4">
                    {#if uploadState.isUploading}
                        <span class="text-sm text-muted-foreground animate-pulse">Mengunggah file...</span>
                    {/if}
                    <Button onclick={submit} disabled={uploadState.isUploading || !allRequiredUploaded()} size="lg">Kirim Pengajuan</Button>
                </div>
            {/if}
        </div>
    </LayoutComposer>
</AppLayout>
