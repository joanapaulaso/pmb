<?php

namespace App\Actions\Fortify;

use App\Models\User;
use App\Models\Profile;
use App\Models\Institution;
use App\Models\Laboratory;
use App\Models\PendingLabCoordinator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
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
            'lab_coordinator' => ['nullable', 'boolean'],
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

        // Criar ou obter laboratório
        if (!empty($input['new_laboratory'])) {
            $laboratoryId = $this->getOrCreateLaboratory(
                $input['new_laboratory'],
                $input['institution_id'] ?? null,
                $input['isInternational'] ? null : ($input['state_id'] ?? null),
                $input['team_id'] ?? null
            );
            $input['laboratory_id'] = $laboratoryId; // Ensure laboratory_id is set
        }

        $user = User::create([
            'name' => $input['full_name'],
            'email' => $input['email'],
            'password' => Hash::make($input['password']),
            'admin' => isset($input['admin']) ? (bool) $input['admin'] : false,
        ]);

        $profileData = [
            'user_id' => $user->id,
            'birth_date' => $input['birth_date'] ?? null,
            'institution_id' => $input['institution_id'] ?? null,
            'country_code' => $input['country_code'] ?? null,
            'state_id' => $input['isInternational'] ? null : ($input['state_id'] ?? null),
            'municipality_id' => $input['isInternational'] ? null : ($input['municipality_id'] ?? null),
            'laboratory_id' => $input['laboratory_id'] ?? null,
            'gender' => $input['gender'] ?? null,
        ];

        Profile::create($profileData);

        // Handle lab coordinator request
        if (isset($input['lab_coordinator']) && $input['lab_coordinator'] && $input['laboratory_id']) {
            $token = Str::random(60);
            PendingLabCoordinator::create([
                'user_id' => $user->id,
                'laboratory_id' => $input['laboratory_id'],
                'token' => $token,
                'expires_at' => now()->addDays(7),
            ]);

            $this->sendLabCoordinatorApprovalEmail($user, $input['laboratory_id'], $token);
        } else {
            Log::info('Lab coordinator request not processed', [
                'lab_coordinator' => $input['lab_coordinator'] ?? 'not set',
                'laboratory_id' => $input['laboratory_id'] ?? 'not set',
            ]);
            $user->profile->update(['lab_coordinator' => false]);
        }

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

    private function sendLabCoordinatorApprovalEmail($user, $laboratoryId, $token)
    {
        $laboratory = Laboratory::find($laboratoryId);
        $approvalUrl = route('lab-coordinator.approve', ['token' => $token]);
        $rejectionUrl = route('lab-coordinator.reject', ['token' => $token]);

        \Mail::raw(
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

        \Log::info("Email de aprovação de coordenador enviado para contato@portalmetabolomicabrasil.com.br", [
            'user_id' => $user->id,
            'laboratory_id' => $laboratoryId,
            'token' => $token,
        ]);
    }

    /**
     * Salva as categorias selecionadas pelo usuário
     */
    private function saveUserCategories($user, $selectedSubcategories)
    {
        if (!Schema::hasTable('user_categories') || !Schema::hasTable('categories')) {
            \Log::warning("Tabela 'user_categories' ou 'categories' não existe. Não foi possível salvar categorias.");
            return;
        }

        \Log::info("Salvando categorias para o usuário {$user->id}: " . json_encode($selectedSubcategories));

        foreach ($selectedSubcategories as $selection) {
            if (isset($selection['category']) && isset($selection['subcategory'])) {
                $categoryName = $selection['category'];
                $subcategoryName = $selection['subcategory'];

                // Busca ou cria a categoria pai
                $category = \App\Models\Category::firstOrCreate(
                    ['name' => $categoryName, 'type' => 'category'],
                    ['name' => $categoryName, 'type' => 'category']
                );

                // Busca ou cria a subcategoria
                $subcategory = \App\Models\Category::firstOrCreate(
                    ['name' => $subcategoryName, 'type' => 'subcategory', 'parent_id' => $category->id],
                    ['name' => $subcategoryName, 'type' => 'subcategory', 'parent_id' => $category->id]
                );

                // Salva o registro em user_categories com o category_id da subcategoria
                \App\Models\UserCategory::create([
                    'user_id' => $user->id,
                    'category_id' => $subcategory->id,
                    'category_name' => $categoryName,
                    'subcategory_name' => $subcategoryName,
                ]);
                \Log::info("Categoria salva: user_id={$user->id}, category_id={$subcategory->id}, category_name={$categoryName}, subcategory_name={$subcategoryName}");
            } else {
                \Log::warning("Seleção de categoria inválida para o usuário {$user->id}: " . json_encode($selection));
            }
        }
    }

    /**
     * Obtém ou cria um laboratório com base no nome, instituição, estado e time
     */
    private function getOrCreateLaboratory($labName, $institutionId, $stateId, $teamId = null)
    {
        if (!$labName || !$institutionId) {
            \Log::warning("Dados insuficientes para criar laboratório: labName={$labName}, institutionId={$institutionId}");
            return null;
        }

        $institution = Institution::find($institutionId);
        if (!$institution) {
            \Log::warning("Instituição ID {$institutionId} não encontrada");
            return null;
        }

        $query = Laboratory::where('name', $labName)
            ->where('institution_id', $institution->id);

        // Para usuários internacionais, state_id será null
        if ($institution->country_code === 'BR' && !$stateId) {
            \Log::warning("Estado é obrigatório para laboratórios de instituições brasileiras");
            return null;
        } elseif ($institution->country_code === 'BR') {
            $query->where('state_id', $stateId);
        } else {
            $query->whereNull('state_id');
        }

        if ($teamId) {
            $query->where('team_id', $teamId);
        } else {
            $query->whereNull('team_id');
        }

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
