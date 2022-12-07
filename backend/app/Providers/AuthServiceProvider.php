<?php

declare(strict_types=1);

namespace App\Providers;

use App\Data\Models\User;
use App\Guards\JWTGuard;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->registerPolicies();

        Auth::extend('jwt', function ($app, $name, array $config) {
            return new JWTGuard(Auth::createUserProvider($config['provider']));
        });

        Gate::define('staff', function (User $user) {
            return $user->role_id > User::ROLE_USER;
        });

        Gate::define('moderator', function (User $user) {
            return $user->role_id >= User::ROLE_MODERATOR;
        });

        Gate::define('admin', function (User $user) {
            return $user->role_id >= User::ROLE_ADMIN;
        });

        Gate::define('developer', function (User $user) {
            return $user->role_id >= User::ROLE_DEVELOPER;
        });
    }
}
