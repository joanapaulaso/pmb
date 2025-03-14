<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Profile;
use App\Models\Team;
use App\Models\Country;
use App\Models\State;
use App\Models\Municipality;
use App\Models\Institution;
use App\Models\Laboratory;
use App\Models\UserCategory;
use Illuminate\Http\Request;

class PublicProfileController extends Controller
{
    public function show(User $user)
    {
        // Carregar o perfil do usuário com eager loading
        $user->load('profile');
        $profile = $user->profile;

        $country = null;
        $state = null;
        $municipality = null;
        $institution = null;
        $laboratory = null;
        $labTeam = null;

        // Se o perfil não existe, vamos criá-lo para evitar erros
        if (!$profile) {
            $profile = null;
        } else {
            // Carregar país se existir
            if ($profile->country_code) {
                $country = Country::where('code', $profile->country_code)->first();
            }

            // Carregar estado se existir
            if ($profile->state_id) {
                $state = State::find($profile->state_id);
            }

            // Carregar município se existir
            if ($profile->municipality_id) {
                $municipality = Municipality::find($profile->municipality_id);
            }

            // Carregar instituição se existir
            if ($profile->institution_id) {
                $institution = Institution::find($profile->institution_id);
            }

            // Carregar laboratório e seu relacionamento Team
            if ($profile->laboratory_id) {
                $laboratory = Laboratory::find($profile->laboratory_id);

                // Se o laboratório existir e tiver um team_id, buscamos o time diretamente
                if ($laboratory && $laboratory->team_id) {
                    $labTeam = Team::find($laboratory->team_id);
                }
            }
        }

        // Carregar as categorias do usuário
        $userCategories = UserCategory::where('user_id', $user->id)->get();

        // Carregar os Teams (equipes/laboratórios) do usuário com eager loading
        $user->load(['teams', 'ownedTeams']);

        // Obter todos os teams do usuário
        $teams = $user->allTeams();

        return view('public-profile', compact(
            'user',
            'profile',
            'country',
            'state',
            'municipality',
            'institution',
            'laboratory',
            'labTeam',
            'teams',
            'userCategories'
        ));
    }
}
