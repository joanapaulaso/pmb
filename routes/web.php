<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\PublicProfileController;
use App\Http\Controllers\DropdownController;
use App\Models\Post;
use App\Livewire\RegisterComponent;

Route::get('/', function () {
    return view('welcome');
});

// Rota para o registro usando Livewire, apenas para usuários não autenticados
Route::middleware('guest')->group(function () {
    Route::get('/register', RegisterComponent::class)->name('register');
});

// Rotas protegidas por autenticação
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    // Rota unificada para o dashboard
    Route::match(['get', 'post'], '/dashboard', function (Request $request) {
        $tags = ['all', 'general', 'question', 'job', 'promotion', 'idea', 'collaboration', 'news', 'paper'];
        $posts = Post::all(); // Pode ser ajustado para filtragem no PostController se necessário
        $selectedTags = $request->input('tags', []);

        if ($request->isMethod('post')) {
            // Lógica adicional para POST, como filtragem, pode ser delegada ao PostController
            return app(PostController::class)->index($request);
        }

        return view('dashboard', compact('posts', 'tags', 'selectedTags'));
    })->name('dashboard');

    // Outras rotas autenticadas
    Route::get('/membros', [UserController::class, 'index'])->name('membros');
    Route::post('/posts', [PostController::class, 'store'])->name('posts.store');
    Route::post('/posts/{post}/reply', [PostController::class, 'reply'])->name('posts.reply');
    Route::delete('/posts/{post}', [PostController::class, 'destroy'])->name('posts.destroy');
    Route::delete('/replies/{reply}', [PostController::class, 'destroyReply'])->name('replies.destroy');
    Route::get('/profile/{user}', [PublicProfileController::class, 'show'])->name('public.profile');
});

// Rotas para dropdowns
Route::middleware(['web'])->group(function () {
    Route::get('/get-countries', [DropdownController::class, 'getCountries']);
    Route::get('/get-states', [DropdownController::class, 'getStates']);
    Route::get('/get-municipalities/{state_id}', [DropdownController::class, 'getMunicipalities']);
    Route::get('/get-institutions/{state_id}', [DropdownController::class, 'getInstitutions']);
    Route::get('/get-laboratories/{institution_id?}', [DropdownController::class, 'getLaboratories']);
});
