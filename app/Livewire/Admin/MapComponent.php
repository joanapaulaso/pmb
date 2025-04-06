<?php

namespace App\Livewire\Admin;

use Livewire\Component;

class MapComponent extends Component
{
    public $lat;
    public $lng;
    public $address;
    public $componentId;

    public function mount($lat = null, $lng = null, $address = null)
    {
        $this->componentId = $this->id();
        $this->lat = $lat ?? -16.3141633;
        $this->lng = $lng ?? -52.6125466;
        $this->address = $address;
    }

    public function updateCoordinates($lat, $lng, $address)
    {
        $this->lat = $lat;
        $this->lng = $lng;
        $this->address = $address;
        $this->dispatch('coordinatesUpdated', $this->lat, $this->lng, $this->address)->to('admin.laboratory-edit');
        $this->dispatch('coordinatesUpdated', $this->lat, $this->lng, $this->address)->to('admin.laboratory-create');
    }

    public function render()
    {
        return view('livewire.admin.map-component', [
            'componentId' => $this->componentId,
        ]);
    }
}