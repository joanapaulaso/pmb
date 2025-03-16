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
            'gender' => ['required', 'string'],
            'institution_address' => ['nullable', 'string', 'max:255'],
            'admin' => ['nullable', 'boolean'],
            'team_id' => ['nullable', 'exists:teams,id'],
        ])->validate();

        // Criar ou atualizar instituição com o endereço
        if (!empty($input['new_institution'])) {
            $institution = Institution::create([
                'name' => $input['new_institution'],
                'state_id' => $input['isInternational'] ? null : ($input['state_id'] ?? null),
                'municipality_id' => $input['isInternational'] ? null : ($input['municipality_id'] ?? null),
                'country_code' => $input['country_code'] ?? 'BR',
                'address' => $input['institution_address'] ?? null,
            ]);
            $input['institution_id'] = $institution->id;
        } elseif (!empty($input['institution_id']) && !empty($input['institution_address'])) {
            $institution = Institution::find($input['institution_id']);
            if ($institution && !$institution->address) {
                $institution->update(['address' => $input['institution_address']]);
            }
        }

        if (!empty($input['new_laboratory'])) {
            $input['laboratory_id'] = $this->getOrCreateLaboratory(
                $input['new_laboratory'],
                $input['institution_id'] ?? null,
                $input['state_id'] ?? null,
                $input['team_id'] ?? null
            );
        }

        $user = User::create([
            'name' => $input['full_name'],
            'email' => $input['email'],
            'password' => Hash::make($input['password']),
            'admin' => isset($input['admin']) ? (bool) $input['admin'] : false,
        ]);

        Profile::create([
            'user_id' => $user->id,
            'birth_date' => $input['birth_date'] ?? null,
            'institution_id' => $input['institution_id'] ?? null,
            'country_code' => $input['country_code'] ?? null,
            'state_id' => $input['state_id'] ?? null,
            'municipality_id' => $input['municipality_id'] ?? null,
            'lab_coordinator' => isset($input['lab_coordinator']) ? 1 : 0,
            'laboratory_id' => $input['laboratory_id'] ?? null,
            'gender' => $input['gender'] ?? null,
        ]);

        // Associar o usuário ao time, se team_id for fornecido
        if (!empty($input['team_id'])) {
            $team = \App\Models\Team::find($input['team_id']);
            if ($team) {
                $user->teams()->attach($team->id, ['created_at' => now(), 'updated_at' => now()]);
                \Log::info("Usuário {$user->id} associado ao time {$team->id}");
            } else {
                \Log::warning("Time {$input['team_id']} não encontrado para associação com o usuário {$user->id}");
            }
        }

        if (!empty($input['selected_subcategories'])) {
            $this->saveUserCategories($user, $input['selected_subcategories']);
        }

        return $user;
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

    /**
     * Obtém ou cria um laboratório com base no nome, instituição, estado e time
     *
     * @param string $labName Nome do laboratório
     * @param int|null $institutionId ID da instituição
     * @param int|null $stateId ID do estado
     * @param int|null $teamId ID do time (adicionado)
     * @return int|null ID do laboratório ou null em caso de erro
     */
    private function getOrCreateLaboratory($labName, $institutionId, $stateId, $teamId = null)
    {
        if (!$labName || !$institutionId) {
            \Log::warning("Dados insuficientes para criar laboratório: labName={$labName}, institutionId={$institutionId}");
            return null; // Se faltar algum dado essencial, retorna null
        }

        // Verifica se a instituição existe
        $institution = Institution::find($institutionId);
        if (!$institution) {
            \Log::warning("Instituição ID {$institutionId} não encontrada");
            return null;
        }

        // Para usuários do Brasil, validar o estado
        if ($institution->country_code === 'BR' && !$stateId) {
            \Log::warning("Estado é obrigatório para laboratórios de instituições brasileiras");
            return null;
        }

        // Constrói a consulta base
        $query = Laboratory::where('name', $labName)
            ->where('institution_id', $institution->id);

        if ($institution->country_code === 'BR') {
            $query->where('state_id', $stateId);
        } else {
            $query->whereNull('state_id');
        }

        // Se team_id for fornecido, adiciona à consulta
        if ($teamId) {
            $query->where('team_id', $teamId);
        } else {
            $query->whereNull('team_id');
        }

        // Verifica se o laboratório já existe com os critérios fornecidos
        $laboratory = $query->first();

        if (!$laboratory) {
            try {
                $laboratory = Laboratory::create([
                    'name' => $labName,
                    'institution_id' => $institution->id,
                    'state_id' => $institution->country_code === 'BR' ? $stateId : null,
                    'team_id' => $teamId,
                ]);
                \Log::info("Novo laboratório '{$labName}' criado para a instituição '{$institution->name}'" .
                    ($institution->country_code === 'BR' ? " no estado {$stateId}" : " (internacional)") .
                    ($teamId ? " e time {$teamId}" : " sem time associado"));
            } catch (\Exception $e) {
                \Log::error('Erro ao criar laboratório: ' . $e->getMessage());
                return null;
            }
        }

        return $laboratory->id;
    }
}
