<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\Ecole;
use App\Models\EcoleModel;
use App\Models\Menu;
use App\Models\MenuModel;
use Carbon\Carbon;

class ImportMenus extends Command
{
    protected $signature = 'menus:import';
    protected $description = 'Importe les menus scolaires pour les 5 prochaines semaines depuis data.angers.fr';

    public function handle()
    {
        $this->info("🧹 Suppression des menus antérieurs…");
        MenuModel::where('date_menu', '<', Carbon::now()->startOfWeek())->delete();

        $this->info("📦 Import des affectations écoles → restaurants…");

        $affectations = Http::get(
            "https://data.angers.fr/api/records/1.0/search/?dataset=scdl_affectation_ecoles_restaurants_angers_1&rows=500"
        )->json()['records'] ?? [];

        $mapRestaurantToEcole = [];

        foreach ($affectations as $a) {
            $f = $a['fields'] ?? [];
            if (!isset($f['restaurantid'], $f['ecolenom'], $a['recordid'])) continue;

            $ecole = EcoleModel::updateOrCreate(
                ['id_externe' => $a['recordid']],
                [
                    'nom' => $f['ecolenom'],
                    'adresse' => $f['ecoleadresse'] ?? null,
                    'ville' => 'Angers',
                    'restaurant_id' => $f['restaurantid'],
                    'restaurant_nom' => $f['restaurantnom'] ?? null,
                    'latitude' => $f['geo'][0] ?? null,
                    'longitude' => $f['geo'][1] ?? null,
                ]
            );

            $mapRestaurantToEcole[$f['restaurantid']] = $ecole->id;
        }

        $this->info("🥗 Import des menus pour les 5 prochaines semaines…");

        $start = Carbon::now()->startOfWeek();
        $end = Carbon::now()->addWeeks(5)->endOfWeek();

        $url = "https://data.angers.fr/api/records/1.0/search/?" .
               "dataset=scdl_menus_restauration_scolaire_angers" .
               "&rows=5000" .
               "&sort=menudate" .
               "&where=menudate >= '" . $start->format('Y-m-d') . "' AND menudate <= '" . $end->format('Y-m-d') . "'";

        $menus = Http::get($url)->json()['records'] ?? [];

        foreach ($menus as $m) {
            $f = $m['fields'] ?? [];
            $restaurantId = $f['menurestaurantid'] ?? null;
            $ecoleId = $mapRestaurantToEcole[$restaurantId] ?? null;

            if (!$ecoleId || !isset($f['menudate'], $f['menuplatnom'], $f['menuplattype'])) continue;

            $date = Carbon::parse($f['menudate']);
            $week = $date->weekOfYear;

            MenuModel::create([
                'ecole_id' => $ecoleId,
                'date_menu' => $date->format('Y-m-d'),
                'numero_semaine' => $week,
                'entree' => $f['menuplattype'] === 'entrée' ? $f['menuplatnom'] : null,
                'plat_principal' => $f['menuplattype'] === 'plat' ? $f['menuplatnom'] : null,
                'accompagnement' => $f['menuplattype'] === 'accompagnement' ? $f['menuplatnom'] : null,
                'dessert' => $f['menuplattype'] === 'dessert' ? $f['menuplatnom'] : null,
                'est_vegetarien' => $f['menuplatregime'] === 'sans viande',
                'est_biologique' => isset($f['menuplatlabelabio']),
                'donnees_brutes' => $f
            ]);
        }

        $this->info("✅ Import terminé !");
    }
}