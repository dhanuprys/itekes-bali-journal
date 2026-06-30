<script lang="ts">
    import TextLink from '@/components/text-link.svelte';
    import { Button } from '@/components/ui/button';
    import AuthLayout from '@/layouts/auth-layout.svelte';
    import { Form } from '@inertiajs/svelte';
    import { LoaderCircle } from 'lucide-svelte';

    interface Props {
        status?: string;
    }

    let { status }: Props = $props();
</script>

<svelte:head>
    <title>Verifikasi Email</title>
</svelte:head>

<AuthLayout
    title="Verifikasi Email"
    description="Silakan verifikasi alamat email Anda dengan mengklik tautan yang baru saja kami kirimkan kepada Anda."
>
    {#if status === 'verification-link-sent'}
        <div class="mb-4 text-center text-sm font-medium text-green-600">
            Tautan verifikasi baru telah dikirim ke alamat email yang Anda berikan saat pendaftaran.
        </div>
    {/if}

    <Form method="post" action={route('verification.send')} className="space-y-6 text-center">
        {#snippet children({ processing }: { processing: boolean })}
            <Button type="submit" disabled={processing} variant="secondary">
                {#if processing}
                    <LoaderCircle class="h-4 w-4 animate-spin" />
                {/if}
                Kirim Ulang Email Verifikasi
            </Button>

            <TextLink href={route('logout')} method="post" as="button" class="mx-auto block text-sm">Keluar</TextLink>
        {/snippet}
    </Form>
</AuthLayout>
