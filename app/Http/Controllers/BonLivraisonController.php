<?php

namespace App\Http\Controllers;

use App\Models\BonLivraison;
use App\Models\Facture;
use Illuminate\Http\Request;

class BonLivraisonController extends Controller
{
    public function index(Request $request)
    {
        $bons = BonLivraison::with(['rotation.commande.client', 'rotation.camion', 'facture'])
            ->when($request->q, fn ($q, $v) => $q->where('numero', 'like', "%$v%")
                ->orWhereHas('rotation.commande.client', fn ($w) => $w->where('nom', 'like', "%$v%")))
            ->orderByDesc('id')->paginate(15)->withQueryString();

        return view('bons.index', compact('bons'));
    }

    public function show(BonLivraison $bon)
    {
        $bon->load(['rotation.commande.client', 'rotation.commande.produit', 'rotation.camion', 'rotation.chauffeur']);

        return view('bons.show', compact('bon'));
    }

    public function imprimer(BonLivraison $bon)
    {
        $bon->load(['rotation.commande.client', 'rotation.commande.produit', 'rotation.camion', 'rotation.chauffeur']);

        return view('bons.print', compact('bon'));
    }

    public function signer(BonLivraison $bon)
    {
        $bon->update(['signe' => true]);

        return back()->with('success', 'Bon de livraison marqué comme signé.');
    }

    public function edit(BonLivraison $bon)
    {
        $bon->load(['rotation.commande.client', 'rotation.commande.produit', 'facture']);

        return view('bons.edit', compact('bon'));
    }

    public function update(Request $request, BonLivraison $bon)
    {
        $data = $request->validate([
            'date_emission' => ['required', 'date'],
            'quantite_livree' => ['required', 'numeric', 'min:0.01'],
        ]);
        $data['signe'] = $request->boolean('signe');

        $bon->update($data);

        if ($bon->facture_id) {
            $this->recalculerFacture($bon->facture);
        }

        return redirect()->route('bons.show', $bon)->with('success', 'Bon de livraison modifié avec succès.');
    }

    private function recalculerFacture(Facture $facture): void
    {
        $facture->load('bonsLivraison.rotation.commande');
        $ht = round($facture->bonsLivraison->sum(fn ($b) => $b->montant()), 2);
        $tva = round($ht * 0.20, 2);

        $facture->update([
            'montant_ht' => $ht,
            'tva' => $tva,
            'montant_ttc' => round($ht + $tva, 2),
        ]);

        $facture->actualiserStatut();
    }
}
