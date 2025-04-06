<?php

namespace App\Livewire;

use Livewire\Component;
use App\Actions\Fortify\CreateNewUser;
use App\Actions\Jetstream\CreateTeam;
use App\Actions\Jetstream\AddTeamMember;
use Illuminate\Support\Facades\Auth;
use App\Models\Country;
use App\Models\State;
use App\Models\Municipality;
use App\Models\Institution;
use App\Models\Laboratory;
use App\Models\Team;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class RegisterComponent extends Component
{
    public $isInternational = false;
    public $full_name, $email, $password, $password_confirmation, $birth_date;
    public $country_code = null;
    public $state_id = null;
    public $municipality_id = null;
    public $institution_id = null;
    public $institution_address = '';
    public $laboratory_id = null;
    public $new_institution = '';
    public $new_laboratory = '';
    public $showNewInstitution = false;
    public $showNewLaboratory = false;
    public $lab_coordinator = false;
    public $gender = '';

    public $categories = [];
    public $selectedCategory = null;
    public $selectedSubcategories = [];
    public $subcategories = [];

    protected $listeners = [
        'optionSelected',
        'addSubcategory',
        'removeSubcategory',
        'setInstitutionAddress' => 'setInstitutionAddress',
    ];

    protected function rules()
    {
        $rules = [
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
            'birth_date' => 'nullable|date',
            'country_code' => 'required|string|max:2|exists:countries,code',
            'gender' => 'required|string',
            'institution_address' => 'nullable|string|max:255',
        ];

        if (!$this->isInternational) {
            $rules['state_id'] = 'required|exists:states,id';
            $rules['municipality_id'] = 'required|exists:municipalities,id';
        }

        if ($this->showNewInstitution) {
            $rules['new_institution'] = 'required|string|max:255';
            $rules['new_laboratory'] = $this->lab_coordinator ? 'required|string|max:255' : 'nullable|string|max:255';
        } else {
            $rules['institution_id'] = 'required|exists:institutions,id';
            if ($this->showNewLaboratory) {
                $rules['new_laboratory'] = $this->lab_coordinator ? 'required|string|max:255' : 'nullable|string|max:255';
            } else {
                $rules['laboratory_id'] = $this->lab_coordinator ? 'required|exists:laboratories,id' : 'nullable|exists:laboratories,id';
            }
        }

        return $rules;
    }

    protected $messages = [
        'full_name.required' => 'O nome completo é obrigatório.',
        'email.required' => 'O email é obrigatório.',
        'email.email' => 'Digite um email válido.',
        'email.unique' => 'Este email já está em uso.',
        'password.required' => 'A senha é obrigatória.',
        'password.min' => 'A senha deve ter pelo menos 8 caracteres.',
        'password.confirmed' => 'As senhas não coincidem.',
        'birth_date.date' => 'Digite uma data válida.',
        'country_code.required' => 'O país é obrigatório.',
        'state_id.required' => 'O estado é obrigatório.',
        'municipality_id.required' => 'O município é obrigatório.',
        'institution_id.required' => 'A instituição é obrigatória.',
        'new_institution.required' => 'O nome da nova instituição é obrigatório.',
        'laboratory_id.required' => 'O laboratório é obrigatório.',
        'new_laboratory.required' => 'O nome do novo laboratório é obrigatório.',
        'gender.required' => 'O gênero é obrigatório.',
    ];

    public function mount()
    {
        if (Auth::check()) {
            return redirect()->route('portal');
        }

        if (!$this->isInternational) {
            $this->country_code = 'BR';
        }

        $this->loadCategories();
    }

    public function setInstitutionAddress($data)
    {
        $this->institution_address = $data['address'] ?? '';
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
            $this->state_id = null;
            $this->municipality_id = null;
            $this->country_code = null;
        } else {
            $this->country_code = 'BR';
        }

        $this->institution_id = null;
        $this->laboratory_id = null;
        $this->dispatch('dependencyChanged', 'country_code', $this->country_code);
    }

    public function updatedStateId()
    {
        $this->municipality_id = null;
        $this->institution_id = null;
        $this->laboratory_id = null;
        $this->dispatch('dependencyChanged', 'state_id', $this->state_id);
    }

    public function updatedInstitutionId($value)
    {
        $this->laboratory_id = null;
        $this->institution_address = '';
        $this->dispatch('dependencyChanged');
    }

    public function updatedShowNewInstitution($value)
    {
        if ($value) {
            $this->institution_id = null;
            $this->laboratory_id = null;
            $this->showNewLaboratory = true;
        } else {
            $this->new_institution = '';
            $this->new_laboratory = '';
            $this->showNewLaboratory = false;
        }
        $this->dispatch('dependencyChanged', 'institution_id', $this->institution_id);
    }

    public function optionSelected($data)
    {
        \Log::info("Evento optionSelected recebido no RegisterComponent: " . json_encode($data));
        if (isset($data['field']) && property_exists($this, $data['field'])) {
            $this->{$data['field']} = $data['value'];

            if ($data['field'] === 'state_id') {
                $this->municipality_id = null;
                $this->institution_id = null;
                $this->laboratory_id = null;
                $this->dispatch('dependencyChanged', 'state_id', $this->state_id);
            } else if ($data['field'] === 'country_code') {
                $this->state_id = null;
                $this->municipality_id = null;
                $this->institution_id = null;
                $this->laboratory_id = null;
                $this->dispatch('dependencyChanged', 'country_code', $this->country_code);
            } else if ($data['field'] === 'institution_id') {
                $this->laboratory_id = null;
                $this->institution_address = '';
                $this->dispatch('dependencyChanged', 'institution_id', $this->institution_id);
            }
        }
    }

    public function testAction()
    {
        \Log::info("Teste de ação do Livewire disparado!");
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function submit()
    {
        $this->validate();

        try {
            $data = [
                'full_name' => $this->full_name,
                'email' => $this->email,
                'password' => $this->password,
                'password_confirmation' => $this->password_confirmation,
                'birth_date' => $this->birth_date,
                'country_code' => $this->country_code,
                'state_id' => $this->isInternational ? null : $this->state_id,
                'municipality_id' => $this->isInternational ? null : $this->municipality_id,
                'institution_id' => $this->institution_id,
                'new_institution' => $this->new_institution,
                'laboratory_id' => $this->laboratory_id,
                'new_laboratory' => $this->new_laboratory,
                'lab_coordinator' => $this->lab_coordinator,
                'isInternational' => $this->isInternational,
                'gender' => $this->gender,
                'institution_address' => $this->institution_address,
            ];

            if (!empty($this->selectedSubcategories)) {
                $data['selected_subcategories'] = $this->selectedSubcategories;
            }

            Log::info('Dados enviados para CreateNewUser:', $data);

            $createNewUser = new CreateNewUser();
            $user = $createNewUser->create($data);

            if (!$user || !$user->id) {
                throw new \Exception('Falha ao criar o usuário: objeto inválido retornado.');
            }

            Log::info('Usuário criado com sucesso:', ['user_id' => $user->id, 'user_data' => $user->toArray()]);

            // Move team creation after user creation but before login
            $laboratory = Laboratory::find($user->profile->laboratory_id);
            $laboratoryName = $laboratory ? $laboratory->name : ($this->new_laboratory ?: null);

            if ($laboratoryName && !$this->lab_coordinator) {
                $createTeam = new CreateTeam();
                $teamInput = [
                    'name' => $laboratoryName,
                    'address' => $this->institution_address,
                ];
                $team = $createTeam->create($user, $teamInput);

                if ($team->personal_team) {
                    $team->update(['personal_team' => false]);
                }
                Log::info('Team criado/atualizado com o nome do laboratório usando Jetstream:', [
                    'team_id' => $team->id,
                    'name' => $team->name,
                    'personal_team' => $team->personal_team,
                    'user_id' => $team->user_id,
                ]);

                if ($laboratory && !$laboratory->team_id) {
                    $laboratory->team_id = $team->id;
                    $laboratory->save();
                    Log::info('Laboratório associado ao Team:', ['laboratory_id' => $laboratory->id, 'team_id' => $team->id]);
                }

                $user->teams()->attach($team->id, ['created_at' => now(), 'updated_at' => now()]);
                Log::info("Usuário {$user->id} associado ao time {$team->id} na tabela team_user");

                $role = 'editor';
                $addTeamMember = new AddTeamMember();
                try {
                    $addTeamMember->add($user, $team, $user->email, $role);
                    Log::info('Usuário vinculado ao Team com papel:', ['user_id' => $user->id, 'team_id' => $team->id, 'role' => $role]);
                } catch (\Exception $e) {
                    Log::error('Erro ao vincular usuário ao Team:', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
                }
            } elseif ($this->lab_coordinator) {
                Log::info('Aguardando aprovação para coordenador; team não criado ainda.', ['user_id' => $user->id]);
            }

            Auth::login($user);
            Log::info('Login realizado com sucesso para o usuário:', ['user_id' => $user->id]);

            if (!$user->hasVerifiedEmail()) {
                Log::info('User has not verified email, attempting to send verification email', ['user_id' => $user->id, 'email' => $user->email]);
                try {
                    $user->sendEmailVerificationNotification();
                    Log::info('Email de verificação enviado para o usuário:', ['user_id' => $user->id, 'email' => $user->email]);
                } catch (\Exception $e) {
                    Log::error('Erro ao enviar email de verificação:', [
                        'user_id' => $user->id,
                        'email' => $user->email,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                }
                return redirect()->route('verification.notice');
            }

            return redirect()->route('portal')->with('message', 'Conta criada e login realizado com sucesso!');
        } catch (\Exception $e) {
            \Log::error('Erro ao registrar usuário: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            session()->flash('error', 'Ocorreu um erro ao criar sua conta. Por favor, tente novamente.');
            return null;
        }
    }

    public function debugState()
    {
        Log::info('Estado atual do RegisterComponent:', $this->all());
    }

    public function render()
    {
        return view('livewire.register-component')
            ->extends('layouts.guest')
            ->section('content');
    }
}
