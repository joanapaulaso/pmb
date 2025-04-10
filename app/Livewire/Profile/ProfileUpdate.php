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
use App\Models\PendingLabCoordinator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;

class ProfileUpdate extends Component
{
    use WithFileUploads;

    public $state = [];
    public $profileData = [];
    public $photo;
    public $verificationLinkSent = false;
    public $pendingLabCoordinatorApproval = false; // Track if a lab coordinator request is pending

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

    public $originalLabCoordinator; // Store the original lab_coordinator value

    public function mount()
    {
        $user = Auth::user();
        $this->state = [
            'name' => $user->name,
            'email' => $user->email,
        ];

        $profile = Profile::where('user_id', $user->id)->first();
        if ($profile) {
            $this->profileData = [
                'gender' => $profile->gender,
                'birth_date' => $profile->birth_date ? $profile->birth_date->format('Y-m-d') : null,
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
            $this->originalLabCoordinator = $profile->lab_coordinator; // Store the original value
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
            $this->originalLabCoordinator = false;
        }

        // Check for pending lab coordinator request
        $pendingRequest = PendingLabCoordinator::where('user_id', $user->id)
            ->where('approved', false)
            ->where('expires_at', '>', now())
            ->first();
        $this->pendingLabCoordinatorApproval = $pendingRequest !== null;

        $this->loadCategories();
        $this->loadUserCategories();
    }

    public function updatedProfileDataLabCoordinator($value)
    {
        // If lab_coordinator changes from false to true, send authorization email
        if (!$this->originalLabCoordinator && $value && !$this->pendingLabCoordinatorApproval) {
            if (!$this->profileData['laboratory_id'] && !$this->profileData['new_laboratory']) {
                $this->addError('profileData.laboratory_id', 'Por favor, selecione ou crie um laboratório antes de marcar como coordenador.');
                $this->profileData['lab_coordinator'] = false;
                return;
            }

            $laboratoryId = $this->profileData['laboratory_id'];
            if (!$laboratoryId && $this->profileData['new_laboratory']) {
                // Create the laboratory if new_laboratory is provided
                $laboratory = Laboratory::create([
                    'name' => $this->profileData['new_laboratory'],
                    'institution_id' => $this->profileData['institution_id'],
                    'state_id' => $this->profileData['isInternational'] ? null : $this->profileData['state_id'],
                ]);
                $laboratoryId = $laboratory->id;
                $this->profileData['laboratory_id'] = $laboratoryId;
            }

            $token = Str::random(60);
            PendingLabCoordinator::create([
                'user_id' => Auth::user()->id,
                'laboratory_id' => $laboratoryId,
                'token' => $token,
                'expires_at' => now()->addDays(7),
            ]);

            $this->sendLabCoordinatorApprovalEmail($laboratoryId, $token);
            $this->pendingLabCoordinatorApproval = true;
            $this->profileData['lab_coordinator'] = false; // Revert until approved
        }
    }

    private function sendLabCoordinatorApprovalEmail($laboratoryId, $token)
    {
        $user = Auth::user();
        $laboratory = Laboratory::find($laboratoryId);
        $approvalUrl = route('lab-coordinator.approve', ['token' => $token]);
        $rejectionUrl = route('lab-coordinator.reject', ['token' => $token]);

        Mail::raw(
            "Um usuário solicitou ser coordenador do laboratório {$laboratory->name}.\n" .
            "Nome: {$user->name}\n" .
            "Email: {$user->email}\n" .
            "Aprovar: {$approvalUrl}\n" .
            "Rejeitar: {$rejectionUrl}\n" .
            "Este link expira em 7 dias.",
            function ($message) use ($user) {
                $message->to('contato@portalmetabolomicabrasil.com.br')
                        ->subject("Solicitação de Coordenador de Laboratório: {$user->name}")
                        ->from(config('mail.from.address'), config('mail.from.name'));
            }
        );

        Log::info("Email de aprovação de coordenador enviado para contato@portalmetabolomicabrasil.com.br", [
            'user_id' => $user->id,
            'laboratory_id' => $laboratoryId,
            'token' => $token,
        ]);
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
        Log::info("Loaded user categories for user " . Auth::user()->id . ": " . json_encode($this->selectedSubcategories));
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

    public function updatedProfileDataIsInternational($value)
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
        Log::info("ProfileUpdate received optionSelected: " . json_encode($data));
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

        if ($this->profileData['showNewInstitution'] && !empty($this->profileData['new_institution'])) {
            $institution = Institution::create([
                'name' => $this->profileData['new_institution'],
                'state_id' => $this->profileData['isInternational'] ? null : $this->profileData['state_id'],
                'municipality_id' => $this->profileData['isInternational'] ? null : $this->profileData['municipality_id'],
                'country_code' => $this->profileData['isInternational'] ? ($this->profileData['country_code'] ?? 'BR') : 'BR',
                'address' => $this->profileData['institution_address'] ?? null,
            ]);
            $this->profileData['institution_id'] = $institution->id;
        } elseif ($this->profileData['institution_id'] && !empty($this->profileData['institution_address'])) {
            $institution = Institution::find($this->profileData['institution_id']);
            if ($institution && !$institution->address) {
                $institution->update(['address' => $this->profileData['institution_address']]);
            }
        }

        if (($this->profileData['showNewInstitution'] || $this->profileData['showNewLaboratory']) && !empty($this->profileData['new_laboratory'])) {
            $laboratory = Laboratory::create([
                'name' => $this->profileData['new_laboratory'],
                'institution_id' => $this->profileData['institution_id'],
                'state_id' => $this->profileData['isInternational'] ? null : $this->profileData['state_id'],
                'team_id' => $profile->laboratory ? ($profile->laboratory->team_id ?? null) : null,
            ]);
            $this->profileData['laboratory_id'] = $laboratory->id;
        }

        $profile->gender = $this->profileData['gender'] ?? null;
        $profile->birth_date = $this->profileData['birth_date'] ?? null;
        $profile->country_code = $this->profileData['isInternational'] ? ($this->profileData['country_code'] ?? 'BR') : 'BR';
        $profile->state_id = $this->profileData['isInternational'] ? null : $this->profileData['state_id'];
        $profile->municipality_id = $this->profileData['isInternational'] ? null : $this->profileData['municipality_id'];
        $profile->institution_id = $this->profileData['institution_id'] ?? null;
        $profile->laboratory_id = $this->profileData['laboratory_id'] ?? null;
        // lab_coordinator is updated via PendingLabCoordinators, so don't set it here
        $profile->save();

        Log::info("Salvando categorias para o usuário " . Auth::user()->id . ": " . json_encode($this->selectedSubcategories));
        UserCategory::where('user_id', Auth::user()->id)->delete();
        foreach ($this->selectedSubcategories as $selection) {
            if (isset($selection['category']) && isset($selection['subcategory'])) {
                $categoryName = $selection['category'];
                $subcategoryName = $selection['subcategory'];

                $category = \App\Models\Category::firstOrCreate(
                    ['name' => $categoryName, 'type' => 'category'],
                    ['name' => $categoryName, 'type' => 'category']
                );

                $subcategory = \App\Models\Category::firstOrCreate(
                    ['name' => $subcategoryName, 'type' => 'subcategory', 'parent_id' => $category->id],
                    ['name' => $subcategoryName, 'type' => 'subcategory', 'parent_id' => $category->id]
                );

                UserCategory::create([
                    'user_id' => Auth::user()->id,
                    'category_id' => $subcategory->id,
                    'category_name' => $categoryName,
                    'subcategory_name' => $subcategoryName,
                ]);
                Log::info("Categoria salva: user_id=" . Auth::user()->id . ", category_id={$subcategory->id}, category_name={$categoryName}, subcategory_name={$subcategoryName}");
            } else {
                Log::warning("Seleção de categoria inválida ao salvar para o usuário " . Auth::user()->id . ": " . json_encode($selection));
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
        return view('livewire.profile.profile-update');
    }
}