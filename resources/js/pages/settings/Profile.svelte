<script lang="ts">
    import FileUpload from '@/components/FileUpload.svelte';
    import HeadingSmall from '@/components/HeadingSmall.svelte';
    import InputError from '@/components/InputError.svelte';
    import { StorageUploadAction } from '@/data/storage-upload';
    import { Button } from '@/components/ui/button';
    import { Input } from '@/components/ui/input';
    import { Label } from '@/components/ui/label';
    import AppLayout from '@/layouts/AppLayout.svelte';
    import SettingsLayout from '@/layouts/settings/Layout.svelte';
    import { type BreadcrumbItem, type User } from '@/types';
    import type { ProfileFormSnippetProps } from '@/types/forms';
    import { Form, Link, page } from '@inertiajs/svelte';
    import { Separator } from '@/components/ui/separator';
    import { Badge } from '@/components/ui/badge';
    import { fade } from 'svelte/transition';

    interface Props {
        mustVerifyEmail: boolean;
        status?: string;
    }

    let { mustVerifyEmail, status }: Props = $props();

    const breadcrumbItems: BreadcrumbItem[] = [
        {
            title: 'Profile settings',
            href: '/settings/profile',
        },
    ];

    const user = $page.props.auth.user as User;

    let photoPath = $state(user.photo_path);
</script>

<svelte:head>
    <title>Profil</title>
</svelte:head>

<AppLayout breadcrumbs={breadcrumbItems}>
    <SettingsLayout>
        <div class="flex flex-col space-y-6">
            <HeadingSmall title="Informasi Profil" description="Perbarui nama dan alamat email Anda" />

            <Form method="patch" action={route('profile.update')} class="space-y-6">
                {#snippet children({ errors, processing, recentlySuccessful }: ProfileFormSnippetProps)}
                    <div class="grid gap-2">
                        <Label for="name">Nama</Label>
                        <Input name="name" class="mt-1 block w-full" defaultValue={user.name} required autocomplete="name" placeholder="Full name" />
                        <InputError class="mt-2" message={errors.name} />
                    </div>

                    <div class="grid gap-2">
                        <Label for="username">Username</Label>
                        <Input
                            name="username"
                            class="mt-1 block w-full"
                            defaultValue={user.username}
                            required
                            autocomplete="username"
                            placeholder="Username"
                        />
                        <InputError class="mt-2" message={errors.username} />
                    </div>

                    <div class="grid gap-2">
                        <FileUpload
                            action={StorageUploadAction.USER_PROFILE_PHOTO}
                            bind:value={photoPath}
                            label="Photo Profile"
                            accept=".jpg,.jpeg,.png,.webp"
                            maxSize={3 * 1024 * 1024}
                            error={errors.photo_path}
                        />
                        <input type="hidden" name="photo_path" value={photoPath} />
                    </div>

                    <div class="grid gap-2">
                        <Label for="email">Email</Label>
                        <Input
                            id="email"
                            name="email"
                            class="mt-1 block w-full"
                            defaultValue={user.email}
                            required
                            autocomplete="username"
                            placeholder="Email address"
                        />
                        <InputError class="mt-2" message={errors.email} />
                    </div>

                    {#if mustVerifyEmail && !user.email_verified_at}
                        <div>
                            <p class="-mt-4 text-sm text-muted-foreground">
                                Alamat email Anda belum terverifikasi.
                                <Link
                                    href={route('verification.send')}
                                    method="post"
                                    as="button"
                                    class="text-foreground underline decoration-neutral-300 underline-offset-4 transition-colors duration-300 ease-out hover:decoration-current! dark:decoration-neutral-500"
                                >
                                    Klik di sini untuk mengirim ulang email verifikasi.
                                </Link>
                            </p>

                            {#if status === 'verification-link-sent'}
                                <div class="mt-2 text-sm font-medium text-green-600">Link verifikasi baru telah dikirim ke alamat email Anda.</div>
                            {/if}
                        </div>
                    {/if}

                    <div class="flex items-center gap-4">
                        <Button type="submit" disabled={processing}>Simpan</Button>

                        {#if recentlySuccessful}
                            <p class="text-sm text-neutral-600" transition:fade={{ duration: 150 }}>Saved.</p>
                        {/if}
                    </div>
                {/snippet}
            </Form>

            <Separator />

            <div class="space-y-6">
                <HeadingSmall title="Role & Izin" description="Informasi hak akses akun Anda" />

                <div class="space-y-4">
                    <div>
                        <div class="text-sm font-medium mb-2">Role</div>
                        <div class="flex flex-wrap gap-2">
                            {#each user.roles as role (role)}
                                <Badge>{role}</Badge>
                            {/each}
                        </div>
                    </div>

                    {#if user.permissions && user.permissions.length > 0}
                        <div>
                            <div class="text-sm font-medium mb-2">Izin Khusus</div>
                            <div class="flex flex-wrap gap-2">
                                {#each user.permissions as permission (permission)}
                                    <Badge variant="outline">{permission}</Badge>
                                {/each}
                            </div>
                        </div>
                    {/if}
                </div>
            </div>
        </div>
    </SettingsLayout>
</AppLayout>
