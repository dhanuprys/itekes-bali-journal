<script lang="ts">
    import { Link, page } from '@inertiajs/svelte';

    interface Props {
        canRegister: boolean;
    }

    let { canRegister }: Props = $props();
    let user = $derived($page.props.auth.user);

    let scrollY = $state(0);

    if (typeof window !== 'undefined') {
        window.addEventListener('scroll', () => {
            scrollY = window.scrollY;
        });
    }
</script>

<svelte:head>
    <title>ITEKES Bali Journal - Sistem Revisi Penelitian dan Pengabdian</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
</svelte:head>

<div class="bg-neutral-50 dark:bg-neutral-950 min-h-screen font-sans">
    <!-- Top Bar -->
    <div class="border-b border-neutral-200 dark:border-neutral-800 bg-white dark:bg-neutral-900">
        <div class="max-w-[1400px] mx-auto px-6 lg:px-12">
            <div class="h-14 flex items-center justify-between text-xs">
                <div class="flex items-center gap-6 text-neutral-600 dark:text-neutral-400">
                    <span>Institut Teknologi dan Kesehatan Bali</span>
                </div>
                <div class="flex items-center gap-4">
                    {#if user}
                        <Link
                            href={route('dashboard')}
                            class="text-neutral-900 dark:text-neutral-100 font-medium hover:text-blue-600 dark:hover:text-blue-400 transition-colors"
                        >
                            Dashboard →
                        </Link>
                    {:else}
                        <Link
                            href={route('login')}
                            class="text-neutral-600 dark:text-neutral-400 hover:text-neutral-900 dark:hover:text-neutral-100 transition-colors"
                        >
                            Masuk
                        </Link>
                        {#if canRegister}
                            <Link
                                href={route('register')}
                                class="px-4 py-1.5 bg-neutral-900 dark:bg-neutral-100 text-white dark:text-neutral-900 text-xs font-medium hover:bg-neutral-800 dark:hover:bg-neutral-200 transition-colors"
                            >
                                Daftar
                            </Link>
                        {/if}
                    {/if}
                </div>
            </div>
        </div>
    </div>

    <!-- Main Navigation -->
    <nav class="sticky top-0 z-40 bg-white/80 dark:bg-neutral-900/80 backdrop-blur-xl border-b border-neutral-200/50 dark:border-neutral-800/50">
        <div class="max-w-[1400px] mx-auto px-6 lg:px-12">
            <div class="h-20 flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <img src="/assets/images/itekes-bali.webp" alt="ITEKES" class="h-11 w-11" />
                    <div class="border-l border-neutral-300 dark:border-neutral-700 pl-4 h-8 flex flex-col justify-center">
                        <div class="text-sm font-semibold text-neutral-900 dark:text-neutral-100 leading-tight">ITEKES Journal</div>
                        <div class="text-[10px] text-neutral-500 dark:text-neutral-500 leading-tight">Sistem Revisi</div>
                    </div>
                </div>

                <div class="hidden md:flex items-center gap-8 text-sm">
                    <a
                        href="#tentang"
                        class="text-neutral-600 dark:text-neutral-400 hover:text-neutral-900 dark:hover:text-neutral-100 transition-colors">Tentang</a
                    >
                    <a
                        href="#fitur"
                        class="text-neutral-600 dark:text-neutral-400 hover:text-neutral-900 dark:hover:text-neutral-100 transition-colors">Fitur</a
                    >
                    <a
                        href="#alur"
                        class="text-neutral-600 dark:text-neutral-400 hover:text-neutral-900 dark:hover:text-neutral-100 transition-colors"
                        >Alur Kerja</a
                    >
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero -->
    <section class="relative overflow-hidden">
        <div class="max-w-[1400px] mx-auto px-6 lg:px-12 py-24 lg:py-32">
            <div class="grid lg:grid-cols-12 gap-12 items-center">
                <div class="lg:col-span-7">
                    <div
                        class="inline-block px-3 py-1 bg-blue-50 dark:bg-blue-950/30 border border-blue-200 dark:border-blue-900 text-blue-700 dark:text-blue-400 text-xs font-medium mb-6"
                    >
                        Platform Digital Terpadu
                    </div>

                    <h1 class="text-4xl lg:text-5xl xl:text-6xl font-light text-neutral-900 dark:text-neutral-100 mb-6 leading-[1.1]">
                        Sistem Manajemen<br />
                        <span class="font-semibold">Review Penelitian</span>
                    </h1>

                    <p class="text-lg text-neutral-600 dark:text-neutral-400 mb-8 max-w-xl leading-relaxed">
                        Platform komprehensif untuk mengelola proses review dan revisi penelitian serta pengabdian masyarakat dengan efisien dan
                        terstruktur.
                    </p>

                    {#if !user}
                        <div class="flex flex-col sm:flex-row gap-3">
                            <Link
                                href={route('login')}
                                class="inline-flex items-center justify-center px-6 py-3 bg-neutral-900 dark:bg-neutral-100 text-white dark:text-neutral-900 font-medium hover:bg-neutral-800 dark:hover:bg-neutral-200 transition-all group"
                            >
                                Mulai Menggunakan
                                <svg
                                    class="ml-2 w-4 h-4 group-hover:translate-x-0.5 transition-transform"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </Link>
                            <a
                                href="#fitur"
                                class="inline-flex items-center justify-center px-6 py-3 border border-neutral-300 dark:border-neutral-700 text-neutral-700 dark:text-neutral-300 font-medium hover:border-neutral-400 dark:hover:border-neutral-600 transition-colors"
                            >
                                Pelajari Lebih Lanjut
                            </a>
                        </div>
                    {/if}

                    <div class="mt-12 flex items-center gap-8 pt-8 border-t border-neutral-200 dark:border-neutral-800">
                        <div>
                            <div class="text-2xl font-semibold text-neutral-900 dark:text-neutral-100">100+</div>
                            <div class="text-sm text-neutral-500 dark:text-neutral-500">Usulan Diproses</div>
                        </div>
                        <div class="h-8 w-px bg-neutral-200 dark:bg-neutral-800"></div>
                        <div>
                            <div class="text-2xl font-semibold text-neutral-900 dark:text-neutral-100">50+</div>
                            <div class="text-sm text-neutral-500 dark:text-neutral-500">Reviewer Aktif</div>
                        </div>
                        <div class="h-8 w-px bg-neutral-200 dark:bg-neutral-800"></div>
                        <div>
                            <div class="text-2xl font-semibold text-neutral-900 dark:text-neutral-100">95%</div>
                            <div class="text-sm text-neutral-500 dark:text-neutral-500">Kepuasan Pengguna</div>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-5">
                    <div class="relative">
                        <div
                            class="absolute inset-0 bg-gradient-to-tr from-blue-100 to-indigo-100 dark:from-blue-950/30 dark:to-indigo-950/30 -rotate-3"
                        ></div>
                        <div class="relative bg-white dark:bg-neutral-900 border border-neutral-200 dark:border-neutral-800 p-8">
                            <img src="/assets/images/itekes-bali.webp" alt="ITEKES Bali" class="w-full h-auto opacity-90" />

                            <div class="mt-6 space-y-2">
                                <div class="flex items-center gap-2 text-sm">
                                    <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                                    <span class="text-neutral-600 dark:text-neutral-400">Sistem Online 24/7</span>
                                </div>
                                <div class="flex items-center gap-2 text-sm">
                                    <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
                                    <span class="text-neutral-600 dark:text-neutral-400">Tracking Real-time</span>
                                </div>
                                <div class="flex items-center gap-2 text-sm">
                                    <div class="w-2 h-2 bg-purple-500 rounded-full"></div>
                                    <span class="text-neutral-600 dark:text-neutral-400">Aman & Terenkripsi</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="tentang" class="bg-white dark:bg-neutral-900 border-y border-neutral-200 dark:border-neutral-800">
        <div class="max-w-[1400px] mx-auto px-6 lg:px-12 py-20 lg:py-28">
            <div class="grid lg:grid-cols-2 gap-16">
                <div>
                    <div class="text-xs font-medium text-neutral-500 dark:text-neutral-500 mb-4 tracking-wider uppercase">Tentang Sistem</div>
                    <h2 class="text-3xl lg:text-4xl font-light text-neutral-900 dark:text-neutral-100 mb-6">
                        Solusi Digital untuk<br />
                        <span class="font-semibold">Manajemen Review</span>
                    </h2>
                    <p class="text-neutral-600 dark:text-neutral-400 leading-relaxed mb-6">
                        ITEKES Journal adalah platform yang dirancang khusus untuk mempermudah proses review dan revisi penelitian serta pengabdian
                        masyarakat di lingkungan Institut Teknologi dan Kesehatan Bali.
                    </p>
                    <p class="text-neutral-600 dark:text-neutral-400 leading-relaxed">
                        Dengan workflow yang terstruktur dan interface yang intuitif, sistem ini memastikan setiap tahapan—dari pengajuan proposal
                        hingga laporan akhir—dapat dilacak dan dikelola dengan transparan dan efisien.
                    </p>
                </div>

                <div class="space-y-6">
                    <div class="border-l-2 border-blue-500 pl-6">
                        <h3 class="font-semibold text-neutral-900 dark:text-neutral-100 mb-2">Efisiensi Proses</h3>
                        <p class="text-sm text-neutral-600 dark:text-neutral-400">
                            Otomasi alur kerja mulai dari penugasan reviewer hingga tracking status revisi
                        </p>
                    </div>
                    <div class="border-l-2 border-indigo-500 pl-6">
                        <h3 class="font-semibold text-neutral-900 dark:text-neutral-100 mb-2">Transparansi Penuh</h3>
                        <p class="text-sm text-neutral-600 dark:text-neutral-400">Semua pihak dapat memantau progress usulan secara real-time</p>
                    </div>
                    <div class="border-l-2 border-purple-500 pl-6">
                        <h3 class="font-semibold text-neutral-900 dark:text-neutral-100 mb-2">Kolaborasi Mudah</h3>
                        <p class="text-sm text-neutral-600 dark:text-neutral-400">
                            Platform terintegrasi untuk komunikasi antara pengusul dan reviewer
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Grid -->
    <section id="fitur" class="bg-neutral-50 dark:bg-neutral-950">
        <div class="max-w-[1400px] mx-auto px-6 lg:px-12 py-20 lg:py-28">
            <div class="mb-16">
                <div class="text-xs font-medium text-neutral-500 dark:text-neutral-500 mb-4 tracking-wider uppercase">Fitur Unggulan</div>
                <h2 class="text-3xl lg:text-4xl font-light text-neutral-900 dark:text-neutral-100">
                    Kemampuan <span class="font-semibold">Platform</span>
                </h2>
            </div>

            <div
                class="grid md:grid-cols-2 lg:grid-cols-3 gap-px bg-neutral-200 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-800"
            >
                <!-- Feature 1 -->
                <div class="bg-white dark:bg-neutral-900 p-8 hover:bg-neutral-50 dark:hover:bg-neutral-800/50 transition-colors group">
                    <div class="text-blue-600 dark:text-blue-400 mb-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="1.5"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                            />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-neutral-900 dark:text-neutral-100 mb-2">Manajemen Usulan</h3>
                    <p class="text-sm text-neutral-600 dark:text-neutral-400 leading-relaxed">
                        Kelola proposal, laporan kemajuan, dan laporan akhir dalam satu platform terpadu
                    </p>
                </div>

                <!-- Feature 2 -->
                <div class="bg-white dark:bg-neutral-900 p-8 hover:bg-neutral-50 dark:hover:bg-neutral-800/50 transition-colors group">
                    <div class="text-indigo-600 dark:text-indigo-400 mb-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="1.5"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"
                            />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-neutral-900 dark:text-neutral-100 mb-2">Assignment Reviewer</h3>
                    <p class="text-sm text-neutral-600 dark:text-neutral-400 leading-relaxed">
                        Sistem otomatis untuk menugaskan reviewer berdasarkan bidang keahlian
                    </p>
                </div>

                <!-- Feature 3 -->
                <div class="bg-white dark:bg-neutral-900 p-8 hover:bg-neutral-50 dark:hover:bg-neutral-800/50 transition-colors group">
                    <div class="text-purple-600 dark:text-purple-400 mb-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="1.5"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"
                            />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-neutral-900 dark:text-neutral-100 mb-2">Workflow Review</h3>
                    <p class="text-sm text-neutral-600 dark:text-neutral-400 leading-relaxed">
                        Alur review terstruktur dengan status tracking dan deadline management
                    </p>
                </div>

                <!-- Feature 4 -->
                <div class="bg-white dark:bg-neutral-900 p-8 hover:bg-neutral-50 dark:hover:bg-neutral-800/50 transition-colors group">
                    <div class="text-green-600 dark:text-green-400 mb-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="1.5"
                                d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"
                            />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-neutral-900 dark:text-neutral-100 mb-2">Dokumen Digital</h3>
                    <p class="text-sm text-neutral-600 dark:text-neutral-400 leading-relaxed">
                        Upload dan kelola semua dokumen penelitian dengan sistem version control
                    </p>
                </div>

                <!-- Feature 5 -->
                <div class="bg-white dark:bg-neutral-900 p-8 hover:bg-neutral-50 dark:hover:bg-neutral-800/50 transition-colors group">
                    <div class="text-orange-600 dark:text-orange-400 mb-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="1.5"
                                d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"
                            />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-neutral-900 dark:text-neutral-100 mb-2">Komunikasi Terintegrasi</h3>
                    <p class="text-sm text-neutral-600 dark:text-neutral-400 leading-relaxed">
                        Sistem komentar dan feedback langsung antara reviewer dan pengusul
                    </p>
                </div>

                <!-- Feature 6 -->
                <div class="bg-white dark:bg-neutral-900 p-8 hover:bg-neutral-50 dark:hover:bg-neutral-800/50 transition-colors group">
                    <div class="text-pink-600 dark:text-pink-400 mb-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="1.5"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"
                            />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-neutral-900 dark:text-neutral-100 mb-2">Dashboard Analytics</h3>
                    <p class="text-sm text-neutral-600 dark:text-neutral-400 leading-relaxed">
                        Laporan lengkap dan visualisasi data untuk monitoring performa
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Workflow -->
    <section id="alur" class="bg-white dark:bg-neutral-900">
        <div class="max-w-[1400px] mx-auto px-6 lg:px-12 py-20 lg:py-28">
            <div class="mb-16">
                <div class="text-xs font-medium text-neutral-500 dark:text-neutral-500 mb-4 tracking-wider uppercase">Alur Kerja</div>
                <h2 class="text-3xl lg:text-4xl font-light text-neutral-900 dark:text-neutral-100 mb-4">
                    Proses yang <span class="font-semibold">Terstruktur</span>
                </h2>
                <p class="text-neutral-600 dark:text-neutral-400 max-w-2xl">Setiap usulan melewati tahapan review yang jelas dan terukur</p>
            </div>

            <div class="grid lg:grid-cols-4 gap-8">
                <div class="relative">
                    <div class="text-5xl font-light text-neutral-200 dark:text-neutral-800 mb-4">01</div>
                    <h3 class="text-xl font-semibold text-neutral-900 dark:text-neutral-100 mb-3">Pengajuan</h3>
                    <p class="text-sm text-neutral-600 dark:text-neutral-400 leading-relaxed">
                        Pengusul mengajukan proposal penelitian atau pengabdian masyarakat melalui sistem
                    </p>
                </div>

                <div class="relative">
                    <div class="text-5xl font-light text-neutral-200 dark:text-neutral-800 mb-4">02</div>
                    <h3 class="text-xl font-semibold text-neutral-900 dark:text-neutral-100 mb-3">Penugasan</h3>
                    <p class="text-sm text-neutral-600 dark:text-neutral-400 leading-relaxed">
                        Administrator menugaskan reviewer yang sesuai dengan bidang penelitian
                    </p>
                </div>

                <div class="relative">
                    <div class="text-5xl font-light text-neutral-200 dark:text-neutral-800 mb-4">03</div>
                    <h3 class="text-xl font-semibold text-neutral-900 dark:text-neutral-100 mb-3">Review</h3>
                    <p class="text-sm text-neutral-600 dark:text-neutral-400 leading-relaxed">
                        Reviewer memberikan penilaian, komentar, dan rekomendasi perbaikan
                    </p>
                </div>

                <div class="relative">
                    <div class="text-5xl font-light text-neutral-200 dark:text-neutral-800 mb-4">04</div>
                    <h3 class="text-xl font-semibold text-neutral-900 dark:text-neutral-100 mb-3">Revisi</h3>
                    <p class="text-sm text-neutral-600 dark:text-neutral-400 leading-relaxed">
                        Pengusul melakukan revisi dan submit kembali hingga disetujui
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="bg-neutral-900 dark:bg-neutral-950 text-white">
        <div class="max-w-[1400px] mx-auto px-6 lg:px-12 py-20 lg:py-28">
            <div class="max-w-3xl">
                <h2 class="text-3xl lg:text-4xl font-light mb-6">
                    Siap untuk memulai perjalanan<br />
                    <span class="font-semibold">penelitian Anda?</span>
                </h2>
                <p class="text-neutral-400 text-lg mb-8">
                    Bergabunglah dengan ratusan peneliti yang telah mempercayai platform kami untuk mengelola proses review mereka.
                </p>
                {#if !user}
                    <div class="flex flex-col sm:flex-row gap-3">
                        <Link
                            href={route('register')}
                            class="inline-flex items-center justify-center px-6 py-3 bg-white text-neutral-900 font-medium hover:bg-neutral-100 transition-all"
                        >
                            Daftar Sekarang
                        </Link>
                        <Link
                            href={route('login')}
                            class="inline-flex items-center justify-center px-6 py-3 border border-neutral-700 text-white font-medium hover:border-neutral-600 transition-colors"
                        >
                            Sudah Punya Akun?
                        </Link>
                    </div>
                {/if}
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-white dark:bg-neutral-900 border-t border-neutral-200 dark:border-neutral-800">
        <div class="max-w-[1400px] mx-auto px-6 lg:px-12 py-12">
            <div class="grid md:grid-cols-3 gap-12 mb-12">
                <div>
                    <div class="flex items-center gap-3 mb-4">
                        <img src="/assets/images/itekes-bali.webp" alt="ITEKES" class="h-10 w-10" />
                        <div>
                            <div class="font-semibold text-neutral-900 dark:text-neutral-100">ITEKES Bali</div>
                            <div class="text-xs text-neutral-500 dark:text-neutral-500">Journal Management</div>
                        </div>
                    </div>
                    <p class="text-sm text-neutral-600 dark:text-neutral-400">Institut Teknologi dan Kesehatan Bali</p>
                </div>

                <div>
                    <div class="text-xs font-medium text-neutral-900 dark:text-neutral-100 mb-4 uppercase tracking-wider">Navigasi</div>
                    <ul class="space-y-2 text-sm">
                        <li>
                            <a
                                href="#tentang"
                                class="text-neutral-600 dark:text-neutral-400 hover:text-neutral-900 dark:hover:text-neutral-100 transition-colors"
                                >Tentang</a
                            >
                        </li>
                        <li>
                            <a
                                href="#fitur"
                                class="text-neutral-600 dark:text-neutral-400 hover:text-neutral-900 dark:hover:text-neutral-100 transition-colors"
                                >Fitur</a
                            >
                        </li>
                        <li>
                            <a
                                href="#alur"
                                class="text-neutral-600 dark:text-neutral-400 hover:text-neutral-900 dark:hover:text-neutral-100 transition-colors"
                                >Alur Kerja</a
                            >
                        </li>
                    </ul>
                </div>

                <div>
                    <div class="text-xs font-medium text-neutral-900 dark:text-neutral-100 mb-4 uppercase tracking-wider">Bantuan</div>
                    <ul class="space-y-2 text-sm">
                        {#if !user}
                            <li>
                                <Link
                                    href={route('login')}
                                    class="text-neutral-600 dark:text-neutral-400 hover:text-neutral-900 dark:hover:text-neutral-100 transition-colors"
                                    >Masuk</Link
                                >
                            </li>
                            {#if canRegister}
                                <li>
                                    <Link
                                        href={route('register')}
                                        class="text-neutral-600 dark:text-neutral-400 hover:text-neutral-900 dark:hover:text-neutral-100 transition-colors"
                                        >Daftar</Link
                                    >
                                </li>
                            {/if}
                        {/if}
                    </ul>
                </div>
            </div>

            <div class="pt-8 border-t border-neutral-200 dark:border-neutral-800">
                <div class="flex flex-col md:flex-row justify-between items-center gap-4 text-sm text-neutral-500 dark:text-neutral-500">
                    <p>&copy; {new Date().getFullYear()} Institut Teknologi dan Kesehatan Bali. All rights reserved.</p>
                    <p class="text-xs">Sistem Manajemen Review Penelitian & Pengabdian Masyarakat</p>
                </div>
            </div>
        </div>
    </footer>
</div>

<style>
    :global(body) {
        font-family:
            'Inter',
            -apple-system,
            BlinkMacSystemFont,
            'Segoe UI',
            sans-serif;
        font-feature-settings: 'cv11', 'ss01';
    }
</style>
