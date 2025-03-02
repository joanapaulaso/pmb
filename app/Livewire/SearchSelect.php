<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Country;
use App\Models\State;
use App\Models\Municipality;
use App\Models\Institution;
use App\Models\Laboratory;

class SearchSelect extends Component
{
    public $search = '';
    public $model;
    public $field;
    public $dependsOn = [];
    public $selectedId;
    public $placeholder;
    public $initialValue;

    public function mount($model, $field, $dependsOn = [], $placeholder = '', $initialValue = null)
    {
        $this->model = $model;
        $this->field = $field;
        $this->dependsOn = $dependsOn;
        $this->placeholder = $placeholder;
        $this->selectedId = $initialValue;
        $this->initialValue = $initialValue;

        $this->search = $this->selectedId ? $this->getSelectedName() : '';

        \Log::info("SearchSelect mounted for field: {$field} with initialValue: " . ($initialValue ?? 'null'));
    }

    public function updatedSearch()
    {
        if ($this->selectedId && $this->search !== $this->getSelectedName()) {
            // If user modifies the text after selecting an option, clear the selection
            $this->selectedId = null;
        }
    }

    public function selectOption($idOrCode)
    {
        \Log::info("Option selected in SearchSelect for {$this->field}: {$idOrCode}");
        $modelClass = $this->getModelClass();
        $primaryKey = $this->getPrimaryKey($this->model);

        $this->selectedId = $idOrCode;
        $this->search = $modelClass::where($primaryKey, $idOrCode)->value('name') ?? '';

        // Dispatch event to parent component
        $this->dispatch('optionSelected', [
            'field' => $this->field,
            'value' => $idOrCode
        ]);
    }

    private function getSelectedName()
    {
        if ($this->selectedId) {
            $modelClass = $this->getModelClass();
            $primaryKey = $this->getPrimaryKey($this->model);
            return $modelClass::where($primaryKey, $this->selectedId)->value('name') ?? '';
        }
        return '';
    }

    private function getModelClass()
    {
        return match ($this->model) {
            'states' => State::class,
            'municipalities' => Municipality::class,
            'countries' => Country::class,
            'institutions' => Institution::class,
            'laboratories' => Laboratory::class,
            default => throw new \Exception("Modelo inválido: {$this->model}"),
        };
    }

    public function getPrimaryKey($model)
    {
        return match ($this->model) {
            'countries' => 'code',
            default => 'id',
        };
    }

    public function render()
    {
        $modelClass = $this->getModelClass();
        $query = $modelClass::query();

        // Apply any dependency filters (like filtering municipalities by state)
        foreach ($this->dependsOn as $key => $value) {
            if ($value) {
                $query->where($key, $value);
            }
        }

        // Search results are only shown when:
        // 1. There is search text
        // 2. No option is currently selected
        $results = ($this->search && !$this->selectedId)
            ? $query->where('name', 'like', '%' . $this->search . '%')
            ->limit(10)
            ->get()
            : collect();

        // Make sure selected item's text is always displayed
        if ($this->selectedId && empty($this->search)) {
            $this->search = $this->getSelectedName();
        }

        return view('livewire.search-select', [
            'results' => $results,
        ]);
    }
}
