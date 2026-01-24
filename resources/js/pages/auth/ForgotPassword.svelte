<script lang="ts">
    import InputError from '@/components/InputError.svelte';
    import TextLink from '@/components/TextLink.svelte';
    import { Button } from '@/components/ui/button';
    import { Input } from '@/components/ui/input';
    import { Label } from '@/components/ui/label';
    import AuthLayout from '@/layouts/AuthLayout.svelte';
    import type { BaseFormSnippetProps } from '@/types/forms';
    import { Form } from '@inertiajs/svelte';
    import { LoaderCircle } from 'lucide-svelte';

    interface Props {
        status?: string;
    }

    let { status }: Props = $props();
</script>

<svelte:head>
    <title>Lupa Kata Sandi</title>
</svelte:head>

<AuthLayout title="Lupa Kata Sandi" description="Masukkan email Anda untuk menerima tautan reset kata sandi">
    {#if status}
        <div class="mb-4 text-center text-sm font-medium text-green-600">
            {status}
        </div>
    {/if}

    <div class="space-y-6">
        <Form method="post" action={route('password.email')}>
            {#snippet children({ errors, processing }: BaseFormSnippetProps)}
                <div class="grid gap-2">
                    <Label for="email">Alamat Email</Label>
                    <Input id="email" type="email" name="email" autocomplete="off" autofocus placeholder="email@contoh.com" />
                    <InputError message={errors.email} />
                </div>

                <div class="my-6 flex items-center justify-start">
                    <Button type="submit" class="w-full" disabled={processing}>
                        {#if processing}
                            <LoaderCircle class="h-4 w-4 animate-spin" />
                        {/if}
                        Kirim Tautan Reset Kata Sandi
                    </Button>
                </div>
            {/snippet}
        </Form>

        <div class="space-x-1 text-center text-sm text-muted-foreground">
            <span>Atau, kembali ke</span>
            <TextLink href={route('login')}>halaman masuk</TextLink>
        </div>
    </div>
</AuthLayout>
