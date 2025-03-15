<?php

namespace App\Providers;

use App\Models\Post;
use App\Policies\PostPolicy;
use App\Policies\PostPortalPolicy;
use App\Policies\TeamPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\Team;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        Post::class => PostPolicy::class,
        PostPortal::class => PostPortalPolicy::class,
        Team::class => TeamPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Definir permissões para criar times
        Gate::define('create', function ($user, $team = null) {
            return $user !== null; // Permite que apenas usuários autenticados criem times (ajuste conforme necessário)
        });

        // Definir permissões para adicionar membros ao time
        Gate::define('addTeamMember', function ($user, $team) {
            return $user->ownsTeam($team) || $user->hasRole('admin'); // Ajuste conforme necessário
        });

        Gate::define('viewDescription', function ($user, Team $team) {
            return $user->belongsToTeam($team); // Exemplo: usuário deve pertencer ao time
        });

        Gate::define('updateDescription', function ($user, Team $team) {
            return $user->ownsTeam($team); // Exemplo: apenas o dono do time pode atualizar
        });
    }
}
