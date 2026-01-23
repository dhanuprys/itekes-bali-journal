<?php

namespace Database\Seeders;

use App\Models\User;
use App\Services\PermissionAndRoleDictionary;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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
        $admin = Role::updateOrCreate(['name' => PermissionAndRoleDictionary::getRoleCode('ADMIN')]);
        $lecture = Role::updateOrCreate(['name' => PermissionAndRoleDictionary::getRoleCode('LECTURE')]);
        $guest = Role::updateOrCreate(['name' => PermissionAndRoleDictionary::getRoleCode('GUEST')]);
        $operator = Role::updateOrCreate(['name' => PermissionAndRoleDictionary::getRoleCode('OPERATOR')]);
        $reviewerResearch = Role::updateOrCreate(['name' => PermissionAndRoleDictionary::getRoleCode('REVIEWER_RESEARCH')]);
        $reviewerCommunityService = Role::updateOrCreate(['name' => PermissionAndRoleDictionary::getRoleCode('REVIEWER_COMMUNITY_SERVICE')]);

        // manage users, roles, permissions
        $permissionManageUsers = Permission::updateOrCreate(['name' => PermissionAndRoleDictionary::getPermissionCode('MANAGE_USERS')]);
        // manage options based table
        $permissionManageForm = Permission::updateOrCreate(['name' => PermissionAndRoleDictionary::getPermissionCode('MANAGE_FORM')]);
        // apply for review
        $permissionRequestResearchReview = Permission::updateOrCreate(['name' => PermissionAndRoleDictionary::getPermissionCode('REQUEST_RESEARCH_REVIEW')]);
        $permissionRequestCommunityServiceReview = Permission::updateOrCreate(['name' => PermissionAndRoleDictionary::getPermissionCode('REQUEST_COMMUNITY_SERVICE_REVIEW')]);
        $permissionRequestEthicsReview = Permission::updateOrCreate(['name' => PermissionAndRoleDictionary::getPermissionCode('REQUEST_ETHICS_REVIEW')]);
        // manage reviewer assignment
        $permissionAssignReviewerResearch = Permission::updateOrCreate(['name' => PermissionAndRoleDictionary::getPermissionCode('ASSIGN_REVIEWER_RESEARCH')]);
        $permissionAssignReviewerCommunityService = Permission::updateOrCreate(['name' => PermissionAndRoleDictionary::getPermissionCode('ASSIGN_REVIEWER_COMMUNITY_SERVICE')]);
        $permissionReviewResearch = Permission::updateOrCreate(['name' => PermissionAndRoleDictionary::getPermissionCode('REVIEW_RESEARCH')]);
        $permissionReviewCommunityService = Permission::updateOrCreate(['name' => PermissionAndRoleDictionary::getPermissionCode('REVIEW_COMMUNITY_SERVICE')]);
        $permissionReviewEthics = Permission::updateOrCreate(['name' => PermissionAndRoleDictionary::getPermissionCode('REVIEW_ETHICS')]);
        $permissionViewAllResearch = Permission::updateOrCreate(['name' => PermissionAndRoleDictionary::getPermissionCode('VIEW_ALL_RESEARCH')]);
        $permissionViewAllCommunityService = Permission::updateOrCreate(['name' => PermissionAndRoleDictionary::getPermissionCode('VIEW_ALL_COMMUNITY_SERVICE')]);
        $permissionViewAllEthics = Permission::updateOrCreate(['name' => PermissionAndRoleDictionary::getPermissionCode('VIEW_ALL_ETHICS')]);
        // view for user logs
        $permissionViewUserLogs = Permission::updateOrCreate(['name' => PermissionAndRoleDictionary::getPermissionCode('VIEW_USER_LOGS')]);

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
