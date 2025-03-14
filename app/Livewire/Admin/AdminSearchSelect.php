<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\State;
use App\Models\Municipality;
use App\Models\Country;
use App\Models\Institution;
use App\Models\Laboratory;
use Illuminate\Support\Facades\Log;

class AdminSearchSelect extends Component
{
    public $search = '';
    public $model;
    public $field;
    public $selectedId = null;
    public $placeholder;
    public $stateId = null; // For institution filtering

    public function mount($model, $field, $placeholder = '', $initialValue = null, $stateId = null)
    {
        $this->model = $model;
        $this->field = $field;
        $this->placeholder = $placeholder;
        $this->selectedId = $initialValue;
        $this->stateId = $stateId;

        if ($this->selectedId) {
            $this->search = $this->getSelectedName();
        }

        Log::info("AdminSearchSelect mounted: model={$model}, field={$field}, initialValue={$initialValue}, stateId={$stateId}");
    }

    public function updatedSearch()
    {
        $this->selectedId = null;
        $this->dispatch('admin-search-changed', [
            'field' => $this->field,
            'value' => null
        ]);
    }

    public function selectOption($id, $name)
    {
        $this->selectedId = $id;
        $this->search = $name;

        Log::info("AdminSearchSelect selected: field={$this->field}, id={$id}, name={$name}");

        $this->dispatch('admin-search-selected', [
            'field' => $this->field,
            'value' => $id
        ]);
    }

    private function getSelectedName()
    {
        if (!$this->selectedId) {
            return '';
        }

        $model = $this->getModelClass();
        $primaryKey = $this->getPrimaryKey();

        $item = $model::where($primaryKey, $this->selectedId)->first();
        return $item ? $item->name : '';
    }

    private function getModelClass()
    {
        return match ($this->model) {
            'states' => State::class,
            'municipalities' => Municipality::class,
            'countries' => Country::class,
            'institutions' => Institution::class,
            'laboratories' => Laboratory::class,
            default => throw new \Exception("Invalid model: {$this->model}"),
        };
    }

    private function getPrimaryKey()
    {
        return match ($this->model) {
            'countries' => 'code',
            default => 'id',
        };
    }

    public function render()
    {
        $results = collect([]);

        if ($this->search && !$this->selectedId) {
            $query = $this->getModelClass()::query()
                ->where('name', 'like', '%' . $this->search . '%');

            // If this is an institution search and we have a state_id filter
            if ($this->model === 'institutions' && $this->stateId) {
                $query->where('state_id', $this->stateId);
            }

            $results = $query->limit(10)->get();
        }

        return view('livewire.admin.admin-search-select', [
            'results' => $results
        ]);
    }
}
