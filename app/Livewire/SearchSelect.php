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

    // Flag adicional para controle de filtro
    public $filterActive = true;

    // Livewire lifecycle hooks
    protected $listeners = ['dependencyChanged'];

    public function mount($model, $field, $dependsOn = [], $placeholder = '', $initialValue = null)
    {
        $this->model = $model;
        $this->field = $field;
        $this->dependsOn = $dependsOn;
        $this->placeholder = $placeholder;
        $this->selectedId = $initialValue;
        $this->initialValue = $initialValue;

        $this->search = $this->selectedId ? $this->getSelectedName() : '';

        // Log para debug
        $dependsOnStr = json_encode($dependsOn);
        \Log::info("SearchSelect mounted for field: {$field} with initialValue: " . ($initialValue ?? 'null') . " and dependencies: {$dependsOnStr}");
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

        // Dispatch event to parent component (sem restrição de destino)
        $this->dispatch('optionSelected', [
            'field' => $this->field,
            'value' => $idOrCode
        ]);
    }

    // Handle dependency changes from other components
    public function dependencyChanged($field, $value)
    {
        \Log::info("SearchSelect {$this->field} recebeu dependencyChanged para {$field} com valor {$value}");
        if (array_key_exists($field, $this->dependsOn)) {
            \Log::info("SearchSelect {$this->field} atualizando dependência {$field} para {$value}");
            $this->dependsOn[$field] = $value;
            $this->selectedId = null;
            $this->search = '';
            $this->filterActive = true;
            $this->dispatch('optionSelected', ['field' => $this->field, 'value' => null]);
        } else {
            \Log::warning("SearchSelect {$this->field} recebeu dependencyChanged para campo desconhecido: {$field}");
        }
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

        // Log importante para verificar as condições durante a renderização
        \Log::info("SearchSelect {$this->field} rendering with dependencies:", $this->dependsOn);

        // Apply any dependency filters (like filtering municipalities by state)
        if ($this->filterActive) {
            foreach ($this->dependsOn as $key => $value) {
                if ($value !== null && $value !== '') { // Garantir que o valor seja válido
                    $query->where($key, $value);
                    \Log::info("SearchSelect {$this->field} applying filter {$key} = {$value}");
                } else {
                    \Log::info("SearchSelect {$this->field} skipping filter for {$key} due to null/empty value");
                }
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

        // Debug: log the SQL query that was executed
        if ($this->search && !$this->selectedId) {
            $sql = $query->where('name', 'like', '%' . $this->search . '%')
                ->limit(10)
                ->toSql();
            \Log::info("SearchSelect {$this->field} executing SQL: {$sql}");
            \Log::info("With bindings: " . json_encode($query->getBindings()));
        }

        // Make sure selected item's text is always displayed
        if ($this->selectedId && empty($this->search)) {
            $this->search = $this->getSelectedName();
        }

        return view('livewire.search-select', [
            'results' => $results,
        ]);
    }
}
