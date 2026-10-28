<?php

namespace App\Http\Controllers;

use App\Models\Camion;
use App\Models\Chauffeur;
use App\Models\Client;
use App\Models\Commande;
use App\Models\Produit;
use App\Models\Rotation;
use Illuminate\Http\Request;

class CommandeController extends Controller
{
    public function index(Request $request)
    {
        $commandes = Commande::with(['client', 'produit'])
            ->when($request->statut, fn ($q, $v) => $q->where('statut', $v))
            ->when($request->q, fn ($q, $v) => $q->whereHas('client', fn ($w) => $w->where('nom', 'like', "%$v%")))
            ->orderByDesc('date_livraison')->paginate(15)->withQueryString();

        return view('commandes.index', compact('commandes'));
    }

    public function create()
    {
        $clients = Client::orderBy('nom')->get();
        $produits = Produit::where('actif', true)->orderBy('code')->get();

        return view('commandes.create', compact('clients', 'produits'));
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $produit = Produit::findOrFail($data['produit_id']);
        $data['prix_unitaire'] = $produit->prix_m3;
        $data['montant_total'] = round($produit->prix_m3 * $data['quantite_m3'], 2);

        $commande = Commande::create($data);

        return redirect()->route('commandes.show', $commande)->with('success', 'Commande créée avec succès.');
    }

    public function show(Commande $commande)
    {
        $commande->load(['client', 'produit', 'rotations.camion', 'rotations.chauffeur', 'rotations.bonLivraison']);

        return view('commandes.show', compact('commande'));
    }

    public function edit(Commande $commande)
    {
        $clients = Client::orderBy('nom')->get();
        $produits = Produit::where('actif', true)->orderBy('code')->get();

        return view('commandes.edit', compact('commande', 'clients', 'produits'));
    }

    public function update(Request $request, Commande $commande)
    {
        $data = $this->validated($request);
        $produit = Produit::findOrFail($data['produit_id']);
        $data['prix_unitaire'] = $produit->prix_m3;
        $data['montant_total'] = round($produit->prix_m3 * $data['quantite_m3'], 2);

        $commande->update($data);

        return redirect()->route('commandes.show', $commande)->with('success', 'Commande modifiée avec succès.');
    }

    public function destroy(Commande $commande)
    {
        if ($commande->rotations()->whereNot('statut', 'planifiee')->exists()) {
            return redirect()->route('commandes.index')->with('error', 'Impossible : des livraisons sont déjà en cours.');
        }
        $commande->delete();

        return redirect()->route('commandes.index')->with('success', 'Commande supprimée.');
    }

    public function confirmer(Commande $commande)
    {
        $commande->update(['statut' => 'confirmee']);

        return back()->with('success', 'Commande confirmée.');
    }

    public function annuler(Commande $commande)
    {
        $commande->update(['statut' => 'annulee']);

        return back()->with('success', 'Commande annulée.');
    }

    public function planifier(Commande $commande)
    {
        $jour = $commande->date_livraison->toDateString();
        $camions = Camion::where('etat', '!=', 'en_panne')->where('etat', '!=', 'maintenance')
            ->withCount(['rotations as rotations_jour' => fn ($q) => $q
                ->whereHas('commande', fn ($w) => $w->whereDate('date_livraison', $jour))])
            ->orderBy('matricule')->get();
        $chauffeurs = Chauffeur::orderBy('nom')->get();

        return view('commandes.planifier', compact('commande', 'camions', 'chauffeurs'));
    }

    public function storePlanification(Request $request, Commande $commande)
    {
        $data = $request->validate([
            'camion_id' => ['required', 'exists:camions,id'],
            'chauffeur_id' => ['required', 'exists:chauffeurs,id'],
        ]);

        $camion = Camion::findOrFail($data['camion_id']);
        $capacite = (float) $camion->capacite_m3;
        $reste = (float) $commande->quantite_m3;
        $nb = (int) ceil($reste / $capacite);

        $commande->rotations()->delete();

        for ($i = 1; $i <= $nb; $i++) {
            $qte = min($capacite, $reste);
            Rotation::create([
                'commande_id' => $commande->id,
                'camion_id' => $camion->id,
                'chauffeur_id' => $data['chauffeur_id'],
                'num_ordre' => $i,
                'quantite_m3' => round($qte, 2),
                'statut' => 'planifiee',
            ]);
            $reste -= $qte;
        }

        $commande->update(['statut' => 'planifiee']);

        return redirect()->route('commandes.show', $commande)
            ->with('success', "Commande planifiée : $nb rotation(s) créée(s).");
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'client_id' => ['required', 'exists:clients,id'],
            'produit_id' => ['required', 'exists:produits,id'],
            'quantite_m3' => ['required', 'numeric', 'min:0.5'],
            'date_livraison' => ['required', 'date'],
            'adresse_chantier' => ['required', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);
    }
}
