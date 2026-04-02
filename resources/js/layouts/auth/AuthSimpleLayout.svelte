<script lang="ts">
    import { Link } from '@inertiajs/svelte';
    import type { Snippet } from 'svelte';

    interface Props {
        title: string;
        description: string;
        children?: Snippet;
    }

    let { title, description, children }: Props = $props();
</script>

<svelte:head>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
</svelte:head>

<div class="min-h-screen bg-neutral-50 dark:bg-neutral-950 font-sans">
    <!-- Top Bar -->
    <div class="border-b border-neutral-200 dark:border-neutral-800 bg-white dark:bg-neutral-900">
        <div class="max-w-[1400px] mx-auto px-6 lg:px-12">
            <div class="h-14 flex items-center justify-between text-xs">
                <span class="text-neutral-600 dark:text-neutral-400">Institut Teknologi dan Kesehatan Bali</span>
                <Link
                    href={route('home')}
                    class="text-neutral-600 dark:text-neutral-400 hover:text-neutral-900 dark:hover:text-neutral-100 transition-colors"
                >
                    ← Kembali ke Beranda
                </Link>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="flex min-h-[calc(100vh-3.5rem)]">
        <!-- Left Column - Branding (Hidden on mobile) -->
        <div class="hidden lg:flex lg:w-1/2 xl:w-2/5 bg-neutral-900 dark:bg-neutral-950 relative overflow-hidden">
            <!-- Background Pattern -->
            <div class="absolute inset-0 opacity-5">
                <div
                    class="absolute inset-0 bg-[linear-gradient(to_right,#80808012_1px,transparent_1px),linear-gradient(to_bottom,#80808012_1px,transparent_1px)] bg-size-[32px_32px]"
                ></div>
            </div>

            <!-- Content -->
            <div class="relative flex flex-col justify-center px-12 xl:px-16 text-white">
                <div class="max-w-md">
                    <!-- Logo -->
                    <div class="mb-8">
                        <img src="/assets/images/itekes-bali.webp" alt="SIPANDU ITEKES BALI" class="h-20 w-20 mb-6" />
                        <div class="border-l-2 border-white/30 pl-4">
                            <h2 class="text-2xl font-semibold text-white mb-1">SIPANDU ITEKES BALI</h2>
                            <p class="text-sm text-neutral-400">Sistem Manajemen Review</p>
                        </div>
                    </div>

                    <!-- Tagline -->
                    <div class="space-y-4">
                        <p class="text-neutral-300 leading-relaxed">
                            Platform digital untuk mengelola proses review dan revisi penelitian serta pengabdian masyarakat dengan efisien dan
                            terstruktur.
                        </p>

                        <!-- Features -->
                        <div class="space-y-3 pt-4">
                            <div class="flex items-center gap-3 text-sm">
                                <div class="w-1.5 h-1.5 rounded-full bg-blue-400"></div>
                                <span class="text-neutral-300">Pelacakan Status Real-time</span>
                            </div>
                            <div class="flex items-center gap-3 text-sm">
                                <div class="w-1.5 h-1.5 rounded-full bg-green-400"></div>
                                <span class="text-neutral-300">Alur Kerja Terstruktur</span>
                            </div>
                            <div class="flex items-center gap-3 text-sm">
                                <div class="w-1.5 h-1.5 rounded-full bg-purple-400"></div>
                                <span class="text-neutral-300">Aman & Terenkripsi</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Decorative gradient -->
            <div class="absolute bottom-0 right-0 w-96 h-96 bg-linear-to-tr from-blue-600/10 to-purple-600/10 blur-3xl"></div>
        </div>

        <!-- Right Column - Form -->
        <div class="w-full lg:w-1/2 xl:w-3/5 flex items-center justify-center p-6 sm:p-12">
            <div class="w-full max-w-md">
                <!-- Mobile Logo (visible on mobile only) -->
                <div class="lg:hidden mb-8 text-center">
                    <Link href={route('home')} class="inline-flex flex-col items-center gap-2">
                        <img src="/assets/images/itekes-bali.webp" alt="ITEKES Bali" class="h-16 w-16" />
                        <div class="text-neutral-900 dark:text-neutral-100">
                            <div class="text-sm font-semibold">ITEKES Journal</div>
                            <div class="text-xs text-neutral-500">Sistem Revisi</div>
                        </div>
                    </Link>
                </div>

                <!-- Form Header -->
                <div class="mb-8">
                    <h1 class="text-3xl font-light text-neutral-900 dark:text-neutral-100 mb-2">
                        {title}
                    </h1>
                    <p class="text-sm text-neutral-600 dark:text-neutral-400">
                        {description}
                    </p>
                </div>

                <!-- Form Content -->
                <div>
                    {@render children?.()}
                </div>

                <!-- Footer -->
                <div class="mt-8 pt-6 border-t border-neutral-200 dark:border-neutral-800">
                    <p class="text-xs text-center text-neutral-500 dark:text-neutral-500">
                        &copy; {new Date().getFullYear()} Institut Teknologi dan Kesehatan Bali
                    </p>
                </div>
            </div>
        </div>
    </div>
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
