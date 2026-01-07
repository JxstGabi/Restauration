<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\MenuProxyApiController;

Route::get('/menus-angers', [MenuProxyApiController::class, 'index']);
