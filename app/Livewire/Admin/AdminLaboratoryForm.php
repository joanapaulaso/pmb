<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Laboratory;
use App\Models\State;
use App\Models\Institution;
use Livewire\Attributes\On;

class AdminLaboratoryForm extends Component
{
    public $laboratory;
    public $isEdit = false;

    // Campos do formulário
    public $name = '';
    public $state_id = null;
    public $institution_id = null;
    public $description = '';
    public $website = '';
    public $address = '';
    public $lat = null;
    public $lng = null;
    public $existingLogo = null;

    protected $listeners = ['dependencyChanged'];

    public function mount($laboratory = null)
    {
        if ($laboratory) {
            $this->laboratory = $laboratory;
            $this->isEdit = true;

            // Preencher os campos com os dados do laboratório
            $this->name = $laboratory->name;
            $this->state_id = $laboratory->state_id;
            $this->institution_id = $laboratory->institution_id;
            $this->description = $laboratory->description;
            $this->website = $laboratory->website;
            $this->address = $laboratory->address;
            $this->lat = $laboratory->lat;
            $this->lng = $laboratory->lng;
            $this->existingLogo = $laboratory->logo;
        }

        \Log::info("AdminLaboratoryForm mounted", [
            'isEdit' => $this->isEdit,
            'state_id' => $this->state_id,
            'institution_id' => $this->institution_id
        ]);
    }

    #[On('optionSelected')]
    public function handleOptionSelected($data)
    {
        \Log::info("AdminLaboratoryForm received optionSelected", $data);

        if ($data['field'] === 'state_id') {
            $oldStateId = $this->state_id;
            $this->state_id = $data['value'];

            // Resetar instituição e notificar mudança
            if ($this->state_id !== $oldStateId) {
                $this->institution_id = null;
                \Log::info("AdminLaboratoryForm dispatching dependencyChanged for state_id: {$this->state_id}");
                $this->dispatch('dependencyChanged', 'state_id', $this->state_id);
            }
        } elseif ($data['field'] === 'institution_id') {
            $this->institution_id = $data['value'];
            \Log::info("Institution ID updated to: {$this->institution_id}");
        }
    }

    // Método para reagir a mudanças no state_id
    public function updatedStateId($value)
    {
        if ($value !== null) {
            \Log::info("State ID updated to: {$value}");
            $this->institution_id = null; // Resetar instituição
            $this->dispatch('dependencyChanged', 'state_id', $value);
        }
    }

    public function dependencyChanged($field, $value)
    {
        \Log::info("AdminLaboratoryForm received dependencyChanged: field={$field}, value={$value}");
    }

    public function getFormAction()
    {
        return $this->isEdit
            ? route('admin.laboratories.update', $this->laboratory)
            : route('admin.laboratories.store');
    }

    public function render()
    {
        \Log::info("AdminLaboratoryForm rendering", [
            'isEdit' => $this->isEdit,
            'state_id' => $this->state_id,
            'institution_id' => $this->institution_id
        ]);

        return view('livewire.admin.admin-laboratory-form', [
            'currentStateId' => $this->state_id
        ]);
    }
}
