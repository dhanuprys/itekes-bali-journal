import '@inertiajs/svelte';
import type { Config } from 'ziggy-js';

export interface Auth {
    user: User;
}

export interface BreadcrumbItem {
    title: string;
    href: string;
}

export interface NavItem {
    title: string;
    href: string;
    icon?: any;
    badge?: string;
    isActive?: boolean;
    items?: NavItem[];
    match?: string;
}

export interface NavGroup {
    label: string | null;
    items: (NavItem | null)[];
}

export type PageProps<T extends Record<string, unknown> = Record<string, unknown>> = T & {
    name: string;
    quote: { message: string; author: string };
    auth: Auth;
    [key: string]: unknown;
    ziggy: Config & { location: string };
};

export interface User {
    id: number;
    name: string;
    email: string;
    avatar?: string;
    roles: string[];
    permissions: string[];
    email_verified_at: string | null;
    two_factor_confirmed_at: string | null;
    created_at: string;
    updated_at: string;
}

export type BreadcrumbItemType = BreadcrumbItem;

export interface ResearchSubmission {
    id: number;
    user_id: number;
    stage: string;
    status: string;
    created_at: string;
    updated_at: string;
    latest_detail?: ResearchSubmissionDetail;
    details?: ResearchSubmissionDetail[];
}

export interface ResearchSubmissionDetail {
    id: number;
    research_submission_id: number;
    title: string;
    leader_name: string;
    leader_nidn: string;
    budget: number;
    proposal_path: string;
    study_program_id: number;
    research_schema_id?: number;
    research_target_id?: number;
    study_program?: any;
    research_schema?: any;
    research_target?: any;
    created_at: string;
    updated_at: string;
}

export interface CommunityServiceSubmission {
    id: number;
    user_id: number;
    stage: string;
    status: string;
    created_at: string;
    updated_at: string;
    latest_detail?: CommunityServiceSubmissionDetail;
    details?: CommunityServiceSubmissionDetail[];
}

export interface CommunityServiceSubmissionDetail {
    id: number;
    community_service_submission_id: number;
    title: string;
    leader_name: string;
    leader_nidn: string;
    budget: number;
    proposal_path: string;
    study_program_id: number;
    community_service_schema_id?: number;
    community_service_target_id?: number;
    study_program?: any;
    schema?: any;
    target?: any;
    created_at: string;
    updated_at: string;
}

export interface NotificationPayload {
    title: string;
    url?: string;
    type: 'info' | 'success' | 'warning' | 'error';
    metadata?: Record<string, any>;
}

export interface Notification {
    id: string;
    type: string;
    notifiable_type: string;
    notifiable_id: number;
    data: {
        message: string;
        payload?: NotificationPayload;
    };
    content: string; // aggregated from data.message
    extra?: NotificationPayload; // aggregated from data.payload
    read_at: string | null;
    created_at: string;
    updated_at: string;
}
