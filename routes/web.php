<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EcoleController;
use App\Http\Controllers\MenuController;
use App\Models\EcoleModel;

// Page de bienvenue (Landing page)
Route::get('/', function () {
    $ecoles = EcoleModel::orderBy('nom')->get();
    return view('bienvenue', compact('ecoles'));
})->name('bienvenue');

// Page carte (anciennement accueil)
Route::get('/map', [EcoleController::class, 'index'])->name('map');

// Page listant les menus (optionnellement filtrés par `?school=Nom+ecole`)
Route::get('/menus', [MenuController::class, 'index'])->name('menus.index');
