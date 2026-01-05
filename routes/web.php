<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EcoleController;

Route::get('/', [EcoleController::class, 'index'])->name('accueil');
