<?php

// always links to resources/js/data/permission-and-role.ts

namespace App\Services;

class PermissionAndRoleDictionary
{
    // BUILT-IN ROLES
    public static $roles = [
        'ADMIN' => 'admin',
        'LECTURE' => 'lecture',
        'GUEST' => 'guest',
        'OPERATOR' => 'operator',
        'REVIEWER_RESEARCH' => 'reviewer-research',
        'REVIEWER_COMMUNITY_SERVICE' => 'reviewer-community-service'
    ];

    // BUILT-IN PERMISSIONS (IMMUTABLE)
    public static $permissions = [
        'MANAGE_USERS' => 'manage users',
        'MANAGE_FORM' => 'manage form',
        'REQUEST_RESEARCH_REVIEW' => 'request research review',
        'REQUEST_COMMUNITY_SERVICE_REVIEW' => 'request community service review',
        'REQUEST_ETHICS_REVIEW' => 'request ethics review',
        'ASSIGN_REVIEWER_RESEARCH' => 'assign reviewer research',
        'ASSIGN_REVIEWER_COMMUNITY_SERVICE' => 'assign reviewer community service',
        'REVIEW_RESEARCH' => 'review research',
        'REVIEW_COMMUNITY_SERVICE' => 'review community service',
        'REVIEW_ETHICS' => 'review ethics',
        'VIEW_ALL_RESEARCH' => 'view all research',
        'VIEW_ALL_COMMUNITY_SERVICE' => 'view all community service',
        'VIEW_ALL_ETHICS' => 'view all ethics',
        'VIEW_USER_LOGS' => 'view user logs',
    ];

    public static function getRoleCode($key)
    {
        return self::$roles[$key];
    }

    public static function getPermissionCode($key)
    {
        return self::$permissions[$key];
    }
}