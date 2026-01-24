<?php

// always links to resources/js/data/permission-and-role.ts

namespace App\Enums;

enum PermissionRole: string
{
    // BUILT-IN ROLES
    case R_ADMIN = 'admin';
    case R_LECTURE = 'lecture';
    case R_GUEST = 'guest';
    case R_OPERATOR = 'operator';
    case R_REVIEWER_RESEARCH = 'reviewer-research';
    case R_REVIEWER_COMMUNITY_SERVICE = 'reviewer-community-service';


    // BUILT-IN PERMISSIONS (IMMUTABLE)
    case P_MANAGE_USERS = 'manage users';
    case P_MANAGE_BASE = 'manage base';
    case P_MANAGE_FORM = 'manage form';
    case P_REQUEST_RESEARCH_REVIEW = 'request research review';
    case P_REQUEST_COMMUNITY_SERVICE_REVIEW = 'request community service review';
    case P_REQUEST_ETHICS_REVIEW = 'request ethics review';
    case P_ASSIGN_REVIEWER_RESEARCH = 'assign reviewer research';
    case P_ASSIGN_REVIEWER_COMMUNITY_SERVICE = 'assign reviewer community service';
    case P_REVIEW_RESEARCH = 'review research';
    case P_REVIEW_COMMUNITY_SERVICE = 'review community service';
    case P_REVIEW_ETHICS = 'review ethics';
    case P_VIEW_ALL_RESEARCH = 'view all research';
    case P_VIEW_ALL_COMMUNITY_SERVICE = 'view all community service';
    case P_VIEW_ALL_ETHICS = 'view all ethics';
    case P_VIEW_USER_LOGS = 'view user logs';

    public static function getRoleAsArray()
    {
        return [
            self::R_ADMIN,
            self::R_LECTURE,
            self::R_GUEST,
            self::R_OPERATOR,
            self::R_REVIEWER_RESEARCH,
            self::R_REVIEWER_COMMUNITY_SERVICE,
        ];
    }
}