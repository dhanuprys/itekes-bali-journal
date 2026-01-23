<?php

namespace Database\Seeders;

use App\Enums\PermissionRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class PermissionAndRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = Role::updateOrCreate(['name' => PermissionRole::R_ADMIN->value]);
        $lecture = Role::updateOrCreate(['name' => PermissionRole::R_LECTURE->value]);
        $guest = Role::updateOrCreate(['name' => PermissionRole::R_GUEST->value]);
        $operator = Role::updateOrCreate(['name' => PermissionRole::R_OPERATOR->value]);
        $reviewerResearch = Role::updateOrCreate(['name' => PermissionRole::R_REVIEWER_RESEARCH->value]);
        $reviewerCommunityService = Role::updateOrCreate(['name' => PermissionRole::R_REVIEWER_COMMUNITY_SERVICE->value]);

        // manage users, roles, permissions
        $permissionManageUsers = Permission::updateOrCreate(['name' => PermissionRole::P_MANAGE_USERS->value]);
        // manage base values (e.g study program)
        $permissionManageBase = Permission::updateOrCreate(['name' => PermissionRole::P_MANAGE_BASE->value]);
        // manage options based table
        $permissionManageForm = Permission::updateOrCreate(['name' => PermissionRole::P_MANAGE_FORM->value]);
        // apply for review
        $permissionRequestResearchReview = Permission::updateOrCreate(['name' => PermissionRole::P_REQUEST_RESEARCH_REVIEW->value]);
        $permissionRequestCommunityServiceReview = Permission::updateOrCreate(['name' => PermissionRole::P_REQUEST_COMMUNITY_SERVICE_REVIEW->value]);
        $permissionRequestEthicsReview = Permission::updateOrCreate(['name' => PermissionRole::P_REQUEST_ETHICS_REVIEW->value]);
        // manage reviewer assignment
        $permissionAssignReviewerResearch = Permission::updateOrCreate(['name' => PermissionRole::P_ASSIGN_REVIEWER_RESEARCH->value]);
        $permissionAssignReviewerCommunityService = Permission::updateOrCreate(['name' => PermissionRole::P_ASSIGN_REVIEWER_COMMUNITY_SERVICE->value]);
        $permissionReviewResearch = Permission::updateOrCreate(['name' => PermissionRole::P_REVIEW_RESEARCH->value]);
        $permissionReviewCommunityService = Permission::updateOrCreate(['name' => PermissionRole::P_REVIEW_COMMUNITY_SERVICE->value]);
        $permissionReviewEthics = Permission::updateOrCreate(['name' => PermissionRole::P_REVIEW_ETHICS->value]);
        $permissionViewAllResearch = Permission::updateOrCreate(['name' => PermissionRole::P_VIEW_ALL_RESEARCH->value]);
        $permissionViewAllCommunityService = Permission::updateOrCreate(['name' => PermissionRole::P_VIEW_ALL_COMMUNITY_SERVICE->value]);
        $permissionViewAllEthics = Permission::updateOrCreate(['name' => PermissionRole::P_VIEW_ALL_ETHICS->value]);
        // view for user logs
        $permissionViewUserLogs = Permission::updateOrCreate(['name' => PermissionRole::P_VIEW_USER_LOGS->value]);

        $lecture->givePermissionTo([
            $permissionRequestResearchReview,
            $permissionRequestCommunityServiceReview,
            $permissionRequestEthicsReview
        ]);

        $guest->givePermissionTo([
            $permissionRequestEthicsReview
        ]);

        $operator->givePermissionTo([
            $permissionAssignReviewerResearch,
            $permissionAssignReviewerCommunityService,
            $permissionReviewEthics,
            $permissionManageForm,
            $permissionViewAllResearch,
            $permissionViewAllCommunityService,
            $permissionViewAllEthics,
            $permissionViewUserLogs
        ]);

        $reviewerResearch->givePermissionTo([
            $permissionReviewResearch,
            $permissionViewAllResearch,
        ]);

        $reviewerCommunityService->givePermissionTo([
            $permissionReviewCommunityService,
            $permissionViewAllCommunityService,
        ]);

        $userAdmin = User::updateOrCreate([
            'email' => 'admin@itekes-bali.ac.id',
        ], [
            'name' => 'Admin',
            'password' => bcrypt('password'),
        ]);
        $userAdmin->assignRole($admin);

        $userLecture = User::updateOrCreate([
            'email' => 'lecture@itekes-bali.ac.id',
        ], [
            'name' => 'Lecture',
            'password' => bcrypt('password'),
        ]);
        $userLecture->assignRole($lecture);

        $userGuest = User::updateOrCreate([
            'email' => 'guest@itekes-bali.ac.id',
        ], [
            'name' => 'Guest',
            'password' => bcrypt('password'),
        ]);
        $userGuest->assignRole($guest);

        $userOperator = User::updateOrCreate([
            'email' => 'operator@itekes-bali.ac.id',
        ], [
            'name' => 'Operator',
            'password' => bcrypt('password'),
        ]);
        $userOperator->assignRole($operator);

        $userReviewerResearch = User::updateOrCreate([
            'email' => 'reviewer-research@itekes-bali.ac.id',
        ], [
            'name' => 'Reviewer Research',
            'password' => bcrypt('password'),
        ]);
        $userReviewerResearch->assignRole($reviewerResearch);

        $userReviewerCommunityService = User::updateOrCreate([
            'email' => 'reviewer-community@itekes-bali.ac.id',
        ], [
            'name' => 'Reviewer Community Service',
            'password' => bcrypt('password'),
        ]);
        $userReviewerCommunityService->assignRole($reviewerCommunityService);
    }
}
