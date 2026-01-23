<script lang="ts">
    import NavFooter from '@/components/NavFooter.svelte';
    import NavMain from '@/components/NavMain.svelte';
    import NavUser from '@/components/NavUser.svelte';
    import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
    import { type NavItem } from '@/types';
    import { Link, page } from '@inertiajs/svelte';
    import AppLogo from './AppLogo.svelte';
    import { BookOpen } from 'lucide-svelte';
    import { createMenu } from '@/data/menu';

    const user = $derived($page.props.auth.user);
    const menu = $derived(createMenu(user));

    const footerNavItems: NavItem[] = [
        {
            title: 'Changelog',
            href: '/changelog',
            icon: BookOpen,
        },
    ];
</script>

<Sidebar collapsible="icon" variant="inset">
    <SidebarHeader>
        <SidebarMenu>
            <SidebarMenuItem>
                <SidebarMenuButton size="lg">
                    <Link href={route('dashboard')}>
                        <AppLogo />
                    </Link>
                </SidebarMenuButton>
            </SidebarMenuItem>
        </SidebarMenu>
    </SidebarHeader>

    <SidebarContent>
        <NavMain group={menu.general} />
        <NavMain group={menu.applyForReview} />
        <NavMain group={menu.reviewerMenu} />
        <NavMain group={menu.assignmentMenu} />
        <NavMain group={menu.masterMenu} />
    </SidebarContent>

    <SidebarFooter>
        <NavFooter items={footerNavItems} class="mt-auto" />
        <NavUser />
    </SidebarFooter>
</Sidebar>
