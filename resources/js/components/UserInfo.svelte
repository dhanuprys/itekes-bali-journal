<script lang="ts">
    import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
    import { useInitials } from '@/hooks/useInitials';
    import type { User } from '@/types';

    interface Props {
        user: User;
    }

    let { user }: Props = $props();

    const { getInitials } = useInitials();

    // Prioritize photo_path, fallback to avatar
    let avatarSrc = $derived(user.photo_path ? `/storage/${user.photo_path}` : user.avatar);
    let showAvatar = $derived(!!avatarSrc);
</script>

<Avatar class="h-8 w-8 overflow-hidden rounded-full">
    {#if showAvatar}
        <AvatarImage src={avatarSrc} alt={user.name} />
    {:else}
        <AvatarFallback class="rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white">
            {getInitials(user.name)}
        </AvatarFallback>
    {/if}
</Avatar>

<div class="grid flex-1 text-left text-sm leading-tight">
    <span class="truncate font-medium">{user.name}</span>
    <span class="truncate text-xs text-muted-foreground">#{user.username}</span>
</div>
