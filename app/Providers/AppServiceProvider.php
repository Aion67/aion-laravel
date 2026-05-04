<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::define('access-pharmacy-operations', static fn (User $user): bool => in_array($user->role, [
            User::ROLE_ADMIN,
            User::ROLE_PHARMACIST,
        ], true));

        Gate::define('adjust-inventory', static fn (User $user): bool => $user->role === User::ROLE_ADMIN);
        Gate::define('view-stock-movements', static fn (User $user): bool => $user->role === User::ROLE_ADMIN);
        Gate::define('manage-sales', static fn (User $user): bool => $user->role === User::ROLE_ADMIN);
        Gate::define('view-reports', static fn (User $user): bool => $user->role === User::ROLE_ADMIN);
        Gate::define('manage-users', static fn (User $user): bool => $user->role === User::ROLE_ADMIN);
    }
}
