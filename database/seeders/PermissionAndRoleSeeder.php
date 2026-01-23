<?php

namespace Database\Seeders;

use App\Models\User;
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
        $admin = Role::create(['name' => 'admin']);
        $operator = Role::create(['name' => 'operator']);
        $reviewerResearch = Role::create(['name' => 'reviewer-research']);
        $reviewerCommunityService = Role::create(['name' => 'reviewer-community-service']);

        $userAdmin = User::updateOrCreate([
            'email' => 'admin@itekes-bali.ac.id',
        ], [
            'name' => 'Admin',
            'password' => bcrypt('password'),
        ]);
        $userAdmin->assignRole($admin);

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
