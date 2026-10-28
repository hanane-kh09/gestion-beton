<?php

namespace Database\Seeders;

use App\Models\Formulation;
use App\Models\MatierePremiere;
use App\Models\Produit;
use Illuminate\Database\Seeder;

class StockSeeder extends Seeder
{
    public function run(): void
    {
        $matieresData = [
            'Ciment CPJ45' => ['unite' => 'tonne', 'stock_actuel' => 120, 'seuil_alerte' => 30],
            'Sable' => ['unite' => 'm3', 'stock_actuel' => 400, 'seuil_alerte' => 100],
            'Gravier G1' => ['unite' => 'm3', 'stock_actuel' => 350, 'seuil_alerte' => 100],
            'Gravier G2' => ['unite' => 'm3', 'stock_actuel' => 300, 'seuil_alerte' => 80],
            'Adjuvant plastifiant' => ['unite' => 'litre', 'stock_actuel' => 800, 'seuil_alerte' => 200],
        ];

        $matieres = [];
        foreach ($matieresData as $nom => $attrs) {
            $matieres[$nom] = MatierePremiere::updateOrCreate(['nom' => $nom], $attrs);
        }

        $formulationsParProduit = [
            'B15' => ['Ciment CPJ45' => 0.250, 'Sable' => 0.44, 'Gravier G1' => 0.60, 'Gravier G2' => 0.38, 'Adjuvant plastifiant' => 0.9],
            'B20' => ['Ciment CPJ45' => 0.300, 'Sable' => 0.42, 'Gravier G1' => 0.58, 'Gravier G2' => 0.37, 'Adjuvant plastifiant' => 1.0],
            'B25' => ['Ciment CPJ45' => 0.350, 'Sable' => 0.40, 'Gravier G1' => 0.55, 'Gravier G2' => 0.35, 'Adjuvant plastifiant' => 1.2],
            'B30' => ['Ciment CPJ45' => 0.400, 'Sable' => 0.38, 'Gravier G1' => 0.53, 'Gravier G2' => 0.34, 'Adjuvant plastifiant' => 1.35],
            'B35' => ['Ciment CPJ45' => 0.425, 'Sable' => 0.37, 'Gravier G1' => 0.52, 'Gravier G2' => 0.33, 'Adjuvant plastifiant' => 1.45],
        ];

        foreach ($formulationsParProduit as $code => $dosages) {
            $produit = Produit::where('code', $code)->first();
            if (! $produit) {
                continue;
            }

            foreach ($dosages as $nomMatiere => $quantite) {
                Formulation::updateOrCreate(
                    ['produit_id' => $produit->id, 'matiere_premiere_id' => $matieres[$nomMatiere]->id],
                    ['quantite_par_m3' => $quantite]
                );
            }
        }
    }
}
