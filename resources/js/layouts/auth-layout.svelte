<script lang="ts">
    import AuthLayout from '@/layouts/auth/auth-simple-layout.svelte';
    import type { Snippet } from 'svelte';

    interface Props {
        title: string;
        description: string;
        children?: Snippet;
    }

    let { title, description, children }: Props = $props();
</script>

<AuthLayout {title} {description}>
    {@render children?.()}
</AuthLayout>
