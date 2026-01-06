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
        return view('menus', compact('school'));
    }
}
