<?php

namespace App\Livewire\Teams;

use Livewire\Component;
use App\Models\Team;
use Illuminate\Support\Facades\Gate;

class LegacyInfo extends Component
{
    public Team $team;
    public $researchers = '';
    public $analytical_techniques = '';
    public $research_lines = '';

    public function mount(Team $team)
    {
        $this->team = $team;
        $this->researchers = $team->researchers ?? '';
        $this->analytical_techniques = $team->analytical_techniques ?? '';
        $this->research_lines = $team->research_lines ?? '';
    }

    public function save()
    {
        if (Gate::denies('updateDescription', $this->team)) {
            $this->dispatch('error', ['message' => 'Apenas o coordenador do laboratório pode atualizar estas informações.']);
            return;
        }

        $this->validate([
            'researchers' => 'nullable|string',
            'analytical_techniques' => 'nullable|string',
            'research_lines' => 'nullable|string',
        ]);

        $this->team->update([
            'researchers' => $this->researchers,
            'analytical_techniques' => $this->analytical_techniques,
            'research_lines' => $this->research_lines,
        ]);

        $this->dispatch('success', ['message' => 'Informações atualizadas com sucesso!']);
    }

    public function render()
    {
        if (Gate::denies('viewDescription', $this->team)) {
            abort(403, 'Você precisa estar autenticado para visualizar estas informações.');
        }

        return view('livewire.teams.legacy-info');
    }
}
