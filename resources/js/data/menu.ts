import { NavGroup, NavItem, User } from '@/types';
import { BellIcon, FileTextIcon, LayoutGrid, LogsIcon, MicroscopeIcon, UsersIcon } from 'lucide-svelte';
import { permissions, roles } from './permission-and-role';

function validateAbility(userRoles: string[], userPermissions: string[]) {
    return (neededPermission: string, navItem: NavItem) => {
        if (!userRoles.includes(roles.ADMIN) && !userPermissions.includes(neededPermission)) return null;
        return navItem;
    };
}

export function createMenu(user: User): Record<string, NavGroup> {
    const validate = validateAbility(user.roles, user.permissions);
    return {
        general: {
            label: 'Umum',
            items: [
                {
                    title: 'Dashboard',
                    href: '/dashboard',
                    icon: LayoutGrid,
                },
                {
                    title: 'Notifikasi',
                    href: '/notifications',
                    icon: BellIcon,
                },
            ],
        },
        applyForReview: {
            label: 'Pengajuan',
            items: [
                validate(permissions.REQUEST_RESEARCH_REVIEW, {
                    title: 'Penelitian',
                    href: '/apply/research',
                    icon: MicroscopeIcon,
                    items: [
                        {
                            title: 'Usulan Awal',
                            href: '/apply/research/initial-proposal',
                        },
                        {
                            title: 'Laporan Kemajuan',
                            href: '/apply/research/progress-report',
                        },
                        {
                            title: 'Laporan Akhir',
                            href: '/apply/research/final-report',
                        },
                    ],
                }),
                validate(permissions.REQUEST_COMMUNITY_SERVICE_REVIEW, {
                    title: 'Pengabdian',
                    href: '/apply/community-service',
                    icon: UsersIcon,
                    items: [
                        {
                            title: 'Usulan Awal',
                            href: '/apply/community-service/initial-proposal',
                        },
                        {
                            title: 'Laporan Kemajuan',
                            href: '/apply/community-service/progress-report',
                        },
                        {
                            title: 'Laporan Akhir',
                            href: '/apply/community-service/final-report',
                        },
                    ],
                }),
                validate(permissions.REQUEST_ETHICS_REVIEW, {
                    title: 'Lembar Etik',
                    href: '/apply/ethics',
                    icon: FileTextIcon,
                    items: [
                        {
                            title: 'Usulan Awal',
                            href: '/apply/ethics/initial-proposal',
                        },
                        {
                            title: 'Output',
                            href: '/apply/ethics/output',
                        },
                    ],
                }),
            ],
        },

        reviewerMenu: {
            label: 'Reviewer',
            items: [
                validate(permissions.REVIEW_RESEARCH, {
                    title: 'Penelitian',
                    href: '/review/research',
                    icon: MicroscopeIcon,
                    items: [
                        {
                            title: 'Usulan Awal',
                            href: '/review/research/initial-proposal',
                        },
                        {
                            title: 'Laporan Kemajuan',
                            href: '/review/research/progress-report',
                        },
                        {
                            title: 'Laporan Akhir',
                            href: '/review/research/final-report',
                        },
                    ],
                }),
                validate(permissions.REVIEW_COMMUNITY_SERVICE, {
                    title: 'Pengabdian',
                    href: '/review/community-service',
                    icon: UsersIcon,
                    items: [
                        {
                            title: 'Usulan Awal',
                            href: '/review/community-service/initial-proposal',
                        },
                        {
                            title: 'Laporan Kemajuan',
                            href: '/review/community-service/progress-report',
                        },
                        {
                            title: 'Laporan Akhir',
                            href: '/review/community-service/final-report',
                        },
                    ],
                }),
                validate(permissions.REVIEW_ETHICS, {
                    title: 'Lembar Etik',
                    href: '/review/ethics',
                    icon: FileTextIcon,
                    items: [
                        {
                            title: 'Usulan Awal',
                            href: '/review/ethics/initial-proposal',
                        },
                        {
                            title: 'Output',
                            href: '/review/ethics/output',
                        },
                    ],
                }),
            ],
        },

        assignmentMenu: {
            label: 'Penugasan Reviewer',
            items: [
                validate(permissions.ASSIGN_REVIEWER_RESEARCH, {
                    title: 'Atur Reviewer Penelitian',
                    href: '/assign-reviewer/research',
                    icon: MicroscopeIcon,
                }),
                validate(permissions.ASSIGN_REVIEWER_COMMUNITY_SERVICE, {
                    title: 'Atur Reviewer Pengabdian',
                    href: '/assign-reviewer/community-service',
                    icon: UsersIcon,
                }),
            ],
        },

        masterMenu: {
            label: 'Sistem Dasar',
            items: [
                validate(permissions.MANAGE_USERS, {
                    title: 'Pengguna',
                    href: '/users',
                    icon: UsersIcon,
                    items: [
                        {
                            title: 'Role',
                            href: '/users/role',
                            icon: UsersIcon,
                        },
                        {
                            title: 'Permission',
                            href: '/users/permission',
                            icon: UsersIcon,
                        },
                    ],
                }),
                validate(permissions.MANAGE_FORM, {
                    title: 'Pilihan Form',
                    href: '#',
                    icon: FileTextIcon,
                    items: [
                        {
                            title: 'Opt. Target Penelitian',
                            href: '/master/research-target',
                            icon: MicroscopeIcon,
                        },
                        {
                            title: 'Opt. Skema Penelitian',
                            href: '/master/research-schema',
                            icon: MicroscopeIcon,
                        },
                        {
                            title: 'Opt. Target Pengabdian',
                            href: '/master/community-service-target',
                            icon: UsersIcon,
                        },
                        {
                            title: 'Opt. Skema Pengabdian',
                            href: '/master/community-service-schema',
                            icon: UsersIcon,
                        },
                        {
                            title: 'Opt. Subjek Penelitian',
                            href: '/master/ethic-subject',
                            icon: FileTextIcon,
                        },
                    ],
                }),
                validate(permissions.VIEW_USER_LOGS, {
                    title: 'User Logs',
                    href: '/user-logs',
                    icon: LogsIcon,
                }),
            ],
        },
    };
}
