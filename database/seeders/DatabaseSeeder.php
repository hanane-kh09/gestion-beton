<?php

namespace Database\Seeders;

use App\Models\Camion;
use App\Models\Chauffeur;
use App\Models\Produit;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::create(['name' => 'Administrateur', 'email' => 'admin@alhamra.ma', 'password' => 'password', 'role' => 'admin']);
        User::create(['name' => 'Service Commercial', 'email' => 'commercial@alhamra.ma', 'password' => 'password', 'role' => 'commercial']);
        User::create(['name' => 'Responsable Logistique', 'email' => 'logistique@alhamra.ma', 'password' => 'password', 'role' => 'logistique']);
        User::create(['name' => 'Caissier Comptable', 'email' => 'caissier@alhamra.ma', 'password' => 'password', 'role' => 'caissier']);

        $produits = [
            ['code' => 'B15', 'designation' => 'Béton B15 — propreté et gros béton (dosage 250 kg/m³)', 'prix_m3' => 750],
            ['code' => 'B20', 'designation' => 'Béton B20 — dallages et formes (dosage 300 kg/m³)', 'prix_m3' => 820],
            ['code' => 'B25', 'designation' => 'Béton B25 — structures courantes (dosage 350 kg/m³)', 'prix_m3' => 900],
            ['code' => 'B30', 'designation' => 'Béton B30 — structures exigeantes (dosage 400 kg/m³)', 'prix_m3' => 980],
            ['code' => 'B35', 'designation' => 'Béton B35 — ouvrages spéciaux', 'prix_m3' => 1080],
            ['code' => 'POMPAGE', 'designation' => 'Service de pompage du béton (forfait par intervention)', 'prix_m3' => 1500],
        ];
        foreach ($produits as $p) {
            Produit::create($p);
        }

        $c1 = Camion::create(['matricule' => '12345-A-40', 'capacite_m3' => 8, 'etat' => 'disponible']);
        $c2 = Camion::create(['matricule' => '67890-B-40', 'capacite_m3' => 10, 'etat' => 'disponible']);
        $c3 = Camion::create(['matricule' => '54321-C-40', 'capacite_m3' => 8, 'etat' => 'disponible']);

        Chauffeur::create(['nom' => 'EL AMRANI', 'prenom' => 'Mohamed', 'cin' => 'EE123456', 'num_permis' => 'P-445566', 'telephone' => '0661000001', 'camion_id' => $c1->id]);
        Chauffeur::create(['nom' => 'BENALI', 'prenom' => 'Rachid', 'cin' => 'EE654321', 'num_permis' => 'P-778899', 'telephone' => '0661000002', 'camion_id' => $c2->id]);
        Chauffeur::create(['nom' => 'OUAZZANI', 'prenom' => 'Hassan', 'cin' => 'EE112233', 'num_permis' => 'P-334455', 'telephone' => '0661000003', 'camion_id' => $c3->id]);

        $this->call(DemoSeeder::class);
    }
}
