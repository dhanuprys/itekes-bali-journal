<script lang="ts">
    import InputError from '@/components/input-error.svelte';
    import TextLink from '@/components/text-link.svelte';
    import { Button } from '@/components/ui/button';
    import { Input } from '@/components/ui/input';
    import { Label } from '@/components/ui/label';
    import { Spinner } from '@/components/ui/spinner';
    import AuthBase from '@/layouts/auth-layout.svelte';
    import type { BaseFormSnippetProps } from '@/types/forms';
    import { Form } from '@inertiajs/svelte';
</script>

<svelte:head>
    <title>Daftar</title>
</svelte:head>

<AuthBase title="Buat Akun Baru" description="Masukkan detail Anda untuk membuat akun">
    <Form method="post" action={route('register')} resetOnSuccess={['password', 'password_confirmation']} class="flex flex-col gap-6">
        {#snippet children({ errors, processing }: BaseFormSnippetProps)}
            <div class="grid gap-6">
                <div class="grid gap-2">
                    <Label for="name">Nama Lengkap</Label>
                    <Input id="name" name="name" type="text" required autofocus tabindex={1} autocomplete="name" placeholder="Nama lengkap Anda" />
                    <InputError message={errors.name} />
                </div>

                <div class="grid gap-2">
                    <Label for="username">Username</Label>
                    <Input id="username" name="username" type="text" required tabindex={2} autocomplete="username" placeholder="username" />
                    <InputError message={errors.username} />
                    <p class="text-xs text-neutral-500">Hanya huruf kecil, angka, dan garis bawah. Contoh: john_doe123</p>
                </div>

                <div class="grid gap-2">
                    <Label for="email">Alamat Email</Label>
                    <Input id="email" name="email" type="email" required tabindex={3} autocomplete="email" placeholder="email@contoh.com" />
                    <InputError message={errors.email} />
                </div>

                <div class="grid gap-2">
                    <Label for="password">Kata Sandi</Label>
                    <Input id="password" name="password" type="password" required tabindex={4} autocomplete="new-password" placeholder="Kata Sandi" />
                    <InputError message={errors.password} />
                </div>

                <div class="grid gap-2">
                    <Label for="password_confirmation">Konfirmasi Kata Sandi</Label>
                    <Input
                        id="password_confirmation"
                        name="password_confirmation"
                        type="password"
                        required
                        tabindex={5}
                        autocomplete="new-password"
                        placeholder="Konfirmasi kata sandi"
                    />
                    <InputError message={errors.password_confirmation} />
                </div>

                <Button type="submit" class="mt-2 w-full" tabindex={6} disabled={processing}>
                    {#if processing}
                        <Spinner />
                    {/if}
                    Buat Akun
                </Button>
            </div>

            <div class="text-center text-sm text-muted-foreground">
                Sudah punya akun?
                <TextLink href={route('login')} class="underline underline-offset-4" tabindex={7}>Masuk</TextLink>
            </div>
        {/snippet}
    </Form>
</AuthBase>
