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

    // Coordenadas geográficas
    public $latitude = null;
    public $longitude = null;

    // Campos de localização física
    public $building = '';
    public $floor = '';
    public $room = '';

    // Informações de contato
    public $phone = '';
    public $contact_email = '';
    public $contact_person = '';

    // Informações complementares
    public $complement = '';
    public $reference_point = '';
    public $postal_code = '';

    // Informações operacionais
    public $working_hours = '';
    public $website = '';
    public $has_accessibility = false;

    // Observações
    public $address_notes = '';

    // Campos adicionais
    public $department = '';
    public $campus = '';

    protected function getListeners()
    {
        return [
            'setInstitutionAddressFromPlacePicker' => 'setInstitutionAddressFromPlacePicker',
            'setCoordinatesFromMap' => 'setCoordinatesFromMap',
        ];
    }

    public function mount(Team $team)
    {
        $this->team = $team;

        // Dados de endereço principal e coordenadas
        $this->institution_address = $team->address ?? '';
        $this->latitude = $team->latitude ?? null;
        $this->longitude = $team->longitude ?? null;

        // Campos de localização física
        $this->building = $team->building ?? '';
        $this->floor = $team->floor ?? '';
        $this->room = $team->room ?? '';

        // Informações de contato
        $this->phone = $team->phone ?? '';
        $this->contact_email = $team->contact_email ?? '';
        $this->contact_person = $team->contact_person ?? '';

        // Informações complementares
        $this->complement = $team->complement ?? '';
        $this->reference_point = $team->reference_point ?? '';
        $this->postal_code = $team->postal_code ?? '';

        // Informações operacionais
        $this->working_hours = $team->working_hours ?? '';
        $this->website = $team->website ?? '';
        $this->has_accessibility = $team->has_accessibility ?? false;

        // Observações
        $this->address_notes = $team->address_notes ?? '';

        // Campos adicionais
        $this->department = $team->department ?? '';
        $this->campus = $team->campus ?? '';
    }

    public function updateInstitutionAddress()
    {
        Log::info('Tentativa de salvar endereço e informações adicionais', [
            'address' => $this->institution_address,
            'coordinates' => [
                'lat' => $this->latitude,
                'lng' => $this->longitude,
                'type_lat' => gettype($this->latitude),
                'type_lng' => gettype($this->longitude)
            ],
            'additional_fields' => [
                'building' => $this->building,
                'floor' => $this->floor,
                'room' => $this->room,
            ]
        ]);

        if (Gate::denies('updateAddress', $this->team)) {
            $this->dispatch('error', ['message' => 'Apenas o coordenador do laboratório pode atualizar o endereço.']);
            Log::warning('Usuário sem permissão para atualizar endereço', ['user_id' => auth()->id()]);
            return;
        }

        // Converter coordenadas para float antes da validação
        if ($this->latitude !== null) $this->latitude = (float) $this->latitude;
        if ($this->longitude !== null) $this->longitude = (float) $this->longitude;

        $this->validate([
            'institution_address' => 'nullable|string|max:255',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            // Campos de localização física
            'building' => 'nullable|string|max:255',
            'floor' => 'nullable|string|max:50',
            'room' => 'nullable|string|max:50',
            // Informações de contato
            'phone' => 'nullable|string|max:50',
            'contact_email' => 'nullable|email|max:255',
            'contact_person' => 'nullable|string|max:255',
            // Informações complementares
            'complement' => 'nullable|string|max:255',
            'reference_point' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:20',
            // Informações operacionais
            'working_hours' => 'nullable|string|max:255',
            'website' => 'nullable|url|max:255',
            'has_accessibility' => 'boolean',
            // Observações
            'address_notes' => 'nullable|string',
            // Campos adicionais
            'department' => 'nullable|string|max:255',
            'campus' => 'nullable|string|max:255',
        ]);

        // Verificar valores antes de salvar
        Log::info('Valores antes de salvar no banco de dados:', [
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'address' => $this->institution_address,
            'building' => $this->building,
            'floor' => $this->floor,
            'room' => $this->room,
            'other_fields' => [
                'phone' => $this->phone,
                'department' => $this->department,
                'campus' => $this->campus
            ]
        ]);

        $updated = $this->team->update([
            'address' => $this->institution_address,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            // Campos de localização física
            'building' => $this->building,
            'floor' => $this->floor,
            'room' => $this->room,
            // Informações de contato
            'phone' => $this->phone,
            'contact_email' => $this->contact_email,
            'contact_person' => $this->contact_person,
            // Informações complementares
            'complement' => $this->complement,
            'reference_point' => $this->reference_point,
            'postal_code' => $this->postal_code,
            // Informações operacionais
            'working_hours' => $this->working_hours,
            'website' => $this->website,
            'has_accessibility' => $this->has_accessibility,
            // Observações
            'address_notes' => $this->address_notes,
            // Campos adicionais
            'department' => $this->department,
            'campus' => $this->campus,
        ]);

        Log::info('Resultado da atualização:', [
            'updated' => $updated,
            'team_after_update' => [
                'id' => $this->team->id,
                'address' => $this->team->address,
                'latitude' => $this->team->latitude,
                'longitude' => $this->team->longitude,
                'building' => $this->team->building,
                'floor' => $this->team->floor,
                'room' => $this->team->room
            ]
        ]);

        if ($updated) {
            Log::info('Endereço, coordenadas e informações adicionais salvas com sucesso', [
                'team_id' => $this->team->id,
                'address' => $this->institution_address,
                'coordinates' => [
                    'lat' => $this->latitude,
                    'lng' => $this->longitude
                ]
            ]);
            $this->dispatch('success', ['message' => 'Endereço e informações adicionais atualizadas com sucesso!']);
        } else {
            Log::error('Falha ao salvar endereço e informações adicionais', ['team_id' => $this->team->id]);
            $this->dispatch('error', ['message' => 'Erro ao salvar as informações. Tente novamente.']);
        }
    }

    public function setInstitutionAddressFromPlacePicker($data = [])
    {
        Log::info('Endereço recebido do Place Picker', ['data' => $data]);

        if (is_array($data) && isset($data['address'])) {
            $this->institution_address = $data['address'];

            // Atualizar coordenadas se disponíveis e converter explicitamente para float
            if (isset($data['latitude']) && isset($data['longitude'])) {
                $this->latitude = (float) $data['latitude'];
                $this->longitude = (float) $data['longitude'];
                Log::info('Coordenadas atualizadas do Place Picker', [
                    'lat' => $this->latitude,
                    'lng' => $this->longitude,
                    'type_lat' => gettype($this->latitude),
                    'type_lng' => gettype($this->longitude)
                ]);
            }

            // Tentar extrair o CEP do endereço (formato brasileiro)
            if (preg_match('/\d{5}-\d{3}/', $this->institution_address, $matches)) {
                $this->postal_code = $matches[0];
            }

            Log::info('Novo valor de institution_address', ['value' => $this->institution_address]);
        } else {
            Log::warning('Formato de dados inválido recebido do Place Picker', ['data' => $data]);
        }
    }
    /**
     * Recebe as coordenadas diretamente do JavaScript
     */
    public function setCoordinatesFromMap($data = [])
    {
        if (is_array($data) && isset($data['lat']) && isset($data['lng'])) {
            $this->latitude = (float) $data['lat'];
            $this->longitude = (float) $data['lng'];

            Log::info('Coordenadas atualizadas diretamente do mapa', [
                'lat' => $this->latitude,
                'lng' => $this->longitude,
                'type_lat' => gettype($this->latitude),
                'type_lng' => gettype($this->longitude)
            ]);
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
