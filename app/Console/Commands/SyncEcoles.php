<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\EcoleModel;

class SyncEcoles extends Command
{
    protected $signature = 'sync:ecoles';
    protected $description = 'Synchronise les écoles depuis l’API OpenData d’Angers';

    public function handle()
    {
        $url = "https://data.angers.fr/api/explore/v2.1/catalog/datasets/ecoles_angers/records?limit=100";

        $response = Http::get($url);

        if (!$response->successful()) {
            dd("Erreur API", $response->status(), $response->body());
        }

        $records = $response->json()['results'] ?? [];

        foreach ($records as $record) {

            // ID externe = id_adresse (unique par école)
            $idExterne = $record['id_adresse'] ?? null;

            if (!$idExterne) {
                continue;
            }

            EcoleModel::updateOrCreate(
                ['id_externe' => $idExterne],
                [
                    'nom' => $record['nom'] ?? 'Nom inconnu',
                    'type' => strtolower($record['libelle_structure'] ?? 'inconnu'),
                    'adresse' => $record['libelle_adresse'] ?? null,
                    'ville' => $record['nom_commune'] ?? null,
                    'code_postal' => $record['code_postal'] ?? null,
                    'latitude' => $record['geo_point_2d']['lat'] ?? null,
                    'longitude' => $record['geo_point_2d']['lon'] ?? null,
                ]
            );
        }

        $this->info("Synchronisation terminée !");
    }

}