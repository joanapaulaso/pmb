<?php

namespace App\Livewire\Profile;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\Profile;
use App\Models\Institution;
use App\Models\Laboratory;
use App\Models\Country;
use App\Models\State;
use App\Models\Municipality;
use App\Models\UserCategory;

class UserProfileDetails extends Component
{
    public $user;
    public $profile;

    protected $listeners = ['saved' => 'refreshProfile'];

    public function mount()
    {
        $this->user = Auth::user();
        $this->profile = Profile::where('user_id', $this->user->id)->first() ?? new Profile();
    }

    public function render()
    {
        $userCategories = UserCategory::where('user_id', $this->user->id)->get();

        // Buscar informações relacionadas
        $country = $this->profile->country_code ? Country::where('code', $this->profile->country_code)->first() : null;
        $state = $this->profile->state_id ? State::find($this->profile->state_id) : null;
        $municipality = $this->profile->municipality_id ? Municipality::find($this->profile->municipality_id) : null;
        $institution = $this->profile->institution_id ? Institution::find($this->profile->institution_id) : null;
        $laboratory = $this->profile->laboratory_id ? Laboratory::find($this->profile->laboratory_id) : null;

        return view('livewire.profile.user-profile-details', [
            'country' => $country,
            'state' => $state,
            'municipality' => $municipality,
            'institution' => $institution,
            'laboratory' => $laboratory,
            'userCategories' => $userCategories
        ]);
    }
}
