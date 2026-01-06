<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EcoleController;
use App\Http\Controllers\MenuController;

Route::get('/', [EcoleController::class, 'index'])->name('accueil');

// Page listant les menus (optionnellement filtrés par `?school=Nom+ecole`)
Route::get('/menus', [MenuController::class, 'index'])->name('menus.index');
