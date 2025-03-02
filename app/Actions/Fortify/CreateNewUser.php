<?php

namespace App\Actions\Fortify;

use App\Models\User;
use App\Models\Profile;
use App\Models\Institution;
use App\Models\Laboratory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Laravel\Fortify\Contracts\CreatesNewUsers;

ini_set('max_execution_time', 120); // 2 minutos

class CreateNewUser implements CreatesNewUsers
{
    public function create(array $input)
    {
        try {
            Validator::make($input, [
                'full_name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'password' => ['required', 'string', 'min:8', 'confirmed'],
                'birth_date' => ['nullable', 'date'],
                'institution_id' => ['nullable', 'exists:institutions,id'],
                'new_institution' => ['nullable', 'string', 'max:255'],
                'country_code' => ['required_if:isInternational,true', 'string', 'max:2', 'exists:countries,code'],
                'state_id' => ['nullable', 'exists:states,id', 'exclude_if:isInternational,true'],
                'municipality_id' => ['nullable', 'exists:municipalities,id', 'exclude_if:isInternational,true'],
                'laboratory_id' => ['nullable', 'exists:laboratories,id'],
                'new_laboratory' => ['nullable', 'string', 'max:255'],
                'gender' => ['required', 'string'], // Adicionada validação para gênero
            ])->validate();

            // Criar nova instituição se o usuário digitou uma nova
            if (!empty($input['new_institution'])) {
                $institution = Institution::create([
                    'name' => $input['new_institution'],
                    'state_id' => $input['state_id'] ?? null,
                    'municipality_id' => $input['municipality_id'] ?? null,
                    'country_code' => $input['country_code'] ?? 'BR', // Padrão: Brasil
                ]);
                $input['institution_id'] = $institution->id;
            }

            // Criar novo laboratório se o usuário digitou um novo
            if (!empty($input['new_laboratory'])) {
                $input['laboratory_id'] = $this->getOrCreateLaboratory(
                    $input['new_laboratory'],
                    $input['institution_id'] ?? null,
                    $input['state_id'] ?? null
                );
            }

            // Criar usuário - invertendo para não usar transaction por enquanto
            $user = User::create([
                'name' => $input['full_name'],
                'email' => $input['email'],
                'password' => Hash::make($input['password']),
            ]);

            // Criar o perfil associado ao usuário
            Profile::create([
                'user_id' => $user->id,
                'birth_date' => $input['birth_date'] ?? null,
                'institution_id' => $input['institution_id'] ?? null,
                'country_code' => $input['country_code'] ?? null,
                'state_id' => $input['state_id'] ?? null,
                'municipality_id' => $input['municipality_id'] ?? null,
                'lab_coordinator' => isset($input['lab_coordinator']) ? 1 : 0,
                'laboratory_id' => $input['laboratory_id'] ?? null,
                'gender' => $input['gender'] ?? null, // Adicionado campo de gênero
            ]);

            // Salvar as subcategorias é opcional e não impede criação do usuário
            try {
                if (!empty($input['selected_subcategories'])) {
                    // Verifique se as classes existem antes de tentar usá-las
                    if (class_exists('App\Models\Category') && class_exists('App\Models\UserCategory')) {
                        $this->saveUserCategories($user, $input['selected_subcategories']);
                    }
                }
            } catch (\Exception $e) {
                // Apenas logamos o erro, mas não impedimos a criação do usuário
                Log::error('Erro ao salvar categorias: ' . $e->getMessage());
            }

            return $user;
        } catch (\Exception $e) {
            Log::error('Erro ao criar usuário: ' . $e->getMessage());
            throw $e; // Re-lançar exceção para ser capturada pelo sistema
        }
    }

    /**
     * Salva as categorias selecionadas pelo usuário
     */
    private function saveUserCategories($user, $selectedSubcategories)
    {
        // Se as tabelas ainda não existirem, ignoramos esta etapa
        if (!Schema::hasTable('categories') || !Schema::hasTable('user_categories')) {
            return;
        }

        foreach ($selectedSubcategories as $selection) {
            if (isset($selection['category']) && isset($selection['subcategory'])) {
                $categoryName = $selection['category'];
                $subcategoryName = $selection['subcategory'];

                // Procurar a categoria principal - não impede registro se não encontrar
                $category = \App\Models\Category::where('name', $categoryName)
                    ->where('type', 'category')
                    ->first();

                if ($category) {
                    // Procurar a subcategoria relacionada
                    $subcategory = \App\Models\Category::where('name', $subcategoryName)
                        ->where('type', 'subcategory')
                        ->where('parent_id', $category->id)
                        ->first();

                    if ($subcategory) {
                        \App\Models\UserCategory::create([
                            'user_id' => $user->id,
                            'category_id' => $subcategory->id,
                            'category_name' => $categoryName,
                            'subcategory_name' => $subcategoryName
                        ]);
                    } else {
                        // Se a subcategoria não existir no banco, ainda podemos armazenar os nomes
                        \App\Models\UserCategory::create([
                            'user_id' => $user->id,
                            'category_id' => $category->id,  // Usamos o ID da categoria principal
                            'category_name' => $categoryName,
                            'subcategory_name' => $subcategoryName
                        ]);
                    }
                }
            }
        }
    }

    private function getOrCreateLaboratory($labName, $institutionId, $stateId)
    {
        if (!$labName || !$institutionId || !$stateId) {
            return null; // Se faltar algum dado essencial, retorna null
        }

        // Verifica se a instituição realmente pertence ao estado selecionado
        $institution = Institution::where('id', $institutionId)
            ->where('state_id', $stateId)
            ->first();

        if (!$institution) {
            \Log::warning("Instituição ID {$institutionId} não encontrada no estado {$stateId}");
            return null;
        }

        // Verifica se o laboratório já existe para a instituição e estado
        $laboratory = Laboratory::where('name', $labName)
            ->where('institution_id', $institution->id)
            ->where('state_id', $stateId)
            ->first();

        if (!$laboratory) {
            try {
                $laboratory = Laboratory::create([
                    'name' => $labName,
                    'institution_id' => $institution->id,
                    'state_id' => $stateId,
                ]);
                \Log::info("Novo laboratório '{$labName}' criado para a instituição '{$institution->name}' no estado {$stateId}");
            } catch (\Exception $e) {
                \Log::error('Erro ao criar laboratório: ' . $e->getMessage());
                return null;
            }
        }

        return $laboratory->id;
    }
}
