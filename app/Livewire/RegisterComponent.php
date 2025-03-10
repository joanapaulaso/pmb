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

    // Adicionando variáveis para as categorias
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

    public function setInstitutionAddress($data)
    {
        $this->institution_address = $data['address'] ?? '';
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

    public function updatedInstitutionId($value)
    {
        // Resetar campos dependentes
        $this->laboratory_id = null;
        $this->institution_address = ''; // Resetar o endereço quando a instituição mudar
        $this->dispatch('dependencyChanged');
    }

    public function optionSelected($data)
    {
        Log::info("Option selected in parent: " . json_encode($data));

        if (isset($data['field']) && property_exists($this, $data['field'])) {
            $this->{$data['field']} = $data['value'];

            if ($data['field'] === 'state_id') {
                $this->municipality_id = null;
            } else if ($data['field'] === 'country_code') {
                $this->state_id = null;
                $this->municipality_id = null;
            } else if ($data['field'] === 'institution_id') {
                // Quando uma instituição é selecionada, limpar o campo de endereço
                $this->institution_address = '';
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
            // Regras de validação básicas
            $rules = [
                'full_name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|min:8|confirmed',
                'birth_date' => 'nullable|date',
                'country_code' => 'required|string|max:2|exists:countries,code',
                'gender' => 'required|string',
                'institution_address' => 'nullable|string|max:255', // Novo campo de endereço (opcional)
            ];

            // Regras condicionais para localização (se usuário for do Brasil)
            if (!$this->isInternational) {
                $rules['state_id'] = 'required|exists:states,id';
                $rules['municipality_id'] = 'required|exists:municipalities,id';
            }

            // Regras condicionais para instituição
            if ($this->showNewInstitution) {
                $rules['new_institution'] = 'required|string|max:255';
            } else {
                $rules['institution_id'] = 'required|exists:institutions,id';
            }

            // Regras condicionais para laboratório
            if ($this->showNewLaboratory) {
                $rules['new_laboratory'] = 'required|string|max:255';
            } else if (!$this->showNewInstitution) {
                $rules['laboratory_id'] = 'required|exists:laboratories,id';
            }

            // Executar a validação
            $this->validate($rules);

            // Preparar os dados a serem enviados para CreateNewUser
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
                'institution_address' => $this->institution_address, // Novo campo adicionado
            ];

            // Adicionar subcategorias, se houver
            if (!empty($this->selectedSubcategories)) {
                $data['selected_subcategories'] = $this->selectedSubcategories;
            }

            Log::info('Dados enviados para CreateNewUser:', $data);

            // Criar o usuário usando a ação CreateNewUser
            $createNewUser = new CreateNewUser();
            $user = $createNewUser->create($data);

            if (!$user || !$user->id) {
                throw new \Exception('Falha ao criar o usuário: objeto inválido retornado.');
            }

            Log::info('Usuário criado com sucesso:', ['user_id' => $user->id, 'user_data' => $user->toArray()]);

            // Determinar o nome do laboratório e criar o laboratório, se necessário
            $laboratoryName = null;
            $laboratory = null;

            if ($this->showNewLaboratory && $this->new_laboratory) {
                $laboratory = Laboratory::create([
                    'name' => $this->new_laboratory,
                    'institution_id' => $this->showNewInstitution ? null : $this->institution_id,
                    'state_id' => $this->state_id,
                ]);
                $laboratoryName = $this->new_laboratory;
                Log::info('Novo laboratório criado:', ['laboratory_id' => $laboratory->id, 'name' => $laboratoryName]);
            } elseif ($this->laboratory_id) {
                $laboratory = Laboratory::find($this->laboratory_id);
                if (!$laboratory) {
                    throw new \Exception('Laboratório selecionado não encontrado.');
                }
                $laboratoryName = $laboratory->name;
                Log::info('Laboratório existente selecionado:', ['laboratory_id' => $laboratory->id, 'name' => $laboratoryName]);
            }

            // Criar equipe (team) se houver um laboratório
            if ($laboratoryName) {
                $createTeam = new CreateTeam();
                $teamInput = [
                    'name' => $laboratoryName,
                    'address' => $this->institution_address, // Endereço inicial definido pelo coordenador
                ];
                $team = $createTeam->create($user, $teamInput);

                // Garantir que personal_team seja falso
                if ($team->personal_team) {
                    $team->update(['personal_team' => false]);
                }
                Log::info('Team criado/atualizado com o nome do laboratório usando Jetstream:', [
                    'team_id' => $team->id,
                    'name' => $team->name,
                    'personal_team' => $team->personal_team,
                    'user_id' => $team->user_id,
                ]);

                // Associar o laboratório ao time
                if ($laboratory && !$laboratory->team_id) {
                    $laboratory->team_id = $team->id;
                    $laboratory->save();
                    Log::info('Laboratório associado ao Team:', ['laboratory_id' => $laboratory->id, 'team_id' => $team->id]);
                }

                // Adicionar o usuário como membro da equipe, exceto se for o coordenador
                if (!$this->lab_coordinator) {
                    $role = 'editor'; // Papel padrão do Jetstream
                    $addTeamMember = new AddTeamMember();
                    try {
                        $addTeamMember->add($user, $team, $user->email, $role);
                        Log::info('Usuário vinculado ao Team com papel:', ['user_id' => $user->id, 'team_id' => $team->id, 'role' => $role]);
                    } catch (\Exception $e) {
                        Log::error('Erro ao vincular usuário ao Team:', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
                        throw new \Exception('Falha ao vincular o usuário ao Team: ' . $e->getMessage());
                    }
                } else {
                    Log::info('Usuário é o coordenador e já é o dono do time; nenhuma ação adicional necessária.', ['user_id' => $user->id, 'team_id' => $team->id]);
                }
            } else {
                Log::warning('Nenhum laboratório definido para criar o Team.');
            }

            // Verificar se o usuário é autenticável antes de fazer login
            if (!($user instanceof \Illuminate\Contracts\Auth\Authenticatable)) {
                throw new \Exception('O objeto User não implementa Authenticatable.');
            }

            // Fazer login e redirecionar
            Auth::login($user);
            Log::info('Login realizado com sucesso para o usuário:', ['user_id' => $user->id]);
            return redirect()->route('dashboard')->with('message', 'Conta criada e login realizado com sucesso!');
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
