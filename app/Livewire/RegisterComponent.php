<?php

namespace App\Livewire;

use Livewire\Component;
use App\Actions\Fortify\CreateNewUser;
use Illuminate\Support\Facades\Auth;
use App\Models\Country;
use App\Models\State;
use App\Models\Municipality;
use App\Models\Institution;
use App\Models\Laboratory;
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

    // This method handles when options are selected from search-select components
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
            // Base validation rules
            $rules = [
                'full_name' => 'required|string|max:255',
                'email' => 'required|email|unique:users',
                'password' => 'required|min:8|confirmed',
                'birth_date' => 'nullable|date',
                'country_code' => 'required|string|max:2|exists:countries,code',
                'gender' => 'required|string', // Validação para o campo de gênero
            ];

            // Add conditional validation rules based on location
            if (!$this->isInternational) {
                $rules['state_id'] = 'required|exists:states,id';
                $rules['municipality_id'] = 'required|exists:municipalities,id';
            }

            // Institution validation
            if ($this->showNewInstitution) {
                $rules['new_institution'] = 'required|string|max:255';
            } else {
                $rules['institution_id'] = 'required|exists:institutions,id';
            }

            // Laboratory validation
            if ($this->showNewLaboratory) {
                $rules['new_laboratory'] = 'required|string|max:255';
            } else if (!$this->showNewInstitution) {
                // Only require lab selection if using an existing institution
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
                'gender' => $this->gender, // Adicionando o gênero aos dados
            ];

            // Adicionar as categorias escolhidas apenas se houver seleções
            if (!empty($this->selectedSubcategories)) {
                $data['selected_subcategories'] = $this->selectedSubcategories;
            }

            $createNewUser = new CreateNewUser();
            $user = $createNewUser->create($data);

            Auth::login($user);

            return redirect()->route('dashboard')->with('message', 'Conta criada e login realizado com sucesso!');
        } catch (\Exception $e) {
            Log::error('Erro ao registrar usuário: ' . $e->getMessage());
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
