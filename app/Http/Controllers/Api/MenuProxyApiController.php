<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class MenuProxyApiController extends Controller
{
    public function index(Request $request)
    {
        $params = [
            'dataset' => 'scdl_menus_restauration_scolaire_angers',
            'rows' => $request->query('rows', 200),
            'sort' => $request->query('sort', 'menudate'),
        ];
        if ($request->has('where')) {
            $params['where'] = $request->query('where');
        }
        if ($request->has('refine.menurestaurantnom')) {
            $params['refine.menurestaurantnom'] = $request->query('refine.menurestaurantnom');
        }
        if ($request->has('q')) {
            $params['q'] = $request->query('q');
        }
        $response = Http::get('https://data.angers.fr/api/records/1.0/search/', $params);
        return response($response->body(), $response->status())
            ->header('Content-Type', $response->header('Content-Type', 'application/json'));
    }
}
