<?php

namespace App\Http\Controllers;

use App\Models\BonLivraison;
use App\Models\Camion;
use App\Models\Chauffeur;
use App\Models\MouvementStock;
use App\Models\Rotation;
use Illuminate\Http\Request;

class RotationController extends Controller
{
    public function planning(Request $request)
    {
        $date = $request->date ?: today()->toDateString();

        $rotations = Rotation::with(['commande.client', 'commande.produit', 'camion', 'chauffeur', 'bonLivraison'])
            ->whereHas('commande', fn ($q) => $q->whereDate('date_livraison', $date))
            ->orderBy('camion_id')->orderBy('num_ordre')->get()
            ->groupBy(fn ($r) => $r->camion->matricule);

        $camions = Camion::orderBy('matricule')->get();
        $chauffeurs = Chauffeur::orderBy('nom')->get();

        return view('rotations.planning', compact('rotations', 'date', 'camions', 'chauffeurs'));
    }

    public function updateAffectation(Request $request, Rotation $rotation)
    {
        if ($rotation->statut !== 'planifiee') {
            return back()->with('error', 'Seules les rotations planifiées peuvent être réaffectées.');
        }

        $data = $request->validate([
            'camion_id' => ['required', 'exists:camions,id'],
            'chauffeur_id' => ['required', 'exists:chauffeurs,id'],
        ]);

        $rotation->update($data);

        return back()->with('success', 'Affectation de la rotation modifiée.');
    }

    public function updateStatut(Request $request, Rotation $rotation)
    {
        $data = $request->validate([
            'statut' => ['required', 'in:planifiee,chargement,en_route,sur_chantier,terminee'],
        ]);

        $statut = $data['statut'];

        if ($statut === 'en_route' && ! $rotation->heure_depart) {
            $rotation->heure_depart = now();
            $rotation->camion->update(['etat' => 'en_mission']);
        }

        if ($statut === 'sur_chantier' && ! $rotation->heure_arrivee) {
            $rotation->heure_arrivee = now();
        }

        $rotation->statut = $statut;
        $rotation->save();

        if ($statut === 'terminee') {
            $rotation->camion->update(['etat' => 'disponible']);

            if (! $rotation->bonLivraison) {
                BonLivraison::create([
                    'rotation_id' => $rotation->id,
                    'numero' => BonLivraison::genererNumero(),
                    'date_emission' => today(),
                    'quantite_livree' => $rotation->quantite_m3,
                ]);
            }

            $this->consommerMatieres($rotation);

            $commande = $rotation->commande;
            $toutesTerminees = $commande->rotations()->where('statut', '!=', 'terminee')->doesntExist();
            $commande->update(['statut' => $toutesTerminees ? 'livree' : 'en_livraison']);
        }

        return back()->with('success', 'Statut de la rotation mis à jour.');
    }

    private function consommerMatieres(Rotation $rotation): void
    {
        if (MouvementStock::where('rotation_id', $rotation->id)->exists()) {
            return;
        }

        $produit = $rotation->commande->produit;

        foreach ($produit->formulations as $formulation) {
            $quantite = round((float) $formulation->quantite_par_m3 * (float) $rotation->quantite_m3, 2);

            MouvementStock::create([
                'matiere_premiere_id' => $formulation->matiere_premiere_id,
                'type' => 'sortie',
                'quantite' => $quantite,
                'date_mouvement' => today(),
                'rotation_id' => $rotation->id,
                'commentaire' => 'Production rotation BL',
            ]);

            $formulation->matierePremiere->decrement('stock_actuel', $quantite);
        }
    }
}
