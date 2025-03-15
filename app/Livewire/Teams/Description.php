<?php

namespace App\Livewire\Teams;

use Livewire\Component;
use App\Models\Team;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class Description extends Component
{
    public $team;
    public $description = '';

    public function mount(Team $team)
    {
        $this->team = $team;
        $this->description = $team->description ?? '';
    }

    public function updateDescription()
    {
        Log::info('Tentativa de salvar descrição', [
            'description' => $this->description,
            'team_id' => $this->team->id,
        ]);

        if (Gate::denies('updateDescription', $this->team)) {
            $this->dispatch('error', ['message' => 'Apenas o coordenador do laboratório pode atualizar a descrição.']);
            Log::warning('Usuário sem permissão para atualizar descrição', ['user_id' => auth()->id()]);
            return;
        }

        $this->validate([
            'description' => 'nullable|string|max:1000',
        ]);

        Log::info('Valor de description antes de atualizar', [
            'description' => $this->description,
        ]);

        try {
            $updated = $this->team->update([
                'description' => $this->description,
            ]);

            Log::info('Resultado da atualização da descrição', [
                'updated' => $updated,
                'team_after_update' => [
                    'id' => $this->team->id,
                    'description' => $this->team->description,
                ],
            ]);

            if ($updated) {
                Log::info('Descrição salva com sucesso', [
                    'team_id' => $this->team->id,
                    'description' => $this->description,
                ]);
                $this->dispatch('success', ['message' => 'Descrição atualizada com sucesso!']);
            } else {
                Log::error('Falha ao salvar descrição - Nenhum dado atualizado', ['team_id' => $this->team->id]);
                $this->dispatch('error', ['message' => 'Erro ao salvar a descrição. Tente novamente.']);
            }
        } catch (\Exception $e) {
            Log::error('Exceção ao salvar descrição', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'team_id' => $this->team->id,
            ]);
            $this->dispatch('error', ['message' => 'Erro ao salvar a descrição: ' . $e->getMessage()]);
        }
    }

    public function render()
    {
        if (Gate::denies('viewDescription', $this->team)) {
            abort(403, 'Você precisa estar autenticado para visualizar a descrição deste laboratório.');
        }

        return view('livewire.teams.description');
    }
}
