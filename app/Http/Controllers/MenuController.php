<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MenuController extends Controller
{
    /**
     * Affiche la page des menus. Accepte le paramètre `school` en query string.
     */
    public function index(Request $request)
    {
        $school = $request->query('school');

        // Date du lundi de la semaine actuelle
        $today = now();
        $monday = $today->copy()->startOfWeek();

        // Requête de base : menus à partir de ce lundi
        $query = \App\Models\MenuModel::where('date_menu', '>=', $monday->toDateString());

        // Filtrer par école si précisé
        if ($school) {
            $query->whereHas('ecole', function ($q) use ($school) {
                $q->where('nom', 'like', "%$school%");
            });
        }

        // Récupérer tous les menus triés par date
        $menus = $query->orderBy('date_menu')->get()->groupBy('date_menu');

        return view('menus', compact('school', 'menus'));
    }
    
}
