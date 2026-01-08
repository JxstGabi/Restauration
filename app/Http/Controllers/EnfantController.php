<?php

namespace App\Http\Controllers;

use App\Models\EcoleModel;
use App\Models\EnfantModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnfantController extends Controller
{
    /**
     * Affiche la liste des enfants du parent connecté.
     */
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        $enfants = $user->enfants()->with('ecole')->get();
        return view('enfants.index', compact('enfants'));
    }

    /**
     * Affiche le formulaire d'ajout d'un enfant.
     */
    public function create()
    {
        $ecoles = EcoleModel::orderBy('nom')->get();
        return view('enfants.create', compact('ecoles'));
    }

    /**
     * Enregistre un nouvel enfant.
     */
    public function store(Request $request)
    {
        $request->validate([
            'prenom' => 'required|string|max:255',
            'ecole_id' => 'required|exists:ecoles,id',
            'sexe' => 'nullable|integer|in:0,1',
        ]);

        EnfantModel::create([
            'utilisateur_id' => Auth::id(),
            'prenom' => $request->prenom,
            'ecole_id' => $request->ecole_id,
            'sexe' => $request->sexe,
        ]);

        return redirect()->route('enfants.index')->with('success', 'Enfant ajouté avec succès !');
    }

    /**
     * Affiche le formulaire de modification d'un enfant.
     */
    public function edit(EnfantModel $enfant)
    {
        if ($enfant->utilisateur_id !== Auth::id()) {
            abort(403);
        }
        
        $ecoles = EcoleModel::orderBy('nom')->get();
        return view('enfants.edit', compact('enfant', 'ecoles'));
    }

    /**
     * Met à jour les informations d'un enfant.
     */
    public function update(Request $request, EnfantModel $enfant)
    {
        if ($enfant->utilisateur_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'prenom' => 'required|string|max:255',
            'ecole_id' => 'required|exists:ecoles,id',
            'sexe' => 'nullable|integer|in:0,1',
        ]);

        $enfant->update([
            'prenom' => $request->prenom,
            'ecole_id' => $request->ecole_id,
            'sexe' => $request->sexe,
        ]);

        return redirect()->route('enfants.index')->with('success', 'Informations mises à jour.');
    }

    /**
     * Supprime un enfant (désinscription).
     */
    public function destroy(EnfantModel $enfant)
    {
        // Vérification que l'enfant appartient bien à l'utilisateur connecté
        if ($enfant->utilisateur_id !== Auth::id()) {
            abort(403);
        }

        $enfant->delete();

        return redirect()->route('enfants.index')->with('success', 'Enfant supprimé.');
    }
}
