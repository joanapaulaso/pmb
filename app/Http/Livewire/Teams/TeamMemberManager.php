<?php

namespace App\Http\Livewire\Teams;

use Laravel\Jetstream\Features;
use Laravel\Jetstream\Jetstream;
use Laravel\Jetstream\Http\Livewire\TeamMemberManager as BaseTeamMemberManager;

class TeamMemberManager extends BaseTeamMemberManager
{
    /**
     * Sobrescreve o estado inicial para já sugerir o papel de membro.
     *
     * @var array
     */
    public $addTeamMemberForm = [
        'email' => '',
        'role' => 'member',
    ];

    /**
     * Adiciona um novo membro ao time com papel padrão "member" quando não selecionado.
     */
    public function addTeamMember()
    {
        $this->resetErrorBag();

        $selectedRole = $this->addTeamMemberForm['role'] ?: 'member';

        if (Features::sendsTeamInvitations()) {
            app(\Laravel\Jetstream\Contracts\InvitesTeamMembers::class)->invite(
                $this->user,
                $this->team,
                $this->addTeamMemberForm['email'],
                $selectedRole
            );
        } else {
            app(\Laravel\Jetstream\Contracts\AddsTeamMembers::class)->add(
                $this->user,
                $this->team,
                $this->addTeamMemberForm['email'],
                $selectedRole
            );
        }

        $this->addTeamMemberForm = [
            'email' => '',
            'role' => 'member',
        ];

        $this->team = $this->team->fresh();

        $this->dispatch('saved');
    }

    /**
     * Abre o modal de gerenciamento de função sempre com um valor padrão.
     */
    public function manageRole($userId)
    {
        $this->currentlyManagingRole = true;
        $this->managingRoleFor = Jetstream::findUserByIdOrFail($userId);

        $role = $this->managingRoleFor->teamRole($this->team);
        $this->currentRole = $role ? $role->key : (optional($this->managingRoleFor->membership)->role ?? 'member');
    }
}
