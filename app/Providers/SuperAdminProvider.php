<?php

namespace App\Providers;

use App\Enums\PermissionRole;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class SuperAdminProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Gate::before(function ($user, $ability) {
            if ($user->hasRole(PermissionRole::R_ADMIN->value)) {
                return true;
            }

            return null;
        });
    }
}
