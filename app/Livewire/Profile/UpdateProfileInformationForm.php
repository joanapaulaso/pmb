<?php

namespace App\Livewire\Profile;

use Illuminate\Support\Facades\Auth;
use Laravel\Fortify\Contracts\UpdatesUserProfileInformation;
use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Profile;
use App\Models\Institution;
use App\Models\Laboratory;
use App\Models\UserCategory;
use Illuminate\Support\Facades\Log;

class UpdateProfileInformationForm extends Component
{
    use WithFileUploads;

    public $state = [];
    public $profileData = [];
    public $photo;
    public $verificationLinkSent = false;

    // Campos para categorias
    public $categories = [];
    public $selectedCategory = null;
    public $selectedSubcategories = [];
    public $subcategories = [];

    protected $listeners = [
        'optionSelected',
        'addSubcategory',
        'removeSubcategory',
    ];

    public function mount()
    {
        $this->state = Auth::user()->withoutRelations()->toArray();

        $profile = Profile::where('user_id', Auth::user()->id)->first();
        if ($profile) {
            $this->profileData = [
                'gender' => $profile->gender,
                'birth_date' => $profile->birth_date,
                'isInternational' => $profile->country_code && $profile->country_code !== 'BR',
                'country_code' => $profile->country_code,
                'state_id' => $profile->state_id,
                'municipality_id' => $profile->municipality_id,
                'institution_id' => $profile->institution_id,
                'laboratory_id' => $profile->laboratory_id,
                'lab_coordinator' => $profile->lab_coordinator,
                'institution_address' => $profile->institution ? $profile->institution->address : null,
                'showNewInstitution' => false,
                'new_institution' => '',
                'showNewLaboratory' => false,
                'new_laboratory' => '',
            ];
        } else {
            $this->profileData = [
                'gender' => null,
                'birth_date' => null,
                'isInternational' => false,
                'country_code' => 'BR',
                'state_id' => null,
                'municipality_id' => null,
                'institution_id' => null,
                'laboratory_id' => null,
                'lab_coordinator' => false,
                'institution_address' => null,
                'showNewInstitution' => false,
                'new_institution' => '',
                'showNewLaboratory' => false,
                'new_laboratory' => '',
            ];
        }

        $this->loadCategories();
        $this->loadUserCategories();
    }

    public function loadCategories()
    {
        try {
            $categoriesJson = file_get_contents(storage_path('app/data/categories.json'));
            $this->categories = json_decode($categoriesJson, true);

            if ($this->selectedCategory && isset($this->categories[$this->selectedCategory])) {
                $this->subcategories = $this->categories[$this->selectedCategory];
            }
        } catch (\Exception $e) {
            Log::error('Erro ao carregar categorias: ' . $e->getMessage());
            $this->categories = [];
        }
    }

    public function loadUserCategories()
    {
        $userCategories = UserCategory::where('user_id', Auth::user()->id)->get();
        $this->selectedSubcategories = $userCategories->map(function ($category) {
            return [
                'category' => $category->category_name,
                'subcategory' => $category->subcategory_name,
            ];
        })->toArray();
    }

    public function updatedSelectedCategory($value)
    {
        if ($value && isset($this->categories[$value])) {
            $this->subcategories = $this->categories[$value];
        } else {
            $this->subcategories = [];
        }
    }

    public function addSubcategory($subcategory)
    {
        if (count($this->selectedSubcategories) >= 3) {
            return;
        }

        if ($this->selectedCategory && $subcategory) {
            $categorySubcategory = [
                'category' => $this->selectedCategory,
                'subcategory' => $subcategory
            ];

            $exists = false;
            foreach ($this->selectedSubcategories as $item) {
                if ($item['category'] === $this->selectedCategory && $item['subcategory'] === $subcategory) {
                    $exists = true;
                    break;
                }
            }

            if (!$exists) {
                $this->selectedSubcategories[] = $categorySubcategory;
            }
        }
    }

    public function removeSubcategory($index)
    {
        if (isset($this->selectedSubcategories[$index])) {
            unset($this->selectedSubcategories[$index]);
            $this->selectedSubcategories = array_values($this->selectedSubcategories);
        }
    }

    public function updatedIsInternational($value)
    {
        if ($value) {
            $this->profileData['state_id'] = null;
            $this->profileData['municipality_id'] = null;
            $this->profileData['country_code'] = null;
        } else {
            $this->profileData['country_code'] = 'BR';
        }
        $this->dispatch('dependencyChanged', 'country_code', $this->profileData['country_code']);
    }

    public function updatedProfileDataStateId()
    {
        $this->profileData['municipality_id'] = null;
        $this->profileData['institution_id'] = null;
        $this->profileData['laboratory_id'] = null;
        $this->dispatch('dependencyChanged', 'state_id', $this->profileData['state_id']);
    }

    public function updatedProfileDataInstitutionId()
    {
        $this->profileData['laboratory_id'] = null;
        $this->dispatch('dependencyChanged', 'institution_id', $this->profileData['institution_id']);
    }

    public function updatedProfileDataShowNewInstitution($value)
    {
        if ($value) {
            $this->profileData['institution_id'] = null;
            $this->profileData['laboratory_id'] = null;
            $this->profileData['showNewLaboratory'] = true;
        } else {
            $this->profileData['new_institution'] = '';
            $this->profileData['new_laboratory'] = '';
            $this->profileData['showNewLaboratory'] = false;
        }
        $this->dispatch('dependencyChanged', 'institution_id', $this->profileData['institution_id']);
    }

    public function optionSelected($data)
    {
        if (isset($data['field']) && array_key_exists($data['field'], $this->profileData)) {
            $this->profileData[$data['field']] = $data['value'];

            if ($data['field'] === 'state_id') {
                $this->profileData['municipality_id'] = null;
                $this->profileData['institution_id'] = null;
                $this->profileData['laboratory_id'] = null;
                $this->dispatch('dependencyChanged', 'state_id', $this->profileData['state_id']);
            } else if ($data['field'] === 'country_code') {
                $this->profileData['state_id'] = null;
                $this->profileData['municipality_id'] = null;
                $this->profileData['institution_id'] = null;
                $this->profileData['laboratory_id'] = null;
                $this->dispatch('dependencyChanged', 'country_code', $this->profileData['country_code']);
            } else if ($data['field'] === 'institution_id') {
                $this->profileData['laboratory_id'] = null;
                $this->dispatch('dependencyChanged', 'institution_id', $this->profileData['institution_id']);
            }
        }
    }

    public function updateProfileInformation(UpdatesUserProfileInformation $updater)
    {
        $this->resetErrorBag();

        $updater->update(
            Auth::user(),
            $this->photo ? array_merge($this->state, ['photo' => $this->photo]) : $this->state
        );

        $profile = Profile::where('user_id', Auth::user()->id)->first() ?? new Profile(['user_id' => Auth::user()->id]);

        // Atualizar ou criar instituição
        if ($this->profileData['showNewInstitution'] && !empty($this->profileData['new_institution'])) {
            $institution = Institution::create([
                'name' => $this->profileData['new_institution'],
                'state_id' => $this->profileData['isInternational'] ? null : $this->profileData['state_id'],
                'municipality_id' => $this->profileData['isInternational'] ? null : $this->profileData['municipality_id'],
                'country_code' => $this->profileData['country_code'] ?? 'BR',
                'address' => $this->profileData['institution_address'] ?? null,
            ]);
            $this->profileData['institution_id'] = $institution->id;
        } elseif ($this->profileData['institution_id'] && !empty($this->profileData['institution_address'])) {
            $institution = Institution::find($this->profileData['institution_id']);
            if ($institution && !$institution->address) {
                $institution->update(['address' => $this->profileData['institution_address']]);
            }
        }

        // Atualizar ou criar laboratório
        if (($this->profileData['showNewInstitution'] || $this->profileData['showNewLaboratory']) && !empty($this->profileData['new_laboratory'])) {
            $laboratory = Laboratory::create([
                'name' => $this->profileData['new_laboratory'],
                'institution_id' => $this->profileData['institution_id'],
                'state_id' => $this->profileData['isInternational'] ? null : $this->profileData['state_id'],
                'team_id' => $profile->laboratory ? ($profile->laboratory->team_id ?? null) : null,
            ]);
            $this->profileData['laboratory_id'] = $laboratory->id;
        }

        // Atualizar perfil
        $profile->gender = $this->profileData['gender'] ?? null;
        $profile->birth_date = $this->profileData['birth_date'] ?? null;
        $profile->country_code = $this->profileData['country_code'] ?? 'BR';
        $profile->state_id = $this->profileData['isInternational'] ? null : $this->profileData['state_id'];
        $profile->municipality_id = $this->profileData['isInternational'] ? null : $this->profileData['municipality_id'];
        $profile->institution_id = $this->profileData['institution_id'] ?? null;
        $profile->laboratory_id = $this->profileData['laboratory_id'] ?? null;
        $profile->lab_coordinator = $this->profileData['lab_coordinator'] ?? false;
        $profile->save();

        // Atualizar categorias de interesse
        UserCategory::where('user_id', Auth::user()->id)->delete();
        foreach ($this->selectedSubcategories as $selection) {
            if (isset($selection['category']) && isset($selection['subcategory'])) {
                UserCategory::create([
                    'user_id' => Auth::user()->id,
                    'category_name' => $selection['category'],
                    'subcategory_name' => $selection['subcategory'],
                ]);
            }
        }

        if (isset($this->photo)) {
            return redirect()->route('profile.show');
        }

        $this->dispatch('saved');
    }

    public function deleteProfilePhoto()
    {
        Auth::user()->deleteProfilePhoto();
        $this->dispatch('refresh-navigation-menu');
    }

    public function sendEmailVerification()
    {
        Auth::user()->sendEmailVerificationNotification();
        $this->verificationLinkSent = true;
    }

    public function getUserProperty()
    {
        return Auth::user();
    }

    public function render()
    {
        Log::info('Rendering view: livewire.profile.update-profile-information-form');
        return view('livewire.profile.update-profile-information-form');
    }
}
