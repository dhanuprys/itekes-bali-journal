<script lang="ts">
    import InputError from '@/components/input-error.svelte';
    import TextLink from '@/components/text-link.svelte';
    import { Button } from '@/components/ui/button';
    import { Checkbox } from '@/components/ui/checkbox';
    import { Input } from '@/components/ui/input';
    import { Label } from '@/components/ui/label';
    import { Spinner } from '@/components/ui/spinner';
    import AuthBase from '@/layouts/auth-layout.svelte';
    import type { BaseFormSnippetProps } from '@/types/forms';
    import { Form } from '@inertiajs/svelte';

    interface Props {
        status?: string;
        canResetPassword: boolean;
        canRegister: boolean;
        loginNonce: string;
        loginPuzzle: string;
    }

    let { status, canResetPassword, canRegister, loginNonce, loginPuzzle }: Props = $props();

    let rawPassword = $state('');

    function solvePuzzle(pwd: string, puzzleBase64: string): string {
        if (!pwd || !puzzleBase64) return pwd;
        try {
            const puzzle = JSON.parse(atob(puzzleBase64));
            let chars = Array.from(pwd);
            
            if (puzzle.action === 'reverse') chars = chars.reverse();
            
            let processed = '';
            for (let i = 0; i < chars.length; i++) {
                let code = chars[i].codePointAt(0) || 0;
                if (puzzle.action === 'shift') code = (code + puzzle.key);
                if (puzzle.action === 'xor') code = (code ^ puzzle.key);
                
                processed += code.toString(16).padStart(6, '0');
            }
            return processed;
        } catch (e) {
            return pwd;
        }
    }

    let obfuscatedPassword = $derived(solvePuzzle(rawPassword, loginPuzzle));
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

    <Form method="post" action={route('login')} class="flex flex-col gap-6">
        {#snippet children({ errors, processing }: BaseFormSnippetProps)}
            <div class="grid gap-6">
                <input type="hidden" name="login_nonce" value={loginNonce} />
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
                        id="raw_password"
                        type="password"
                        required
                        tabindex={2}
                        autocomplete="current-password"
                        placeholder="Kata Sandi"
                        bind:value={rawPassword}
                    />
                    <input type="hidden" name="password" value={obfuscatedPassword} />
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
