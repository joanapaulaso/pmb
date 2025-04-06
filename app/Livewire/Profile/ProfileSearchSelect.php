<?php

namespace App\Livewire\Profile;

use Livewire\Component;
use App\Models\State;
use App\Models\Municipality;
use App\Models\Institution;
use App\Models\Country;
use App\Models\Laboratory;

class ProfileSearchSelect extends Component
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

        // Carregar o nome do item selecionado, se houver um initialValue
        $this->search = $this->selectedId ? $this->getSelectedName() : '';
        \Log::info("Mounted ProfileSearchSelect for {$this->field} with selectedId: " . ($this->selectedId ?? 'null') . ", search: {$this->search}, dependsOn: " . json_encode($this->dependsOn));
    }

    public function updatedSearch()
    {
        if ($this->selectedId && $this->search !== $this->getSelectedName()) {
            $this->selectedId = null;
            $this->dispatch('optionSelected', ['field' => $this->field, 'value' => null]);
            \Log::info("Cleared selectedId for {$this->field} due to search change");
        }
    }

    public function selectOption($id)
    {
        $this->selectedId = $id;
        $this->search = $this->getSelectedName();
        $this->dispatch('optionSelected', ['field' => $this->field, 'value' => $id]);
        \Log::info("Selected option for {$this->field}: {$id}, dispatched to ProfileUpdate");
    }

    public function dependencyChanged($field, $value)
    {
        if (array_key_exists($field, $this->dependsOn)) {
            $this->dependsOn[$field] = $value;
            $this->selectedId = null;
            $this->search = '';
            $this->dispatch('optionSelected', ['field' => $this->field, 'value' => null]);
            \Log::info("Dependency changed for {$this->field}: {$field} = " . ($value ?? 'null'));
        }
    }

    private function getSelectedName()
    {
        if ($this->selectedId) {
            $modelClass = $this->getModelClass();
            $primaryKey = $this->getPrimaryKey();
            $name = $modelClass::where($primaryKey, $this->selectedId)->value('name');
            \Log::info("Fetched name for {$this->model} with {$primaryKey} = {$this->selectedId}: " . ($name ?? 'not found'));
            return $name ?? '';
        }
        return '';
    }

    private function getPrimaryKey()
    {
        return match ($this->model) {
            'countries' => 'code',
            default => 'id',
        };
    }

    private function getModelClass()
    {
        return match ($this->model) {
            'states' => State::class,
            'municipalities' => Municipality::class,
            'institutions' => Institution::class,
            'countries' => Country::class,
            'laboratories' => Laboratory::class,
            default => throw new \Exception("Modelo inválido: {$this->model}"),
        };
    }

    public function render()
    {
        $modelClass = $this->getModelClass();
        $primaryKey = $this->getPrimaryKey();
        $query = $modelClass::query();

        foreach ($this->dependsOn as $key => $value) {
            if ($value !== null && $value !== '') {
                $query->where($key, $value);
            }
        }

        $results = ($this->search && !$this->selectedId)
            ? $query->where('name', 'like', '%' . $this->search . '%')->limit(10)->get()
            : collect();

        \Log::info("Rendering ProfileSearchSelect for {$this->field} with selectedId: " . ($this->selectedId ?? 'null'));
        return view('livewire.profile.profile-search-select', [
            'results' => $results,
            'primaryKey' => $primaryKey,
        ]);
    }
}