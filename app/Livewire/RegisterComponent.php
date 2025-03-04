<?php

namespace App\Livewire;

use Livewire\Component;
use App\Actions\Fortify\CreateNewUser;
use App\Actions\Jetstream\CreateTeam;
use Illuminate\Support\Facades\Auth;
use App\Models\Country;
use App\Models\State;
use App\Models\Municipality;
use App\Models\Institution;
use App\Models\Laboratory;
use App\Models\Team; // Adicionando o modelo Team
use Illuminate\Support\Facades\Log;

class RegisterComponent extends Component
{
    public $isInternational = false;
    public $full_name, $email, $password, $password_confirmation, $birth_date;
    public $country_code = null;
    public $state_id = null;
    public $municipality_id = null;
    public $institution_id = null;
    public $laboratory_id = null;
    public $new_institution = '';
    public $new_laboratory = '';
    public $showNewInstitution = false;
    public $showNewLaboratory = false;
    public $lab_coordinator = false;
    public $gender = ''; // Nova propriedade para gênero

    // Adicionando variáveis para as categorias
    public $categories = [];
    public $selectedCategory = null;
    public $selectedSubcategories = [];
    public $subcategories = [];

    protected $listeners = ['optionSelected', 'addSubcategory', 'removeSubcategory'];

    public function mount()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        // Set default country to Brazil if not international
        if (!$this->isInternational) {
            $this->country_code = 'BR';
        }

        // Carregar categorias do arquivo JSON
        $this->loadCategories();
    }

    public function loadCategories()
    {
        try {
            $categoriesJson = file_get_contents(storage_path('app/data/categories.json'));
            $this->categories = json_decode($categoriesJson, true);

            // Inicializar subcategorias se uma categoria estiver selecionada
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
        // Limitar a 3 subcategorias
        if (count($this->selectedSubcategories) >= 3) {
            return;
        }

        // Verificar se a categoria e subcategoria estão selecionadas
        if ($this->selectedCategory && $subcategory) {
            $categorySubcategory = [
                'category' => $this->selectedCategory,
                'subcategory' => $subcategory
            ];

            // Verificar se a combinação já existe
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
            // User is not from Brazil
            $this->state_id = null;
            $this->municipality_id = null;
            $this->country_code = null;
        } else {
            // User is from Brazil
            $this->country_code = 'BR';
        }

        // Reset dependent fields
        $this->institution_id = null;
        $this->laboratory_id = null;

        // Broadcast changes to dependent components
        $this->dispatch('dependencyChanged');
    }

    public function updatedStateId()
    {
        // Reset dependent fields when state changes
        $this->municipality_id = null;
        $this->institution_id = null;
        $this->laboratory_id = null;

        // Broadcast changes to dependent components
        $this->dispatch('dependencyChanged');
    }

    public function updatedInstitutionId()
    {
        // Reset dependent fields when institution changes
        $this->laboratory_id = null;

        // Broadcast changes to dependent components
        $this->dispatch('dependencyChanged');
    }

    public function optionSelected($data)
    {
        \Log::info("Option selected in parent: " . json_encode($data));

        // Only update if the field belongs to this component
        if (isset($data['field']) && property_exists($this, $data['field'])) {
            $this->{$data['field']} = $data['value'];

            // Reset dependent fields based on what changed
            if ($data['field'] === 'state_id') {
                $this->municipality_id = null;
                // Don't reset institution_id here to prevent losing user data
            } else if ($data['field'] === 'country_code') {
                $this->state_id = null;
                $this->municipality_id = null;
            }
        }
    }

    public function testAction()
    {
        \Log::info("Teste de ação do Livewire disparado!");
    }

    public function submit()
    {
        try {
            // Base validation rules (mantidas como antes)
            $rules = [
                'full_name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|min:8|confirmed',
                'birth_date' => 'nullable|date',
                'country_code' => 'required|string|max:2|exists:countries,code',
                'gender' => 'required|string',
            ];

            if (!$this->isInternational) {
                $rules['state_id'] = 'required|exists:states,id';
                $rules['municipality_id'] = 'required|exists:municipalities,id';
            }

            if ($this->showNewInstitution) {
                $rules['new_institution'] = 'required|string|max:255';
            } else {
                $rules['institution_id'] = 'required|exists:institutions,id';
            }

            if ($this->showNewLaboratory) {
                $rules['new_laboratory'] = 'required|string|max:255';
            } else if (!$this->showNewInstitution) {
                $rules['laboratory_id'] = 'required|exists:laboratories,id';
            }

            $this->validate($rules);

            $data = [
                'full_name' => $this->full_name,
                'email' => $this->email,
                'password' => $this->password,
                'password_confirmation' => $this->password_confirmation,
                'birth_date' => $this->birth_date,
                'country_code' => $this->country_code,
                'state_id' => $this->state_id,
                'municipality_id' => $this->municipality_id,
                'institution_id' => $this->institution_id,
                'new_institution' => $this->new_institution,
                'laboratory_id' => $this->laboratory_id,
                'new_laboratory' => $this->new_laboratory,
                'lab_coordinator' => $this->lab_coordinator,
                'isInternational' => $this->isInternational,
                'gender' => $this->gender,
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

            // Determinar o nome do laboratório para o Team
            $laboratoryName = null;
            $laboratory = null;

            if ($this->showNewLaboratory && $this->new_laboratory) {
                // Caso de novo laboratório
                $laboratory = Laboratory::create([
                    'name' => $this->new_laboratory,
                    'institution_id' => $this->showNewInstitution ? null : $this->institution_id,
                    'state_id' => $this->state_id,
                ]);
                $laboratoryName = $this->new_laboratory;
                Log::info('Novo laboratório criado:', ['laboratory_id' => $laboratory->id, 'name' => $laboratoryName]);
            } elseif ($this->laboratory_id) {
                // Caso de laboratório existente
                $laboratory = Laboratory::find($this->laboratory_id);
                if (!$laboratory) {
                    throw new \Exception('Laboratório selecionado não encontrado.');
                }
                $laboratoryName = $laboratory->name;
                Log::info('Laboratório existente selecionado:', ['laboratory_id' => $laboratory->id, 'name' => $laboratoryName]);
            }

            // Criar ou associar o Team usando CreateTeam do Jetstream
            if ($laboratoryName) {
                $createTeam = new CreateTeam();
                $teamInput = [
                    'name' => $laboratoryName,
                ];
                $team = $createTeam->create($user, $teamInput);

                // Garantir que personal_team seja 0 (não é um time pessoal)
                $team->update(['personal_team' => false]);
                Log::info('Team criado/atualizado com o nome do laboratório usando Jetstream:', ['team_id' => $team->id, 'name' => $team->name, 'personal_team' => $team->personal_team]);

                // Associar o laboratório ao Team (caso ainda não esteja)
                if ($laboratory && !$laboratory->team_id) {
                    $laboratory->team_id = $team->id;
                    $laboratory->save();
                    Log::info('Laboratório associado ao Team:', ['laboratory_id' => $laboratory->id, 'team_id' => $team->id]);
                }

                // Vincular o usuário ao Team como owner ou member, usando AddTeamMember do Jetstream
                $role = $this->lab_coordinator ? 'owner' : 'member';
                $addTeamMember = new \App\Actions\Jetstream\AddTeamMember();
                $addTeamMember->add($user, $team, $user->email, $role);

                Log::info('Usuário vinculado ao Team com papel:', ['user_id' => $user->id, 'team_id' => $team->id, 'role' => $role]);
            } else {
                Log::warning('Nenhum laboratório definido para criar o Team.');
            }

            // Verificar se o usuário é autenticável antes de tentar login
            if (!($user instanceof \Illuminate\Contracts\Auth\Authenticatable)) {
                throw new \Exception('O objeto User não implementa Authenticatable.');
            }

            $loginSuccessful = Auth::login($user);
            if (!$loginSuccessful) {
                Log::error('Falha ao realizar login:', [
                    'user_id' => $user->id,
                    'user_data' => $user->toArray(),
                    'auth_guard' => Auth::guard()->name,
                    'session_driver' => config('session.driver'),
                ]);
                throw new \Exception('Falha ao realizar login do usuário.');
            }

            Log::info('Login realizado com sucesso para o usuário:', ['user_id' => $user->id]);

            return redirect()->route('dashboard')->with('message', 'Conta criada e login realizado com sucesso!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('Erro de validação ao registrar usuário:', $e->errors());
            throw $e;
        } catch (\Exception $e) {
            Log::error('Erro ao registrar usuário: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            session()->flash('error', 'Ocorreu um erro ao criar sua conta. Por favor, tente novamente.');
            return null;
        }
    }
    public function render()
    {
        return view('livewire.register-component')
            ->extends('layouts.guest')
            ->section('content');
    }
}
