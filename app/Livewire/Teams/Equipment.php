<?php

namespace App\Livewire\Teams;

use Livewire\Component;
use App\Models\Team;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class Equipment extends Component
{
    use WithFileUploads;

    public $team;
    public $equipments = [];

    protected $rules = [
        'equipments.*.model' => 'required|string|max:255',
        'equipments.*.brand' => 'required|string|max:255',
        'equipments.*.technical_responsible' => 'required|string|max:255',
        'equipments.*.available_for_services' => 'boolean',
        'equipments.*.available_for_collaboration' => 'boolean',
        'equipments.*.photo' => 'nullable|image|max:2048',
    ];

    public function mount(Team $team)
    {
        $this->team = $team;

        if ($this->team->equipments) {
            foreach ($this->team->equipments as $equipment) {
                $this->equipments[] = [
                    'id' => $equipment->id,
                    'model' => $equipment->model,
                    'brand' => $equipment->brand,
                    'technical_responsible' => $equipment->technical_responsible,
                    'available_for_services' => $equipment->available_for_services,
                    'available_for_collaboration' => $equipment->available_for_collaboration,
                    'photo_path' => $equipment->photo_path,
                    'photo' => null,
                ];
            }
        }

        if (empty($this->equipments)) {
            $this->addEquipment();
        }
    }

    public function addEquipment()
    {
        $this->equipments[] = [
            'id' => null,
            'model' => '',
            'brand' => '',
            'technical_responsible' => '',
            'available_for_services' => false,
            'available_for_collaboration' => false,
            'photo' => null,
            'photo_path' => null,
        ];
    }

    public function removeEquipment($index)
    {
        if (isset($this->equipments[$index])) {
            if (!empty($this->equipments[$index]['id'])) {
                $equipment = $this->team->equipments()->find($this->equipments[$index]['id']);
                if ($equipment) {
                    Storage::delete($equipment->photo_path);
                    $equipment->delete();
                }
            }
            unset($this->equipments[$index]);
            $this->equipments = array_values($this->equipments);
        }
    }

    public function saveEquipments()
    {
        if (Gate::denies('updateEquipment', $this->team)) {
            $this->dispatch('error', ['message' => 'Apenas o coordenador do laboratório pode atualizar os equipamentos.']);
            Log::warning('Usuário sem permissão para atualizar equipamentos', ['user_id' => auth()->id()]);
            return;
        }

        $this->validate();

        foreach ($this->equipments as $index => $equipmentData) {
            Log::debug('Processando equipamento', [
                'index' => $index,
                'equipmentData' => $equipmentData,
            ]);

            $data = [
                'model' => $equipmentData['model'],
                'brand' => $equipmentData['brand'],
                'technical_responsible' => $equipmentData['technical_responsible'],
                'available_for_services' => $equipmentData['available_for_services'],
                'available_for_collaboration' => $equipmentData['available_for_collaboration'],
            ];

            if ($equipmentData['photo']) {
                $path = $equipmentData['photo']->store('equipment_photos', 'public');
                $data['photo_path'] = $path;

                if (!empty($equipmentData['id']) && !empty($equipmentData['photo_path'])) {
                    Storage::delete($equipmentData['photo_path']);
                }
            } elseif (!empty($equipmentData['photo_path'])) {
                $data['photo_path'] = $equipmentData['photo_path'];
            }

            if (!empty($equipmentData['id'])) {
                Log::info('Atualizando equipamento existente', [
                    'equipment_id' => $equipmentData['id'],
                    'data' => $data,
                ]);
                $equipment = $this->team->equipments()->find($equipmentData['id']);
                if ($equipment) {
                    $equipment->update($data);
                    // Atualizar o array $equipments com os dados atualizados
                    $this->equipments[$index]['photo_path'] = $equipment->photo_path;
                } else {
                    Log::warning('Equipamento não encontrado para atualização', [
                        'equipment_id' => $equipmentData['id'],
                    ]);
                }
            } else {
                Log::info('Criando novo equipamento', [
                    'data' => $data,
                ]);
                $newEquipment = $this->team->equipments()->create($data);
                // Atualizar o array $equipments com o ID do novo equipamento
                $this->equipments[$index]['id'] = $newEquipment->id;
                $this->equipments[$index]['photo_path'] = $newEquipment->photo_path;
            }
        }

        Log::info('Equipamentos salvos com sucesso', ['team_id' => $this->team->id, 'equipments' => $this->equipments]);
        $this->dispatch('success', ['message' => 'Equipamentos atualizados com sucesso!']);
    }

    public function render()
    {
        return view('livewire.teams.equipment');
    }
}
