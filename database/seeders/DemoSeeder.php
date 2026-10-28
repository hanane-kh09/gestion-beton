<?php

namespace Database\Seeders;

use App\Models\BonLivraison;
use App\Models\Camion;
use App\Models\Client;
use App\Models\Commande;
use App\Models\Facture;
use App\Models\Paiement;
use App\Models\Produit;
use App\Models\Rotation;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DemoSeeder extends Seeder
{
    /**
     * Index (0-based, dans l'ordre de creerClients) des 8 clients qui recoivent
     * une facture, avec la repartition demandee : 3 payees, 2 partielles, 3 impayees.
     */
    private const PLAN_FACTURATION = [
        ['categorie' => 'payee', 'totalFraction' => 1.0, 'n' => 3],
        ['categorie' => 'partielle', 'totalFraction' => 0.5, 'n' => 3],
        ['categorie' => 'impayee', 'totalFraction' => 0.0, 'n' => 0],
        ['categorie' => 'impayee', 'totalFraction' => 0.0, 'n' => 0],
        ['categorie' => 'payee', 'totalFraction' => 1.0, 'n' => 2],
        ['categorie' => 'partielle', 'totalFraction' => 0.5, 'n' => 2],
        ['categorie' => 'payee', 'totalFraction' => 1.0, 'n' => 2],
        ['categorie' => 'impayee', 'totalFraction' => 0.0, 'n' => 0],
    ];

    public function run(): void
    {
        DB::transaction(function () {
            Paiement::query()->delete();
            Facture::query()->delete();
            BonLivraison::query()->delete();
            Rotation::query()->delete();
            Commande::query()->delete();
            Client::query()->delete();

            $clients = $this->creerClients();
            $produits = Produit::whereIn('code', ['B15', 'B20', 'B25', 'B30', 'B35'])->get();
            $camions = Camion::with('chauffeur')->orderBy('id')->get();

            [$commandesLivrees, $commandesEnLivraison] = $this->creerCommandes($clients, $produits);

            $pairCounter = 0;
            foreach ($commandesLivrees as $commande) {
                $this->creerRotationsLivree($commande, $camions, $pairCounter);
                $pairCounter++;
            }
            foreach ($commandesEnLivraison as $info) {
                $this->creerRotationsEnLivraison($info['commande'], $camions, $pairCounter, $info['retard']);
                $pairCounter++;
            }

            $this->creerFactures(array_slice($clients, 0, 8));
        });
    }

    /**
     * @return Client[]
     */
    private function creerClients(): array
    {
        $definitions = [
            ['type' => 'entreprise', 'nom' => 'STE ATLAS IMMOBILIER', 'ice' => '001234567000018', 'telephone' => '0661122334', 'email' => 'contact@atlasimmobilier.ma', 'adresse' => 'Avenue Mohammed VI, Guéliz, Marrakech'],
            ['type' => 'particulier', 'nom' => 'M. Youssef Bennani', 'ice' => null, 'telephone' => '0662233445', 'email' => 'youssef.bennani@gmail.com', 'adresse' => 'Rue Ibn Toumert, Targa, Marrakech'],
            ['type' => 'promoteur', 'nom' => 'PROMOTEUR AL BARAKA', 'ice' => '001345678000029', 'telephone' => '0663344556', 'email' => 'contact@albaraka-immo.ma', 'adresse' => 'Route de Fès, Marrakech'],
            ['type' => 'entreprise', 'nom' => 'STE MARRAKECH BTP', 'ice' => '001456789000030', 'telephone' => '0664455667', 'email' => 'info@marrakechbtp.ma', 'adresse' => 'Zone Industrielle Sidi Ghanem, Marrakech'],
            ['type' => 'particulier', 'nom' => 'M. Ahmed El Fassi', 'ice' => null, 'telephone' => '0665566778', 'email' => 'ahmed.elfassi@gmail.com', 'adresse' => "Quartier M'hamid, Marrakech"],
            ['type' => 'entreprise', 'nom' => 'GROUPE RIAD CONSTRUCTION', 'ice' => '001567890000041', 'telephone' => '0666677889', 'email' => 'contact@riadconstruction.ma', 'adresse' => "Route de l'Ourika, Marrakech"],
            ['type' => 'particulier', 'nom' => 'Mme Fatima Zahra Idrissi', 'ice' => null, 'telephone' => '0667788990', 'email' => 'fz.idrissi@gmail.com', 'adresse' => 'Quartier Sidi Youssef Ben Ali, Marrakech'],
            ['type' => 'promoteur', 'nom' => 'PROMOTEUR DAR AL MAGHRIB', 'ice' => '001678901000052', 'telephone' => '0668899001', 'email' => 'contact@daralmaghrib.ma', 'adresse' => 'Avenue Abdelkrim Al Khattabi, Marrakech'],
            ['type' => 'entreprise', 'nom' => 'STE SAHARA TRAVAUX', 'ice' => '001789012000063', 'telephone' => '0669900112', 'email' => 'contact@saharatravaux.ma', 'adresse' => 'Route de Safi, Marrakech'],
            ['type' => 'particulier', 'nom' => 'M. Karim Ouahbi', 'ice' => null, 'telephone' => '0660011223', 'email' => 'karim.ouahbi@gmail.com', 'adresse' => 'Quartier Massira, Marrakech'],
            ['type' => 'entreprise', 'nom' => 'STE ATLAS INFRA', 'ice' => '001890123000074', 'telephone' => '0661223344', 'email' => 'contact@atlasinfra.ma', 'adresse' => "Route d'Amizmiz, Marrakech"],
            ['type' => 'promoteur', 'nom' => 'PROMOTEUR IMMO PALMERAIE', 'ice' => '001901234000085', 'telephone' => '0662334455', 'email' => 'contact@immopalmeraie.ma', 'adresse' => 'Route de Fès, Palmeraie, Marrakech'],
        ];

        return array_map(fn ($d) => Client::create($d), $definitions);
    }

    /**
     * @param  Client[]  $clients
     * @return array{0: Commande[], 1: array{commande: Commande, retard: bool}[]}
     */
    private function creerCommandes(array $clients, \Illuminate\Support\Collection $produits): array
    {
        $chantiers = [
            'Lot 45, Zone Industrielle Sidi Ghanem, Marrakech',
            "Villa en construction, Route de l'Ourika, Marrakech",
            'Immeuble R+4, Avenue Mohammed VI, Guéliz, Marrakech',
            'Chantier Résidence Al Andalous, Targa, Marrakech',
            'Lotissement Annakhil, Marrakech',
            'Chantier Route de Fès, Marrakech',
            "Villa Route d'Amizmiz, Marrakech",
            "Chantier Quartier M'hamid, Marrakech",
            'Immeuble Sidi Youssef Ben Ali, Marrakech',
            'Chantier Massira III, Marrakech',
            'Résidence Palmeraie, Marrakech',
            'Chantier Route de Safi, Marrakech',
        ];
        $minutes = [0, 15, 30, 45];

        // client_index => nombre de commandes livrees (8 clients factures : 4x2 + 4x1 = 12)
        $planLivrees = [0 => 2, 1 => 2, 2 => 2, 3 => 2, 4 => 1, 5 => 1, 6 => 1, 7 => 1];
        $planEnLivraison = [8, 9, 10, 11];
        $planAutres = [
            ['statut' => 'planifiee', 'client' => 0],
            ['statut' => 'planifiee', 'client' => 5],
            ['statut' => 'planifiee', 'client' => 9],
            ['statut' => 'planifiee', 'client' => 11],
            ['statut' => 'confirmee', 'client' => 2],
            ['statut' => 'confirmee', 'client' => 6],
            ['statut' => 'confirmee', 'client' => 10],
            ['statut' => 'en_attente', 'client' => 3],
            ['statut' => 'en_attente', 'client' => 7],
        ];

        $commandesLivrees = [];
        $commandesEnLivraison = [];

        foreach ($planLivrees as $clientIndex => $nb) {
            for ($i = 0; $i < $nb; $i++) {
                $joursAvant = rand(5, 182);
                $date = now()->subDays($joursAvant)->setTime(rand(7, 14), $minutes[array_rand($minutes)]);
                $produit = $produits->random();
                $quantite = rand(6, 60);

                $commandesLivrees[] = Commande::create([
                    'client_id' => $clients[$clientIndex]->id,
                    'produit_id' => $produit->id,
                    'quantite_m3' => $quantite,
                    'prix_unitaire' => $produit->prix_m3,
                    'montant_total' => round($quantite * (float) $produit->prix_m3, 2),
                    'date_livraison' => $date,
                    'adresse_chantier' => $chantiers[array_rand($chantiers)],
                    'statut' => 'livree',
                ]);
            }
        }

        foreach ($planEnLivraison as $i => $clientIndex) {
            $produit = $produits->random();
            $quantite = rand(6, 60);
            $date = today()->copy()->addHours(8 + $i);

            $commande = Commande::create([
                'client_id' => $clients[$clientIndex]->id,
                'produit_id' => $produit->id,
                'quantite_m3' => $quantite,
                'prix_unitaire' => $produit->prix_m3,
                'montant_total' => round($quantite * (float) $produit->prix_m3, 2),
                'date_livraison' => $date,
                'adresse_chantier' => $chantiers[array_rand($chantiers)],
                'statut' => 'en_livraison',
            ]);

            // 3 des 4 commandes en cours ont une rotation bloquee depuis plus de 90 min
            // (alerte de delai), la derniere est en cours normalement.
            $commandesEnLivraison[] = ['commande' => $commande, 'retard' => $i < 3];
        }

        foreach ($planAutres as $plan) {
            $produit = $produits->random();
            $quantite = rand(6, 60);
            $date = today()->copy()->addDays(rand(1, 20))->setTime(rand(7, 14), $minutes[array_rand($minutes)]);

            Commande::create([
                'client_id' => $clients[$plan['client']]->id,
                'produit_id' => $produit->id,
                'quantite_m3' => $quantite,
                'prix_unitaire' => $produit->prix_m3,
                'montant_total' => round($quantite * (float) $produit->prix_m3, 2),
                'date_livraison' => $date,
                'adresse_chantier' => $chantiers[array_rand($chantiers)],
                'statut' => $plan['statut'],
            ]);
        }

        return [$commandesLivrees, $commandesEnLivraison];
    }

    private function creerRotationsLivree(Commande $commande, \Illuminate\Support\Collection $camions, int $pairIndex): void
    {
        $camion = $camions[$pairIndex % $camions->count()];
        $chauffeur = $camion->chauffeur;
        $capacite = (float) $camion->capacite_m3;
        $n = (int) ceil((float) $commande->quantite_m3 / $capacite);
        $remaining = (float) $commande->quantite_m3;
        $depart = $commande->date_livraison->copy();

        for ($ordre = 1; $ordre <= $n; $ordre++) {
            $chunk = min($capacite, $remaining);
            $remaining -= $chunk;
            $duree = rand(35, 80);
            $arrivee = $depart->copy()->addMinutes($duree);

            $rotation = Rotation::create([
                'commande_id' => $commande->id,
                'camion_id' => $camion->id,
                'chauffeur_id' => $chauffeur->id,
                'num_ordre' => $ordre,
                'quantite_m3' => $chunk,
                'heure_depart' => $depart->copy(),
                'heure_arrivee' => $arrivee,
                'statut' => 'terminee',
            ]);

            $this->creerBon($rotation, $arrivee, $chunk);

            $depart = $arrivee->copy()->addMinutes(rand(10, 20));
        }
    }

    private function creerRotationsEnLivraison(Commande $commande, \Illuminate\Support\Collection $camions, int $pairIndex, bool $retard): void
    {
        $camion = $camions[$pairIndex % $camions->count()];
        $chauffeur = $camion->chauffeur;
        $capacite = (float) $camion->capacite_m3;
        $n = (int) ceil((float) $commande->quantite_m3 / $capacite);

        $remaining = (float) $commande->quantite_m3;
        $chunks = [];
        for ($ordre = 1; $ordre <= $n; $ordre++) {
            $chunk = min($capacite, $remaining);
            $remaining -= $chunk;
            $chunks[$ordre] = $chunk;
        }

        // La derniere rotation est en cours (pas encore arrivee) ; on remonte le temps
        // pour reconstituer les rotations precedentes deja terminees.
        $dureeEnCours = $retard ? rand(96, 130) : rand(20, 70);
        $depart = now()->subMinutes($dureeEnCours);

        $infos = [$n => ['depart' => $depart->copy(), 'arrivee' => null]];
        for ($ordre = $n - 1; $ordre >= 1; $ordre--) {
            $arrivee = $depart->copy()->subMinutes(rand(10, 20));
            $duree = rand(35, 80);
            $depart = $arrivee->copy()->subMinutes($duree);
            $infos[$ordre] = ['depart' => $depart->copy(), 'arrivee' => $arrivee];
        }
        ksort($infos);

        foreach ($infos as $ordre => $info) {
            $estDerniere = $ordre === $n;
            $statut = $estDerniere
                ? ($retard ? 'en_route' : (rand(0, 1) === 1 ? 'en_route' : 'sur_chantier'))
                : 'terminee';

            $rotation = Rotation::create([
                'commande_id' => $commande->id,
                'camion_id' => $camion->id,
                'chauffeur_id' => $chauffeur->id,
                'num_ordre' => $ordre,
                'quantite_m3' => $chunks[$ordre],
                'heure_depart' => $info['depart'],
                'heure_arrivee' => $info['arrivee'],
                'statut' => $statut,
            ]);

            if ($statut === 'terminee') {
                $this->creerBon($rotation, $info['arrivee'], $chunks[$ordre]);
            }
        }
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

    /**
     * @param  Client[]  $clientsAFacturer
     */
    private function creerFactures(array $clientsAFacturer): void
    {
        foreach ($clientsAFacturer as $index => $client) {
            $bons = BonLivraison::whereHas('rotation.commande', function ($q) use ($client) {
                $q->where('client_id', $client->id)->where('statut', 'livree');
            })->with('rotation.commande')->get();

            if ($bons->isEmpty()) {
                continue;
            }

            $montantHt = round($bons->sum(fn ($b) => (float) $b->quantite_livree * (float) $b->rotation->commande->prix_unitaire), 2);
            $tva = round($montantHt * 0.20, 2);
            $montantTtc = round($montantHt + $tva, 2);
            $dateEmission = $bons->max(fn ($b) => $b->date_emission)->copy()->addDay();

            $facture = Facture::create([
                'client_id' => $client->id,
                'numero' => Facture::genererNumero(),
                'date_emission' => $dateEmission,
                'date_echeance' => $dateEmission->copy()->addDays(30),
                'montant_ht' => $montantHt,
                'tva' => $tva,
                'montant_ttc' => $montantTtc,
                'statut' => 'impayee',
            ]);

            BonLivraison::whereIn('id', $bons->pluck('id'))->update(['facture_id' => $facture->id]);

            $plan = self::PLAN_FACTURATION[$index];
            $this->creerPaiementsPourFacture($facture, $client, $plan['totalFraction'], $plan['n'], $dateEmission);

            $facture->refresh();
            $facture->actualiserStatut();
        }
    }

    private function creerPaiementsPourFacture(Facture $facture, Client $client, float $totalFraction, int $n, Carbon $dateEmission): void
    {
        if ($n <= 0) {
            return;
        }

        $montants = $this->repartirMontant(round((float) $facture->montant_ttc * $totalFraction, 2), $n);
        $modes = ['especes', 'cheque', 'virement', 'effet'];
        $datePaiement = $dateEmission->copy()->addDays(rand(3, 8));

        foreach ($montants as $montant) {
            $mode = $modes[array_rand($modes)];
            $reference = in_array($mode, ['cheque', 'virement'], true)
                ? strtoupper(substr($mode, 0, 3)).'-'.rand(100000, 999999)
                : null;

            $date = $datePaiement->greaterThan(now()) ? now()->copy() : $datePaiement->copy();

            Paiement::create([
                'client_id' => $client->id,
                'facture_id' => $facture->id,
                'date_paiement' => $date,
                'montant' => $montant,
                'mode' => $mode,
                'reference' => $reference,
            ]);

            $datePaiement = $datePaiement->copy()->addDays(rand(5, 15));
        }
    }

    /**
     * Repartit un montant en $n parts dont la somme vaut exactement $total
     * (travail en centimes pour eviter les ecarts d'arrondi).
     *
     * @return float[]
     */
    private function repartirMontant(float $total, int $n): array
    {
        $centimes = (int) round($total * 100);
        $base = intdiv($centimes, $n);
        $reste = $centimes % $n;
        $parts = [];
        for ($i = 0; $i < $n; $i++) {
            $parts[] = ($base + ($i < $reste ? 1 : 0)) / 100;
        }

        return $parts;
    }
}
