<?php

namespace App\Policies;

use App\Models\Team;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TeamPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Team $team): bool
    {
        return $user->belongsToTeam($team);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Team $team): bool
    {
        return $user->ownsTeam($team);
    }

    /**
     * Determine whether the user can add team members.
     */
    public function addTeamMember(User $user, Team $team): bool
    {
        return $user->ownsTeam($team);
    }

    /**
     * Determine whether the user can update team member permissions.
     */
    public function updateTeamMember(User $user, Team $team): bool
    {
        return $user->ownsTeam($team);
    }

    /**
     * Determine whether the user can remove team members.
     */
    public function removeTeamMember(User $user, Team $team): bool
    {
        return $user->ownsTeam($team);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Team $team): bool
    {
        return $user->ownsTeam($team);
    }

    // Apenas o coordenador pode atualizar o endereço
    public function updateAddress(User $user, Team $team)
    {
        // Verifica se o usuário é o dono do time (coordenador)
        return $team->user_id === $user->id;
    }

    // Qualquer usuário autenticado pode visualizar o endereço
    public function viewAddress(?User $user, Team $team)
    {
        // Retorna true se o usuário estiver autenticado
        return !is_null($user);
    }

    public function viewEquipment(?User $user, Team $team)
    {
        return !is_null($user); // Apenas usuários autenticados podem visualizar
    }

    public function updateEquipment(User $user, Team $team)
    {
        return $user->ownsTeam($team); // Apenas o dono (coordenador) pode atualizar
    }

}
