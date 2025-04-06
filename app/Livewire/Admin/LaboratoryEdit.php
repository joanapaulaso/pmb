<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Laboratory;
use App\Models\Team;
use Illuminate\Support\Facades\DB;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Log;

class LaboratoryEdit extends Component
{
    use WithFileUploads;

    public $laboratory;
    public $name;
    public $state_id;
    public $institution_id;
    public $description;
    public $website;
    public $address;
    public $lat;
    public $lng;
    public $logo;
    public $building;
    public $floor;
    public $room;
    public $department;
    public $campus;
    public $phone;
    public $contact_email;
    public $working_hours;
    public $has_accessibility;
    public $componentId;

    protected $listeners = ['optionSelected', 'coordinatesUpdated'];

    public function mount(Laboratory $laboratory)
    {
        $this->laboratory = $laboratory->load('team');
        $this->componentId = $this->id();
        $this->name = $laboratory->name;
        $this->state_id = $laboratory->state_id;
        $this->institution_id = $laboratory->institution_id;
        $this->description = $laboratory->team->description ?? $laboratory->description;
        $this->website = $laboratory->team->website ?? $laboratory->website;
        $this->address = $laboratory->team->address ?? $laboratory->address;
        $this->lat = $laboratory->team->latitude ?? $laboratory->lat;
        $this->lng = $laboratory->team->longitude ?? $laboratory->lng;
        $this->building = $laboratory->team->building ?? $laboratory->building;
        $this->floor = $laboratory->team->floor ?? $laboratory->floor;
        $this->room = $laboratory->team->room ?? $laboratory->room;
        $this->department = $laboratory->team->department ?? $laboratory->department;
        $this->campus = $laboratory->team->campus ?? $laboratory->campus;
        $this->phone = $laboratory->team->phone ?? $laboratory->phone;
        $this->contact_email = $laboratory->team->contact_email ?? $laboratory->contact_email;
        $this->working_hours = $laboratory->team->working_hours ?? $laboratory->working_hours;
        $this->has_accessibility = $laboratory->team->has_accessibility ?? $laboratory->has_accessibility;
        Log::info("Mounted LaboratoryEdit with componentId: {$this->componentId}, state_id: {$this->state_id}, institution_id: {$this->institution_id}");
    }

    public function optionSelected($data)
    {
        if (isset($data['field']) && property_exists($this, $data['field'])) {
            $this->{$data['field']} = $data['value'];
            Log::info("Option selected in LaboratoryEdit: {$data['field']} = " . ($data['value'] ?? 'null'));
            if ($data['field'] === 'state_id') {
                $this->updatedStateId();
            }
        }
    }

    public function updatedStateId()
    {
        $this->institution_id = null;
        $this->dispatch('dependencyChanged', 'state_id', $this->state_id)->to('admin.laboratory-search-select');
        Log::info("Updated state_id in LaboratoryEdit: {$this->state_id}, institution_id reset to null, dispatched dependencyChanged");
    }

    public function coordinatesUpdated($lat, $lng, $address)
    {
        $this->lat = $lat;
        $this->lng = $lng;
        $this->address = $address;
        Log::info("Coordinates updated in LaboratoryEdit: lat={$lat}, lng={$lng}, address={$address}");
    }

    public function update()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'institution_id' => 'required|exists:institutions,id',
            'state_id' => 'required|exists:states,id',
            'description' => 'nullable|string',
            'website' => 'nullable|url',
            'address' => 'nullable|string',
            'lat' => 'nullable|numeric',
            'lng' => 'nullable|numeric',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'building' => 'nullable|string',
            'floor' => 'nullable|string',
            'room' => 'nullable|string',
            'department' => 'nullable|string',
            'campus' => 'nullable|string',
            'phone' => 'nullable|string',
            'contact_email' => 'nullable|email',
            'working_hours' => 'nullable|string',
            'has_accessibility' => 'boolean',
        ]);

        DB::beginTransaction();
        try {
            $this->laboratory->name = $this->name;
            $this->laboratory->institution_id = $this->institution_id;
            $this->laboratory->state_id = $this->state_id;
            $this->laboratory->save();

            if ($this->laboratory->team) {
                $team = $this->laboratory->team;
                $team->name = $this->name;
                $team->description = $this->description;
                $team->website = $this->website;
                $team->address = $this->address;
                $team->latitude = $this->lat;
                $team->longitude = $this->lng;
                $team->building = $this->building;
                $team->floor = $this->floor;
                $team->room = $this->room;
                $team->department = $this->department;
                $team->campus = $this->campus;
                $team->phone = $this->phone;
                $team->contact_email = $this->contact_email;
                $team->working_hours = $this->working_hours;
                $team->has_accessibility = $this->has_accessibility;

                if ($this->logo) {
                    $path = $this->logo->store('laboratories', 'public');
                    $team->logo = $path;
                }

                $team->save();
            }

            DB::commit();
            return redirect()->route('admin.laboratories.index')->with('success', 'Laboratório atualizado com sucesso');
        } catch (\Exception $e) {
            DB::rollback();
            $this->addError('general', 'Erro ao atualizar laboratório: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.admin.laboratory-edit', [
            'componentId' => $this->componentId,
        ]);
    }
}