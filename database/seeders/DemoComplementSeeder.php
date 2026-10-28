<?php

namespace Database\Seeders;

use App\Models\BonLivraison;
use App\Models\Camion;
use App\Models\Client;
use App\Models\Commande;
use App\Models\Facture;
use App\Models\MatierePremiere;
use App\Models\MouvementStock;
use App\Models\Paiement;
use App\Models\Produit;
use App\Models\Rotation;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DemoComplementSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            $this->creerPaiementsEnAttente();
            $this->mettreMatieresEnAlerte();
            $this->creerPlanningDuJour();
        });
    }

    private function creerPaiementsEnAttente(): void
    {
        $clients = Client::inRandomOrder()->take(5)->get();
        $facturesNonPayees = Facture::where('statut', '!=', 'payee')->get();

        $plans = [
            ['mode' => 'cheque', 'echeance' => 10, 'paiement' => 0, 'prefix' => 'cheque'],
            ['mode' => 'cheque', 'echeance' => 20, 'paiement' => 0, 'prefix' => 'cheque'],
            ['mode' => 'cheque', 'echeance' => -5, 'paiement' => -15, 'prefix' => 'cheque'],
            ['mode' => 'effet', 'echeance' => 30, 'paiement' => 0, 'prefix' => 'effet'],
            ['mode' => 'effet', 'echeance' => 45, 'paiement' => 0, 'prefix' => 'effet'],
        ];

        foreach ($plans as $i => $plan) {
            $client = $clients[$i % $clients->count()];
            $facture = $facturesNonPayees->where('client_id', $client->id)->first();

            $montant = rand(8000, 45000);
            if ($facture) {
                $reste = (float) $facture->resteAPayer();
                if ($reste < 8000) {
                    $facture = null;
                } elseif ($reste < $montant) {
                    $montant = (int) floor($reste * 0.6);
                }
            }

            $reference = $plan['prefix'] === 'cheque'
                ? 'CHQ-'.rand(100000, 999999)
                : 'TRAITE-'.now()->year.'-'.str_pad((string) rand(1, 999), 3, '0', STR_PAD_LEFT);

            Paiement::create([
                'client_id' => $client->id,
                'facture_id' => $facture?->id,
                'date_paiement' => today()->addDays($plan['paiement']),
                'montant' => $montant,
                'mode' => $plan['mode'],
                'reference' => $reference,
                'date_echeance' => today()->addDays($plan['echeance']),
                'statut_encaissement' => 'en_attente',
            ]);

            $facture?->actualiserStatut();
        }
    }

    private function mettreMatieresEnAlerte(): void
    {
        $cibles = [
            'Ciment CPJ45' => 22,
            'Adjuvant plastifiant' => 150,
        ];

        foreach ($cibles as $nom => $nouveauStock) {
            $matiere = MatierePremiere::where('nom', $nom)->first();
            if (! $matiere) {
                continue;
            }

            $consommation = round((float) $matiere->stock_actuel - $nouveauStock, 2);

            if ($consommation > 0) {
                MouvementStock::create([
                    'matiere_premiere_id' => $matiere->id,
                    'type' => 'sortie',
                    'quantite' => $consommation,
                    'date_mouvement' => today()->subDays(rand(1, 3)),
                    'commentaire' => 'Consommation production',
                ]);
            }

            $matiere->update(['stock_actuel' => $nouveauStock]);
        }
    }

    private function creerPlanningDuJour(): void
    {
        $clients = Client::inRandomOrder()->take(4)->get();
        $produits = Produit::whereIn('code', ['B25', 'B30', 'B20'])->get()->keyBy('code');
        $camions = Camion::with('chauffeur')->orderBy('id')->get()->keyBy('id');
        $camion1 = $camions[1];
        $camion2 = $camions[2];
        $camion3 = $camions[3];

        $chantiers = [
            "Chantier Route de l'Ourika, Marrakech",
            'Lotissement Annakhil, Marrakech',
            'Immeuble Avenue Mohammed VI, Guéliz, Marrakech',
            'Résidence Massira III, Marrakech',
        ];

        $commandeA = Commande::create([
            'client_id' => $clients[0]->id,
            'produit_id' => $produits['B25']->id,
            'quantite_m3' => 16,
            'prix_unitaire' => $produits['B25']->prix_m3,
            'montant_total' => round(16 * (float) $produits['B25']->prix_m3, 2),
            'date_livraison' => today()->copy()->setTime(8, 0),
            'adresse_chantier' => $chantiers[0],
            'statut' => 'en_livraison',
        ]);

        $depart2A = now()->subMinutes(100);
        $arrivee1A = $depart2A->copy()->subMinutes(15);
        $depart1A = $arrivee1A->copy()->subMinutes(45);

        $r1A = Rotation::create([
            'commande_id' => $commandeA->id, 'camion_id' => $camion2->id, 'chauffeur_id' => $camion2->chauffeur->id,
            'num_ordre' => 1, 'quantite_m3' => 10,
            'heure_depart' => $depart1A, 'heure_arrivee' => $arrivee1A, 'statut' => 'terminee',
        ]);
        $this->creerBon($r1A, $arrivee1A, 10);

        Rotation::create([
            'commande_id' => $commandeA->id, 'camion_id' => $camion2->id, 'chauffeur_id' => $camion2->chauffeur->id,
            'num_ordre' => 2, 'quantite_m3' => 6,
            'heure_depart' => $depart2A, 'heure_arrivee' => null, 'statut' => 'en_route',
        ]);

        $commandeB = Commande::create([
            'client_id' => $clients[1]->id,
            'produit_id' => $produits['B30']->id,
            'quantite_m3' => 24,
            'prix_unitaire' => $produits['B30']->prix_m3,
            'montant_total' => round(24 * (float) $produits['B30']->prix_m3, 2),
            'date_livraison' => today()->copy()->setTime(9, 30),
            'adresse_chantier' => $chantiers[1],
            'statut' => 'en_livraison',
        ]);

        $depart3B = now()->subMinutes(55);
        $arrivee3B = now()->subMinutes(5);
        $arrivee2B = $depart3B->copy()->subMinutes(15);
        $depart2B = $arrivee2B->copy()->subMinutes(50);
        $arrivee1B = $depart2B->copy()->subMinutes(15);
        $depart1B = $arrivee1B->copy()->subMinutes(45);

        $r1B = Rotation::create([
            'commande_id' => $commandeB->id, 'camion_id' => $camion1->id, 'chauffeur_id' => $camion1->chauffeur->id,
            'num_ordre' => 1, 'quantite_m3' => 8,
            'heure_depart' => $depart1B, 'heure_arrivee' => $arrivee1B, 'statut' => 'terminee',
        ]);
        $this->creerBon($r1B, $arrivee1B, 8);

        $r2B = Rotation::create([
            'commande_id' => $commandeB->id, 'camion_id' => $camion1->id, 'chauffeur_id' => $camion1->chauffeur->id,
            'num_ordre' => 2, 'quantite_m3' => 8,
            'heure_depart' => $depart2B, 'heure_arrivee' => $arrivee2B, 'statut' => 'terminee',
        ]);
        $this->creerBon($r2B, $arrivee2B, 8);

        Rotation::create([
            'commande_id' => $commandeB->id, 'camion_id' => $camion1->id, 'chauffeur_id' => $camion1->chauffeur->id,
            'num_ordre' => 3, 'quantite_m3' => 8,
            'heure_depart' => $depart3B, 'heure_arrivee' => $arrivee3B, 'statut' => 'sur_chantier',
        ]);

        $commandeC = Commande::create([
            'client_id' => $clients[2]->id,
            'produit_id' => $produits['B20']->id,
            'quantite_m3' => 18,
            'prix_unitaire' => $produits['B20']->prix_m3,
            'montant_total' => round(18 * (float) $produits['B20']->prix_m3, 2),
            'date_livraison' => today()->copy()->setTime(11, 0),
            'adresse_chantier' => $chantiers[2],
            'statut' => 'en_livraison',
        ]);

        $depart1C = now()->subMinutes(rand(150, 210));
        $arrivee1C = $depart1C->copy()->addMinutes(rand(40, 75));

        $r1C = Rotation::create([
            'commande_id' => $commandeC->id, 'camion_id' => $camion3->id, 'chauffeur_id' => $camion3->chauffeur->id,
            'num_ordre' => 1, 'quantite_m3' => 8,
            'heure_depart' => $depart1C, 'heure_arrivee' => $arrivee1C, 'statut' => 'terminee',
        ]);
        $this->creerBon($r1C, $arrivee1C, 8);

        Rotation::create([
            'commande_id' => $commandeC->id, 'camion_id' => $camion3->id, 'chauffeur_id' => $camion3->chauffeur->id,
            'num_ordre' => 2, 'quantite_m3' => 8,
            'heure_depart' => null, 'heure_arrivee' => null, 'statut' => 'chargement',
        ]);
        Rotation::create([
            'commande_id' => $commandeC->id, 'camion_id' => $camion3->id, 'chauffeur_id' => $camion3->chauffeur->id,
            'num_ordre' => 3, 'quantite_m3' => 2,
            'heure_depart' => null, 'heure_arrivee' => null, 'statut' => 'planifiee',
        ]);

        $commandeD = Commande::create([
            'client_id' => $clients[3]->id,
            'produit_id' => $produits['B25']->id,
            'quantite_m3' => 14,
            'prix_unitaire' => $produits['B25']->prix_m3,
            'montant_total' => round(14 * (float) $produits['B25']->prix_m3, 2),
            'date_livraison' => today()->copy()->setTime(14, 0),
            'adresse_chantier' => $chantiers[3],
            'statut' => 'planifiee',
        ]);

        Rotation::create([
            'commande_id' => $commandeD->id, 'camion_id' => $camion1->id, 'chauffeur_id' => $camion1->chauffeur->id,
            'num_ordre' => 1, 'quantite_m3' => 8,
            'heure_depart' => null, 'heure_arrivee' => null, 'statut' => 'planifiee',
        ]);
        Rotation::create([
            'commande_id' => $commandeD->id, 'camion_id' => $camion1->id, 'chauffeur_id' => $camion1->chauffeur->id,
            'num_ordre' => 2, 'quantite_m3' => 6,
            'heure_depart' => null, 'heure_arrivee' => null, 'statut' => 'planifiee',
        ]);

        $camion1->update(['etat' => 'en_mission']);
        $camion2->update(['etat' => 'en_mission']);
        $camion3->update(['etat' => 'disponible']);
    }

    private function creerBon(Rotation $rotation, Carbon $dateEmission, float $quantite): void
    {
        BonLivraison::create([
            'rotation_id' => $rotation->id,
            'numero' => BonLivraison::genererNumero(),
            'date_emission' => $dateEmission->toDateString(),
            'quantite_livree' => $quantite,
            'signe' => rand(1, 10) <= 7,
        ]);
    }
}
