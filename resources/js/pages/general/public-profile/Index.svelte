<script lang="ts">
    import { Link, page } from '@inertiajs/svelte';
    import { Calendar, User } from 'lucide-svelte';

    interface Profile {
        name: string;
        username: string;
        photo_path: string | null;
        avatar: string | null;
        created_at: string;
    }

    interface Props {
        profile: Profile;
    }

    let { profile }: Props = $props();
    let authUser = $derived($page.props.auth.user);

    let avatarSrc = $derived(profile.photo_path ? `/storage/${profile.photo_path}` : profile.avatar);

    function formatDate(dateString: string) {
        return new Date(dateString).toLocaleDateString('id-ID', {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
        });
    }
</script>

<svelte:head>
    <title>{profile.name} (@{profile.username}) - ITEKES Bali Journal</title>
</svelte:head>

<div class="bg-neutral-50 dark:bg-neutral-950 min-h-screen font-sans flex flex-col">
    <!-- Navbar -->
    <nav class="sticky top-0 z-40 bg-white/80 dark:bg-neutral-900/80 backdrop-blur-xl border-b border-neutral-200/50 dark:border-neutral-800/50">
        <div class="max-w-[1000px] mx-auto px-6">
            <div class="h-16 flex items-center justify-between">
                <Link href="/" class="flex items-center gap-3 hover:opacity-80 transition-opacity">
                    <img src="/assets/images/itekes-bali.webp" alt="ITEKES" class="h-8 w-8" />
                    <div class="border-l border-neutral-300 dark:border-neutral-700 pl-3 h-6 flex flex-col justify-center">
                        <div class="text-sm font-semibold text-neutral-900 dark:text-neutral-100 leading-tight">ITEKES Journal</div>
                    </div>
                </Link>

                <div class="flex items-center gap-4">
                    {#if authUser}
                        <Link
                            href={route('dashboard')}
                            class="text-sm font-medium text-neutral-900 dark:text-neutral-100 hover:text-blue-600 dark:hover:text-blue-400 transition-colors"
                        >
                            Dashboard
                        </Link>
                    {:else}
                        <Link
                            href={route('login')}
                            class="text-sm font-medium text-neutral-600 dark:text-neutral-400 hover:text-neutral-900 dark:hover:text-neutral-100 transition-colors"
                        >
                            Masuk
                        </Link>
                    {/if}
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="flex-1 flex items-center justify-center p-6">
        <div class="w-full max-w-md">
            <div
                class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-neutral-200 dark:border-neutral-800 overflow-hidden relative"
            >
                <!-- Header Background -->
                <div class="h-32 bg-linear-to-r from-blue-500 to-indigo-600 relative">
                    <div class="absolute inset-0 bg-white/10 pattern-dots"></div>
                </div>

                <!-- Profile Info -->
                <div class="px-8 pb-8">
                    <div class="relative -mt-16 mb-4">
                        <div
                            class="w-32 h-32 rounded-full border-4 border-white dark:border-neutral-900 overflow-hidden bg-neutral-100 dark:bg-neutral-800 shadow-md"
                        >
                            {#if avatarSrc}
                                <img src={avatarSrc} alt={profile.name} class="w-full h-full object-cover" />
                            {:else}
                                <div class="w-full h-full flex items-center justify-center text-neutral-400">
                                    <User size={48} />
                                </div>
                            {/if}
                        </div>
                    </div>

                    <div class="space-y-1">
                        <h1 class="text-2xl font-bold text-neutral-900 dark:text-neutral-100">{profile.name}</h1>
                        <p class="text-neutral-500 dark:text-neutral-400 font-medium">@{profile.username}</p>
                    </div>

                    <div class="mt-6 flex items-center gap-2 text-sm text-neutral-600 dark:text-neutral-400">
                        <Calendar size={16} />
                        <span>Bergabung sejak {formatDate(profile.created_at)}</span>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-white dark:bg-neutral-900 border-t border-neutral-200 dark:border-neutral-800 py-8">
        <div class="max-w-[1000px] mx-auto px-6 text-center text-sm text-neutral-500 dark:text-neutral-500">
            <p>&copy; {new Date().getFullYear()} Institut Teknologi dan Kesehatan Bali.</p>
        </div>
    </footer>
</div>
