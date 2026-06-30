<script lang="ts">
    import InputError from '@/components/input-error.svelte';
    import { Button } from '@/components/ui/button';
    import { Input } from '@/components/ui/input';
    import { InputOTP, InputOTPGroup, InputOTPSlot } from '@/components/ui/input-otp';
    import AuthLayout from '@/layouts/auth-layout.svelte';
    import type { BaseFormSnippetProps } from '@/types/forms';
    import { Form } from '@inertiajs/svelte';

    interface AuthConfigContent {
        title: string;
        description: string;
        toggleText: string;
    }

    let showRecoveryInput = $state<boolean>(false);
    let code = $state<string>('');

    const authConfigContent = $derived<AuthConfigContent>(
        showRecoveryInput
            ? {
                  title: 'Kode Pemulihan',
                  description: 'Harap konfirmasi akses ke akun Anda dengan memasukkan salah satu kode pemulihan darurat Anda.',
                  toggleText: 'masuk menggunakan kode autentikasi',
              }
            : {
                  title: 'Kode Autentikasi',
                  description: 'Masukkan kode autentikasi yang disediakan oleh aplikasi autentikator Anda.',
                  toggleText: 'masuk menggunakan kode pemulihan',
              },
    );

    const toggleRecoveryMode = (clearErrors: () => void): void => {
        showRecoveryInput = !showRecoveryInput;
        clearErrors();
        code = '';
    };
</script>

<svelte:head>
    <title>Autentikasi Dua Faktor</title>
</svelte:head>

<AuthLayout title={authConfigContent.title} description={authConfigContent.description}>
    <div class="space-y-6">
        {#if !showRecoveryInput}
            <Form method="post" action={route('two-factor.login')} class="space-y-4" resetOnError={true} onError={() => (code = '')}>
                {#snippet children({ errors, processing, clearErrors }: BaseFormSnippetProps & { clearErrors: () => void })}
                    <input type="hidden" name="code" value={code} />
                    <div class="flex flex-col items-center justify-center space-y-3 text-center">
                        <div class="flex w-full items-center justify-center">
                            <InputOTP bind:value={code} maxlength={6} disabled={processing} autofocus>
                                {#snippet children({ cells })}
                                    <InputOTPGroup>
                                        {#each cells as cell, index (index)}
                                            <InputOTPSlot {cell} />
                                        {/each}
                                    </InputOTPGroup>
                                {/snippet}
                            </InputOTP>
                        </div>
                        <InputError message={errors.code} />
                    </div>
                    <Button type="submit" class="w-full" disabled={processing}>Lanjutkan</Button>
                    <div class="text-center text-sm text-muted-foreground">
                        <span>atau Anda dapat </span>
                        <button
                            type="button"
                            class="text-foreground underline decoration-neutral-300 underline-offset-4 transition-colors duration-300 ease-out hover:decoration-current! dark:decoration-neutral-500"
                            onclick={() => toggleRecoveryMode(clearErrors)}
                        >
                            {authConfigContent.toggleText}
                        </button>
                    </div>
                {/snippet}
            </Form>
        {:else}
            <Form method="post" action={route('two-factor.login')} class="space-y-4" resetOnError={true}>
                {#snippet children({ errors, processing, clearErrors }: BaseFormSnippetProps & { clearErrors: () => void })}
                    <Input name="recovery_code" type="text" placeholder="Masukkan kode pemulihan" autofocus={showRecoveryInput} required />
                    <InputError message={errors.recovery_code} />
                    <Button type="submit" class="w-full" disabled={processing}>Lanjutkan</Button>

                    <div class="text-center text-sm text-muted-foreground">
                        <span>atau Anda dapat </span>
                        <button
                            type="button"
                            class="text-foreground underline decoration-neutral-300 underline-offset-4 transition-colors duration-300 ease-out hover:decoration-current! dark:decoration-neutral-500"
                            onclick={() => toggleRecoveryMode(clearErrors)}
                        >
                            {authConfigContent.toggleText}
                        </button>
                    </div>
                {/snippet}
            </Form>
        {/if}
    </div>
</AuthLayout>
