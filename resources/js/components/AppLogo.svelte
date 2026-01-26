<script lang="ts">
    import AppLogoIcon from '@/components/AppLogoIcon.svelte';
    import { page } from '@inertiajs/svelte';
</script>

<div class="inline-flex items-center gap-3">
    <div class="flex aspect-square size-8 shrink-0 items-center justify-center rounded-md">
        <AppLogoIcon class="size-8" />
    </div>
    <div class="flex flex-col">
        <span class="truncate text-sm font-semibold">ITEKES Journal</span>
        <span class="text-[0.7rem] text-muted-foreground">{$page.props.version}</span>
    </div>
</div>
