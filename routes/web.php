<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EcoleController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EnfantController;
use App\Models\EcoleModel;

use Illuminate\Support\Facades\Auth;

// Page de bienvenue (Landing page)
Route::get('/', function () {
    $ecoles = EcoleModel::orderBy('nom')->get();
    $enfants = Auth::check() ? Auth::user()->enfants()->with('ecole')->get() : collect();
    return view('bienvenue', compact('ecoles', 'enfants'));
})->name('bienvenue');

// Authentification
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Gestion des enfants (protégé par auth)
Route::middleware(['auth'])->group(function () {
    Route::resource('enfants', EnfantController::class)->except(['show']);
});

// Page carte (anciennement accueil)
Route::get('/map', [EcoleController::class, 'index'])->name('map');

// Page listant les menus (optionnellement filtrés par `?school=Nom+ecole`)
Route::get('/menus', [MenuController::class, 'index'])->name('menus.index');
