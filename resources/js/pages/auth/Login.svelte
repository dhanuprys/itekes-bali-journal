<script lang="ts">
    import InputError from '@/components/InputError.svelte';
    import TextLink from '@/components/TextLink.svelte';
    import { Button } from '@/components/ui/button';
    import { Checkbox } from '@/components/ui/checkbox';
    import { Input } from '@/components/ui/input';
    import { Label } from '@/components/ui/label';
    import { Spinner } from '@/components/ui/spinner';
    import AuthBase from '@/layouts/AuthLayout.svelte';
    import type { BaseFormSnippetProps } from '@/types/forms';
    import { Form } from '@inertiajs/svelte';

    interface Props {
        status?: string;
        canResetPassword: boolean;
        canRegister: boolean;
    }

    let { status, canResetPassword, canRegister }: Props = $props();
</script>

<svelte:head>
    <title>Masuk</title>
</svelte:head>

<AuthBase title="Masuk ke Akun Anda" description="Masukkan email dan kata sandi Anda untuk masuk">
    {#if status}
        <div class="mb-4 text-center text-sm font-medium text-green-600">
            {status}
        </div>
    {/if}

    <Form method="post" action={route('login')} resetOnSuccess={['password']} class="flex flex-col gap-6">
        {#snippet children({ errors, processing }: BaseFormSnippetProps)}
            <div class="grid gap-6">
                <div class="grid gap-2">
                    <Label for="email">Alamat Email</Label>
                    <Input id="email" name="email" type="email" required autofocus tabindex={1} autocomplete="email" placeholder="email@contoh.com" />
                    <InputError message={errors.email} />
                </div>

                <div class="grid gap-2">
                    <div class="flex items-center justify-between">
                        <Label for="password">Kata Sandi</Label>
                        {#if canResetPassword}
                            <TextLink href={route('password.request')} class="text-sm" tabindex={5}>Lupa kata sandi?</TextLink>
                        {/if}
                    </div>
                    <Input
                        id="password"
                        name="password"
                        type="password"
                        required
                        tabindex={2}
                        autocomplete="current-password"
                        placeholder="Kata Sandi"
                    />
                    <InputError message={errors.password} />
                </div>

                <div class="flex items-center justify-between">
                    <Label for="remember" class="flex items-center space-x-3">
                        <Checkbox id="remember" name="remember" tabindex={3} />
                        <span>Ingat saya</span>
                    </Label>
                </div>

                <Button type="submit" class="mt-4 w-full" tabindex={4} disabled={processing}>
                    {#if processing}
                        <Spinner />
                    {/if}
                    Masuk
                </Button>
            </div>

            {#if canRegister}
                <div class="text-center text-sm text-muted-foreground">
                    Belum punya akun?
                    <TextLink href={route('register')} tabindex={5}>Daftar</TextLink>
                </div>
            {/if}
        {/snippet}
    </Form>
</AuthBase>
