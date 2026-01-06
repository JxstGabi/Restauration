<?php

namespace App\Http\Controllers;

use App\Models\EcoleModel;

class EcoleController extends Controller
{
    public function index()
    {
        // On récupère toutes les écoles
        $ecoles = EcoleModel::all();

        return view('accueil', compact('ecoles'));
    }
    
}