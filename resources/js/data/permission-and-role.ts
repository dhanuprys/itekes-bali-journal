// links to /app/Services/PermissionAndRoleDictionary.php
// documentation ready on backend

export const roles = {
    ADMIN: 'admin',
    LECTURE: 'lecture',
    GUEST: 'guest',
    OPERATOR: 'operator',
    REVIEWER_RESEARCH: 'reviewer-research',
    REVIEWER_COMMUNITY_SERVICE: 'reviewer-community-service',
};

export const permissions = {
    MANAGE_USERS: 'manage users',
    MANAGE_BASE: 'manage base',
    MANAGE_FORM: 'manage form',
    REQUEST_RESEARCH_REVIEW: 'request research review',
    REQUEST_COMMUNITY_SERVICE_REVIEW: 'request community service review',
    REQUEST_ETHICS_REVIEW: 'request ethics review',
    ASSIGN_REVIEWER_RESEARCH: 'assign reviewer research',
    ASSIGN_REVIEWER_COMMUNITY_SERVICE: 'assign reviewer community service',
    REVIEW_RESEARCH: 'review research',
    REVIEW_COMMUNITY_SERVICE: 'review community service',
    REVIEW_ETHICS: 'review ethics',
    VIEW_ALL_RESEARCH: 'view all research',
    VIEW_ALL_COMMUNITY_SERVICE: 'view all community service',
    VIEW_ALL_ETHICS: 'view all ethics',
    VIEW_USER_LOGS: 'view user logs',
};
