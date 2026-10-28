<?php

namespace App\Http\Controllers;

use App\Models\Camion;
use App\Models\Client;
use App\Models\Commande;
use App\Models\Facture;
use App\Models\MatierePremiere;
use App\Models\Paiement;
use App\Models\Rotation;

class DashboardController extends Controller
{
    public function index()
    {
        $annee = now()->year;

        $caParMois = array_fill(1, 12, 0.0);
        foreach (Facture::whereYear('date_emission', $annee)->get() as $f) {
            $caParMois[(int) $f->date_emission->format('n')] += (float) $f->montant_ttc;
        }

        $ventesParProduit = [];
        foreach (Commande::with('produit')->where('statut', '!=', 'annulee')->get() as $c) {
            $code = $c->produit->code;
            $ventesParProduit[$code] = ($ventesParProduit[$code] ?? 0) + (float) $c->quantite_m3;
        }

        $totalCamions = Camion::count();
        $camionsActifs = Camion::where('etat', 'en_mission')->count();

        $chequesEffetsEnAttente = Paiement::whereIn('mode', ['cheque', 'effet'])->where('statut_encaissement', 'en_attente');

        $stats = [
            'clients' => Client::count(),
            'commandesEnCours' => Commande::whereIn('statut', ['en_attente', 'confirmee', 'planifiee', 'en_livraison'])->count(),
            'caAnnee' => Facture::whereYear('date_emission', $annee)->sum('montant_ttc'),
            'impayes' => Facture::where('statut', '!=', 'payee')->get()->sum(fn ($f) => $f->resteAPayer()),
            'tauxFlotte' => $totalCamions > 0 ? round($camionsActifs / $totalCamions * 100) : 0,
            'chequesEnAttenteNb' => (clone $chequesEffetsEnAttente)->count(),
            'chequesEnAttenteMontant' => (clone $chequesEffetsEnAttente)->sum('montant'),
        ];

        $matieresEnAlerte = MatierePremiere::all()->filter->enAlerte()->values();

        $rotationsJour = Rotation::with(['commande.client', 'camion', 'chauffeur'])
            ->whereHas('commande', fn ($q) => $q->whereDate('date_livraison', today()))
            ->orderBy('num_ordre')->get();

        $facturesImpayees = Facture::with('client')->where('statut', '!=', 'payee')
            ->orderBy('date_echeance')->take(8)->get();

        return view('dashboard', compact('stats', 'caParMois', 'ventesParProduit', 'rotationsJour', 'facturesImpayees', 'matieresEnAlerte'));
    }
}
