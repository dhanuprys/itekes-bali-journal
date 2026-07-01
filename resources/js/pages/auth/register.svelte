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
    import { Check, Info } from 'lucide-svelte';

    let password = $state('');

    // Password strength logic
    let passwordStrength = $derived.by(() => {
        if (!password) return { score: 0, text: 'Sangat Lemah', color: 'bg-neutral-200 dark:bg-neutral-800' };

        let score = 0;
        
        // Length checks
        if (password.length >= 8) score += 20;
        if (password.length >= 12) score += 10;
        
        // Character variety checks
        if (/[A-Z]/.test(password)) score += 20;
        if (/[a-z]/.test(password)) score += 20;
        if (/[0-9]/.test(password)) score += 15;
        if (/[^A-Za-z0-9]/.test(password)) score += 15;

        score = Math.min(100, score);

        let text = 'Sangat Lemah';
        let color = 'bg-neutral-200 dark:bg-neutral-800';

        if (score >= 90) {
            text = 'Sangat Kuat';
            color = 'bg-emerald-500';
        } else if (score >= 70) {
            text = 'Kuat';
            color = 'bg-emerald-400';
        } else if (score >= 50) {
            text = 'Sedang';
            color = 'bg-yellow-500';
        } else {
            text = 'Lemah';
            color = 'bg-destructive/80';
        }

        return { score, text, color };
    });

    let requirements = $derived([
        { text: 'Minimal 8 karakter', met: password.length >= 8 },
        { text: 'Satu huruf besar', met: /[A-Z]/.test(password) },
        { text: 'Satu angka atau simbol', met: /[0-9]|[^A-Za-z0-9]/.test(password) }
    ]);
</script>

<svelte:head>
    <title>Daftar Akun Baru</title>
</svelte:head>

<AuthBase title="Buat Akun Baru" description="Masukkan detail Anda untuk membuat akun di platform kami">
    <Form method="post" action={route('register')} resetOnSuccess={['password', 'password_confirmation']} class="flex flex-col gap-6">
        {#snippet children({ errors, processing }: BaseFormSnippetProps)}
            <div class="grid gap-5">
                <div class="grid gap-2">
                    <Label for="name">Nama Lengkap</Label>
                    <Input id="name" name="name" type="text" required autofocus tabindex={1} autocomplete="name" placeholder="Nama lengkap Anda" class="transition-all focus:ring-2" />
                    <InputError message={errors.name} />
                </div>

                <div class="grid gap-2">
                    <Label for="username">Username</Label>
                    <Input id="username" name="username" type="text" required tabindex={2} autocomplete="username" placeholder="john_doe123" class="transition-all focus:ring-2" />
                    <InputError message={errors.username} />
                    <p class="flex items-center gap-1.5 text-xs text-neutral-500">
                        <Info class="h-3 w-3" />
                        <span>Hanya huruf kecil, angka, dan garis bawah.</span>
                    </p>
                </div>

                <div class="grid gap-2">
                    <Label for="email">Alamat Email</Label>
                    <Input id="email" name="email" type="email" required tabindex={3} autocomplete="email" placeholder="email@contoh.com" class="transition-all focus:ring-2" />
                    <InputError message={errors.email} />
                </div>

                <div class="grid gap-2">
                    <Label for="password">Kata Sandi</Label>
                    <Input 
                        id="password" 
                        name="password" 
                        type="password" 
                        required 
                        tabindex={4} 
                        autocomplete="new-password" 
                        placeholder="Masukkan kata sandi yang kuat" 
                        bind:value={password}
                        class="transition-all focus:ring-2"
                    />
                    
                    <!-- Password Strength Bar -->
                    <div class="mt-1.5 flex flex-col gap-2">
                        <div class="flex h-1.5 w-full overflow-hidden rounded-full bg-neutral-200 dark:bg-neutral-800">
                            <div 
                                class="h-full transition-all duration-500 ease-out {passwordStrength.color}" 
                                style="width: {passwordStrength.score}%"
                            ></div>
                        </div>
                        <div class="flex items-center justify-between text-xs">
                            <span class="font-medium text-muted-foreground">Kekuatan Sandi:</span>
                            <span class="font-semibold transition-colors {passwordStrength.score >= 50 ? 'text-foreground' : 'text-muted-foreground'}">
                                {passwordStrength.text}
                            </span>
                        </div>
                        
                        {#if password.length > 0}
                            <div class="grid grid-cols-1 gap-1.5 pt-1 sm:grid-cols-2">
                                {#each requirements as req}
                                    <div class="flex items-center gap-1.5 text-xs {req.met ? 'text-emerald-500' : 'text-muted-foreground'} transition-colors duration-300">
                                        <div class="flex h-3 w-3 items-center justify-center rounded-full {req.met ? 'bg-emerald-500/20' : 'bg-neutral-200 dark:bg-neutral-800'}">
                                            {#if req.met}
                                                <Check class="h-2 w-2 stroke-[3]" />
                                            {/if}
                                        </div>
                                        <span>{req.text}</span>
                                    </div>
                                {/each}
                            </div>
                        {/if}
                    </div>
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
                        placeholder="Ulangi kata sandi Anda"
                        class="transition-all focus:ring-2"
                    />
                    <InputError message={errors.password_confirmation} />
                </div>

                <Button type="submit" class="mt-2 w-full transition-all hover:scale-[1.02] active:scale-100" tabindex={6} disabled={processing}>
                    {#if processing}
                        <Spinner class="mr-2" />
                    {/if}
                    Buat Akun Sekarang
                </Button>
            </div>

            <div class="text-center text-sm text-muted-foreground mt-2">
                Sudah punya akun?
                <TextLink href={route('login')} class="font-medium text-primary hover:underline hover:underline-offset-4 transition-all" tabindex={7}>
                    Masuk di sini
                </TextLink>
            </div>
        {/snippet}
    </Form>
</AuthBase>
