<script lang="ts">
    import {
        SidebarGroup,
        SidebarGroupLabel,
        SidebarMenu,
        SidebarMenuBadge,
        SidebarMenuButton,
        SidebarMenuItem,
        SidebarMenuSub,
        SidebarMenuSubButton,
        SidebarMenuSubItem,
        useSidebar,
    } from '@/components/ui/sidebar';
    import { Collapsible, CollapsibleContent, CollapsibleTrigger } from '@/components/ui/collapsible';
    import type { NavGroup } from '@/types';
    import { Link, page, router } from '@inertiajs/svelte';
    import { ChevronRightIcon } from 'lucide-svelte';

    interface Props {
        group: NavGroup;
    }

    let { group }: Props = $props();
    const { setOpen } = useSidebar();
    const filteredItems = $derived(group.items.filter((item) => item !== null));
</script>

<SidebarGroup class="px-2 py-0">
    {#if group.label && filteredItems.length > 0}
        <SidebarGroupLabel>{group.label}</SidebarGroupLabel>
    {/if}
    <SidebarMenu>
        {#each filteredItems as item (item.title)}
            {#if item.items}
                <Collapsible open={item.isActive} class="group/collapsible">
                    {#snippet child({ props })}
                        <SidebarMenuItem {...props}>
                            <CollapsibleTrigger>
                                {#snippet child({ props })}
                                    <button
                                        type="button"
                                        class="w-full"
                                        onclick={() => {
                                            if (item.href !== '#') {
                                                router.visit(item.href);
                                            }
                                            setOpen(true);
                                        }}
                                    >
                                        <SidebarMenuButton {...props} tooltipContent={item.title}>
                                            {#if item.icon}
                                                <item.icon />
                                            {/if}
                                            <span>{item.title}</span>
                                            <ChevronRightIcon
                                                class="ms-auto transition-transform duration-200 group-data-[state=open]/collapsible:rotate-90"
                                            />
                                            {#if item.badge}
                                                <SidebarMenuBadge class="-translate-x-[1.2rem]">{item.badge}</SidebarMenuBadge>
                                            {/if}
                                        </SidebarMenuButton>
                                    </button>
                                {/snippet}
                            </CollapsibleTrigger>
                            <CollapsibleContent>
                                <SidebarMenuSub>
                                    {#each item.items ?? [] as subItem (subItem.title)}
                                        <SidebarMenuSubItem>
                                            <SidebarMenuSubButton>
                                                {#snippet child({ props })}
                                                    <Link class="flex items-center justify-between" href={subItem.href} {...props}>
                                                        <span>{subItem.title}</span>
                                                        {#if subItem.badge}
                                                            <SidebarMenuBadge>{subItem.badge}</SidebarMenuBadge>
                                                        {/if}
                                                    </Link>
                                                {/snippet}
                                            </SidebarMenuSubButton>
                                        </SidebarMenuSubItem>
                                    {/each}
                                </SidebarMenuSub>
                            </CollapsibleContent>
                        </SidebarMenuItem>
                    {/snippet}
                </Collapsible>
            {:else}
                <SidebarMenuItem>
                    <Link href={item.href} class="block w-full">
                        <SidebarMenuButton isActive={item.href === $page.url}>
                            {#snippet tooltipContent()}
                                {item.title}
                            {/snippet}
                            {#if item.icon}
                                {@const Icon = item.icon}
                                <Icon class="h-4 w-4 shrink-0" />
                            {/if}
                            <span>{item.title}</span>
                            {#if item.badge}
                                <SidebarMenuBadge>{item.badge}</SidebarMenuBadge>
                            {/if}
                        </SidebarMenuButton>
                    </Link>
                </SidebarMenuItem>
            {/if}
        {/each}
    </SidebarMenu>
</SidebarGroup>

<!-- <SidebarGroup class="px-2 py-0">
    {#if group.label}
        <SidebarGroupLabel>{group.label}</SidebarGroupLabel>
    {/if}
    <SidebarMenu>
        {#each group.items as item (item.title)}
            <SidebarMenuItem>
                <Link href={item.href} class="block w-full">
                    <SidebarMenuButton isActive={item.href === $page.url}>
                        {#snippet tooltipContent()}
                            {item.title}
                        {/snippet}
                        {#if item.icon}
                            {@const Icon = item.icon}
                            <Icon class="h-4 w-4 shrink-0" />
                        {/if}
                        <span>{item.title}</span>
                    </SidebarMenuButton>
                </Link>
            </SidebarMenuItem>
        {/each}
    </SidebarMenu>
</SidebarGroup> -->
