<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\PublicProfileController;
use App\Http\Controllers\DropdownController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\LabsMapController;
use App\Http\Controllers\DashboardController;
use App\Livewire\RegisterComponent;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Aqui é onde você pode registrar as rotas da web para sua aplicação.
| Estas rotas são carregadas pelo RouteServiceProvider dentro de um grupo
| que contém o middleware "web".
|
*/

// Rotas públicas - acessíveis sem autenticação
Route::get('/', function () {
    return view('welcome');
})->name('welcome');

// Rotas para usuários convidados (não autenticados)
Route::middleware('guest')->group(function () {
    Route::get('/register', RegisterComponent::class)->name('register');
});

// Definição dos middlewares comuns para rotas autenticadas
$authMiddleware = [
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
];

// Rotas protegidas por autenticação
Route::middleware($authMiddleware)->group(function () {
    // Grupo de rotas do Dashboard
    Route::controller(DashboardController::class)->group(function () {
        Route::get('/dashboard', 'index')->name('dashboard');
        Route::post('/dashboard', 'filter')->name('dashboard.filter');
    });

    // Grupo de rotas de Usuários
    Route::controller(UserController::class)->group(function () {
        Route::get('/membros', 'index')->name('membros');
    });

    // Grupo de rotas para Perfis Públicos
    Route::controller(PublicProfileController::class)->group(function () {
        Route::get('/profile/{user}', 'show')->name('public.profile');
    });

    // Grupo de rotas para Posts
    Route::controller(PostController::class)->group(function () {
        Route::post('/posts', 'store')->name('posts.store');
        Route::post('/posts/{post}/reply', 'reply')->name('posts.reply');
        Route::delete('/posts/{post}', 'destroy')->name('posts.destroy');
        Route::delete('/replies/{reply}', 'destroyReply')->name('replies.destroy');
    });

    // Grupo de rotas para Times/Equipes
    Route::controller(TeamController::class)->group(function () {
        Route::get('/teams/{team}/settings', 'show')->name('teams.show');
    });

    // Grupo de rotas para o Mapa de Laboratórios
    Route::controller(LabsMapController::class)->prefix('labs')->name('labs.')->group(function () {
        Route::get('/map', 'index')->name('map');
    });

    // Rota de API para dados dos laboratórios
    Route::get('/api/labs', [LabsMapController::class, 'getLabsData'])->name('api.labs');
});

// Rotas para dropdowns que precisam apenas do middleware 'web'
Route::middleware('web')->controller(DropdownController::class)->prefix('dropdown')->name('dropdown.')->group(function () {
    Route::get('/countries', 'getCountries')->name('countries');
    Route::get('/states', 'getStates')->name('states');
    Route::get('/municipalities/{state_id}', 'getMunicipalities')->name('municipalities');
    Route::get('/institutions/{state_id}', 'getInstitutions')->name('institutions');
    Route::get('/laboratories/{institution_id?}', 'getLaboratories')->name('laboratories');
});
