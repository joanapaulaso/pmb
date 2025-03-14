<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\PostPortalController;
use App\Http\Controllers\PublicProfileController;
use App\Http\Controllers\DropdownController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\LabsMapController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PortalController;
use App\Http\Controllers\VideoController;
use App\Http\Controllers\PublicEventController;
use App\Livewire\RegisterComponent;

// Controllers de Admin
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\VideoPlaylistController;
use App\Http\Controllers\Admin\LaboratoryController;
use App\Http\Controllers\Admin\EventController;

// Rotas públicas - acessíveis sem autenticação
Route::get('/', function () {
    return view('welcome');
})->name('welcome');

// Rotas para vídeos e eventos (públicas)
Route::prefix('videos')->name('videos.')->group(function () {
    Route::get('/', [VideoController::class, 'index'])->name('index');
    Route::get('/playlists/{playlist}', [VideoController::class, 'showPlaylist'])->name('showPlaylist');
});

Route::prefix('events')->name('events.')->group(function () {
    Route::get('/', [PublicEventController::class, 'index'])->name('index'); // Usando PublicEventController
    Route::get('/{slug}', [PublicEventController::class, 'show'])->name('show'); // Usando PublicEventController
});

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

    Route::controller(PortalController::class)->group(function () {
        Route::get('/portal', 'index')->name('portal');
        Route::post('/portal', 'filter')->name('portal.filter');
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

    // Grupo de rotas para Posts do Portal
    Route::controller(PostPortalController::class)->group(function () {
        Route::post('/posts-portal', 'store')->name('posts-portal.store');
        Route::post('/posts-portal/{post}/reply', 'reply')->name('posts-portal.reply');
        Route::delete('/posts-portal/{post}', 'destroy')->name('posts-portal.destroy');
        Route::delete('/replies-portal/{reply}', 'destroyReply')->name('replies-portal.destroy');
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

    Route::post('/upload-image', [App\Http\Controllers\ImageUploadController::class, 'upload'])
        ->middleware(['auth'])
        ->name('upload.image');
});

// Rotas para dropdowns que precisam apenas do middleware 'web'
Route::middleware('web')->controller(DropdownController::class)->prefix('dropdown')->name('dropdown.')->group(function () {
    Route::get('/countries', 'getCountries')->name('countries');
    Route::get('/states', 'getStates')->name('states');
    Route::get('/municipalities/{state_id}', 'getMunicipalities')->name('municipalities');
    Route::get('/institutions/{state_id}', 'getInstitutions')->name('institutions');
    Route::get('/laboratories/{institution_id?}', 'getLaboratories')->name('laboratories');
});

// Rotas para o painel administrativo
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard principal
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

    // Rotas para Videos e Playlists
    Route::get('/videos', [VideoPlaylistController::class, 'index'])->name('videos.index');
    Route::get('/videos/create', [VideoPlaylistController::class, 'createVideo'])->name('videos.create');
    Route::post('/videos', [VideoPlaylistController::class, 'storeVideo'])->name('videos.store');
    Route::get('/videos/{video}/edit', [VideoPlaylistController::class, 'editVideo'])->name('videos.edit');
    Route::put('/videos/{video}', [VideoPlaylistController::class, 'updateVideo'])->name('videos.update');
    Route::delete('/videos/{video}', [VideoPlaylistController::class, 'destroyVideo'])->name('videos.destroy');

    Route::get('/playlists/create', [VideoPlaylistController::class, 'createPlaylist'])->name('playlists.create');
    Route::post('/playlists', [VideoPlaylistController::class, 'storePlaylist'])->name('playlists.store');
    Route::get('/playlists/{playlist}/edit', [VideoPlaylistController::class, 'editPlaylist'])->name('playlists.edit');
    Route::put('/playlists/{playlist}', [VideoPlaylistController::class, 'updatePlaylist'])->name('playlists.update');
    Route::delete('/playlists/{playlist}', [VideoPlaylistController::class, 'destroyPlaylist'])->name('playlists.destroy');

    // Rotas para Laboratórios
    Route::get('/laboratories', [LaboratoryController::class, 'index'])->name('laboratories.index');
    Route::get('/laboratories/create', [LaboratoryController::class, 'create'])->name('laboratories.create');
    Route::post('/laboratories', [LaboratoryController::class, 'store'])->name('laboratories.store');
    Route::get('/laboratories/{laboratory}/edit', [LaboratoryController::class, 'edit'])->name('laboratories.edit');
    Route::put('/laboratories/{laboratory}', [LaboratoryController::class, 'update'])->name('laboratories.update');
    Route::delete('/laboratories/{laboratory}', [LaboratoryController::class, 'destroy'])->name('laboratories.destroy');

    // Rotas para Eventos (Workshops/Seminários)
    Route::get('/events', [EventController::class, 'index'])->name('events.index');
    Route::get('/events/create', [EventController::class, 'create'])->name('events.create');
    Route::post('/events', [EventController::class, 'store'])->name('events.store');
    Route::get('/events/{event}/edit', [EventController::class, 'edit'])->name('events.edit');
    Route::put('/events/{event}', [EventController::class, 'update'])->name('events.update');
    Route::delete('/events/{event}', [EventController::class, 'destroy'])->name('events.destroy');
});
