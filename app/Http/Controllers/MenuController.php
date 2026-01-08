<?php

namespace App\Http\Controllers;

use App\Models\EcoleModel;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    /**
     * Affiche la page des menus. Accepte le paramètre `school` en query string.
     */
    public function index(Request $request)
    {
        $request->validate([
            'school' => 'nullable|string|max:255',
        ]);

        $school = $request->query('school');

        // Vérification de l'existence de l'école (exacte ou partielle)
        if ($school) {
            // On cherche si une école correspond
            $exists = EcoleModel::where('nom', 'LIKE', "%$school%")->exists();

            if (!$exists) {
                return redirect()->route('bienvenue')->withErrors(['school' => "L'école demandée n'a pas été trouvée."])->withInput();
            }
        }

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

    /**
     * Affiche une page simplifiée du menu pour le partage.
     */
    public function share(Request $request)
    {
        $request->validate([
            'school' => 'required|string|max:255',
            'child' => 'nullable|string|max:255',
        ]);

        $school = $request->query('school');
        $childName = $request->query('child');
        
        // Redondant avec required mais explicite
        if (!$school) {
            return redirect()->route('menus.index');
        }

        return view('menus.share', compact('school', 'childName'));
    }
}
