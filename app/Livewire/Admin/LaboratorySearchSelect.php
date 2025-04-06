<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Institution;
use App\Models\State;
use Illuminate\Support\Facades\Log;

class LaboratorySearchSelect extends Component
{
    public $search = '';
    public $model;
    public $field;
    public $dependsOn = [];
    public $selectedId;
    public $placeholder;

    protected $listeners = ['dependencyChanged'];

    public function mount($model, $field, $dependsOn = [], $placeholder = '', $initialValue = null)
    {
        $this->model = $model;
        $this->field = $field;
        $this->dependsOn = $dependsOn;
        $this->placeholder = $placeholder;
        $this->selectedId = $initialValue;

        $this->search = $this->selectedId ? $this->getSelectedName() : '';
        Log::info("Mounted LaboratorySearchSelect for {$this->field} with selectedId: " . ($this->selectedId ?? 'null') . ", dependsOn: " . json_encode($this->dependsOn));
    }

    public function updatedSearch()
    {
        if ($this->selectedId && $this->search !== $this->getSelectedName()) {
            $this->selectedId = null;
            $this->dispatch('optionSelected', ['field' => $this->field, 'value' => null]);
            Log::info("Cleared selectedId for {$this->field} due to search change");
        }
    }

    public function selectOption($id)
    {
        $this->selectedId = $id;
        $this->search = $this->getSelectedName();
        $this->dispatch('optionSelected', ['field' => $this->field, 'value' => $id]);
        Log::info("Selected option for {$this->field}: {$id}");
    }

    public function dependencyChanged($field, $value)
    {
        if ($this->field === 'institution_id' && array_key_exists($field, $this->dependsOn)) {
            $this->dependsOn[$field] = $value;
            $this->selectedId = null;
            $this->search = '';
            $this->dispatch('optionSelected', ['field' => $this->field, 'value' => null]);
            Log::info("Dependency changed for {$this->field}: {$field} = " . ($value ?? 'null') . ", dependsOn updated: " . json_encode($this->dependsOn));
        } else {
            Log::info("Dependency change ignored for {$this->field}: field {$field} not relevant");
        }
    }

    private function getSelectedName()
    {
        if ($this->selectedId) {
            $modelClass = $this->getModelClass();
            $primaryKey = $this->getPrimaryKey();
            $name = $modelClass::where($primaryKey, $this->selectedId)->value('name');
            Log::info("Fetched name for {$this->model} with {$primaryKey} = {$this->selectedId}: " . ($name ?? 'not found'));
            return $name ?? '';
        }
        return '';
    }

    private function getPrimaryKey()
    {
        return 'id';
    }

    private function getModelClass()
    {
        return match ($this->model) {
            'institutions' => Institution::class,
            'states' => State::class,
            default => throw new \Exception("Modelo inválido: {$this->model}"),
        };
    }

    public function render()
    {
        $modelClass = $this->getModelClass();
        $primaryKey = $this->getPrimaryKey();
        $query = $modelClass::query();

        // Aplicar filtros com base nas dependências
        foreach ($this->dependsOn as $key => $value) {
            if ($value !== null && $value !== '') {
                $query->where($key, $value);
                Log::info("Applying filter for {$this->field}: {$key} = {$value}");
            } else {
                // Se o valor da dependência for nulo, não mostrar resultados até que a dependência seja preenchida
                if ($this->model === 'institutions') {
                    $query->where($key, ''); // Isso garante que não haja resultados se state_id for nulo
                    Log::info("No valid {$key} provided for {$this->field}, returning empty results");
                }
            }
        }

        $results = ($this->search && !$this->selectedId)
            ? $query->where('name', 'like', '%' . $this->search . '%')->limit(10)->get()
            : collect();

        Log::info("Rendering LaboratorySearchSelect for {$this->field} with results: " . $results->count() . " items");
        return view('livewire.admin.laboratory-search-select', [
            'results' => $results,
            'primaryKey' => $primaryKey,
        ]);
    }
}