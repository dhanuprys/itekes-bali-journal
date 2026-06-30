<script lang="ts">
    import InputError from '@/components/input-error.svelte';
    import { Button } from '@/components/ui/button';
    import { Input } from '@/components/ui/input';
    import { Label } from '@/components/ui/label';
    import AuthLayout from '@/layouts/auth-layout.svelte';
    import type { BaseFormSnippetProps } from '@/types/forms';
    import { Form } from '@inertiajs/svelte';
    import { LoaderCircle } from 'lucide-svelte';
</script>

<svelte:head>
    <title>Konfirmasi Kata Sandi</title>
</svelte:head>

<AuthLayout title="Konfirmasi Kata Sandi" description="Ini adalah area aman dari aplikasi. Harap konfirmasi kata sandi Anda sebelum melanjutkan.">
    <Form method="post" action={route('password.confirm')} resetOnSuccess>
        {#snippet children({ errors, processing }: BaseFormSnippetProps)}
            <div class="space-y-6">
                <div class="grid gap-2">
                    <Label for="password">Kata Sandi</Label>
                    <Input
                        id="password"
                        name="password"
                        type="password"
                        class="mt-1 block w-full"
                        required
                        autocomplete="current-password"
                        autofocus
                    />

                    <InputError message={errors.password} />
                </div>

                <div class="flex items-center">
                    <Button type="submit" class="w-full" disabled={processing}>
                        {#if processing}
                            <LoaderCircle class="h-4 w-4 animate-spin" />
                        {/if}
                        Konfirmasi Kata Sandi
                    </Button>
                </div>
            </div>
        {/snippet}
    </Form>
</AuthLayout>
