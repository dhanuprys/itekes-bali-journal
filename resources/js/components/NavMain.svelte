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
    import type { NavGroup, NavItem } from '@/types';
    import { Link, page, router } from '@inertiajs/svelte';
    import { ChevronRightIcon } from 'lucide-svelte';

    interface Props {
        group: NavGroup;
    }

    let { group }: Props = $props();
    const { setOpen } = useSidebar();
    const filteredItems = $derived(group.items.filter((item) => item !== null));

    // Normalize path to ignore trailing slashes for consistent comparison
    const normalizePath = (path: string) => (path.length > 1 && path.endsWith('/') ? path.slice(0, -1) : path);

    // Derived active path from current URL (ignoring query params)
    let currentPath = $derived(normalizePath($page.url.split('?')[0]));

    /**
     * Checks if a navigation item is active based on the current path.
     * Implements strict path matching to avoid false positives (e.g., /user matching /users).
     */
    function isActive(item: NavItem): boolean {
        if (!item.href || item.href === '#') return false;

        // 1. Priority: Custom match pattern
        if (item.match) {
            return currentPath.startsWith(item.match);
        }

        const itemPath = normalizePath(item.href);

        // 2. Exact match
        if (itemPath === currentPath) return true;

        // 3. Nested path match (ensuring boundary with '/')
        // Only valid if item is not root '/'
        if (itemPath !== '/' && currentPath.startsWith(`${itemPath}/`)) {
            return true;
        }

        return false;
    }

    /**
     * Recursively checks if any child item matches the active state.
     * Used for auto-expanding parent menus.
     */
    function hasActiveChild(items: NavItem[] = []): boolean {
        return items.some((item) => isActive(item) || hasActiveChild(item.items));
    }
</script>

<SidebarGroup class="px-2 py-0">
    {#if group.label && filteredItems.length > 0}
        <SidebarGroupLabel>{group.label}</SidebarGroupLabel>
    {/if}
    <SidebarMenu>
        {#each filteredItems as item (item.title)}
            {#if item.items}
                <Collapsible open={isActive(item) || hasActiveChild(item.items)} class="group/collapsible">
                    {#snippet child({ props })}
                        <SidebarMenuItem {...props}>
                            <CollapsibleTrigger>
                                {#snippet child({ props })}
                                    <button
                                        type="button"
                                        class="w-full"
                                        onclick={() => {
                                            if (item.href !== '#') {
                                                router.visit(item.href, {
                                                    preserveState: true,
                                                    preserveScroll: true,
                                                });
                                            }
                                            setOpen(true);
                                        }}
                                    >
                                        <SidebarMenuButton {...props} tooltipContent={item.title} isActive={isActive(item)}>
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
                                            <SidebarMenuSubButton isActive={isActive(subItem)}>
                                                {#snippet child({ props })}
                                                    <Link class="flex items-center justify-between" href={subItem.href} {...props}>
                                                        <div class="flex items-center gap-x-2">
                                                            {#if subItem.icon}
                                                                <subItem.icon class="size-3" />
                                                            {/if}
                                                            <span>{subItem.title}</span>
                                                        </div>
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
                        <SidebarMenuButton isActive={isActive(item)}>
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
