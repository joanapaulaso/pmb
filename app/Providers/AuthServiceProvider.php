<?php

namespace App\Providers;

use App\Models\Post;
use App\Policies\PostPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        Post::class => PostPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::define('create', [Team::class], function ($user) {
            return true; // Permite que todos os usuários criem times (ajuste conforme necessário)
        });

        Gate::define('addTeamMember', function ($user, $team) {
            return $user->ownsTeam($team) || $user->hasRole('admin'); // Ajuste conforme necessário
        });
    }
}
