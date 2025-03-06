<?php

namespace App\Livewire\Teams;

use Livewire\Component;
use App\Models\Team;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class Address extends Component
{
    public $team;
    public $institution_address = '';
    public $showNewInstitution = false;

    // Método para Livewire 3
    protected function getListeners()
    {
        return [
            'setInstitutionAddressFromPlacePicker' => 'setInstitutionAddressFromPlacePicker',
        ];
    }

    public function mount(Team $team)
    {
        $this->team = $team;
        $this->institution_address = $team->address ?? '';
    }

    public function updateInstitutionAddress()
    {
        Log::info('Tentativa de salvar endereço', ['address' => $this->institution_address]);

        if (Gate::denies('updateAddress', $this->team)) {
            $this->dispatch('error', ['message' => 'Apenas o coordenador do laboratório pode atualizar o endereço.']);
            Log::warning('Usuário sem permissão para atualizar endereço', ['user_id' => auth()->id()]);
            return;
        }

        $this->validate([
            'institution_address' => 'nullable|string|max:255',
        ]);

        $updated = $this->team->update(['address' => $this->institution_address]);

        if ($updated) {
            Log::info('Endereço salvo com sucesso', ['team_id' => $this->team->id, 'address' => $this->institution_address]);
            $this->dispatch('success', ['message' => 'Endereço do laboratório atualizado com sucesso!']);
        } else {
            Log::error('Falha ao salvar endereço', ['team_id' => $this->team->id]);
            $this->dispatch('error', ['message' => 'Erro ao salvar o endereço. Tente novamente.']);
        }
    }

    /**
     * CORREÇÃO IMPORTANTE: Adicionado valor padrão para o parâmetro $data
     * para resolver o erro de injeção de dependência
     */
    public function setInstitutionAddressFromPlacePicker($data = [])
    {
        Log::info('Endereço recebido do Place Picker', ['data' => $data]);

        if (is_array($data) && isset($data['address'])) {
            $this->institution_address = $data['address'];
            Log::info('Novo valor de institution_address', ['value' => $this->institution_address]);
        } else {
            Log::warning('Formato de dados inválido recebido do Place Picker', ['data' => $data]);
        }
    }

    public function render()
    {
        if (Gate::denies('viewAddress', $this->team)) {
            abort(403, 'Você precisa estar autenticado para visualizar o endereço deste laboratório.');
        }

        return view('livewire.teams.address');
    }
}
