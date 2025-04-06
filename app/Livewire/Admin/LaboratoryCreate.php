<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Laboratory;
use App\Models\Team;
use Illuminate\Support\Facades\DB;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Log;

class LaboratoryCreate extends Component
{
    use WithFileUploads;

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
    public $has_accessibility = false;
    public $componentId;

    protected $listeners = ['optionSelected', 'coordinatesUpdated'];

    public function mount()
    {
        $this->componentId = $this->id();
        $this->state_id = null;
        $this->institution_id = null;
        Log::info("Mounted LaboratoryCreate with componentId: {$this->componentId}");
    }

    public function optionSelected($data)
    {
        if (isset($data['field']) && property_exists($this, $data['field'])) {
            $this->{$data['field']} = $data['value'];
            Log::info("Option selected in LaboratoryCreate: {$data['field']} = " . ($data['value'] ?? 'null'));
            if ($data['field'] === 'state_id') {
                $this->updatedStateId();
            }
        }
    }

    public function updatedStateId()
    {
        $this->institution_id = null;
        $this->dispatch('dependencyChanged', 'state_id', $this->state_id)->to('admin.laboratory-search-select');
        Log::info("Updated state_id in LaboratoryCreate: {$this->state_id}, institution_id reset to null, dispatched dependencyChanged");
    }

    public function coordinatesUpdated($lat, $lng, $address)
    {
        $this->lat = $lat;
        $this->lng = $lng;
        $this->address = $address;
        Log::info("Coordinates updated in LaboratoryCreate: lat={$lat}, lng={$lng}, address={$address}");
    }

    public function store()
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
            $team = new Team();
            $team->name = $this->name;
            $team->personal_team = false;
            $team->user_id = auth()->id();
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

            $laboratory = new Laboratory();
            $laboratory->name = $this->name;
            $laboratory->institution_id = $this->institution_id;
            $laboratory->state_id = $this->state_id;
            $laboratory->team_id = $team->id;
            $laboratory->save();

            DB::commit();
            return redirect()->route('admin.laboratories.index')->with('success', 'Laboratório criado com sucesso');
        } catch (\Exception $e) {
            DB::rollback();
            $this->addError('general', 'Erro ao criar laboratório: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.admin.laboratory-create', [
            'componentId' => $this->componentId,
        ]);
    }
}