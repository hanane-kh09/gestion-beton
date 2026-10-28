<?php

namespace App\Http\Controllers;

use App\Models\BonLivraison;
use App\Models\Client;
use App\Models\Facture;
use Illuminate\Http\Request;

class FactureController extends Controller
{
    public function index(Request $request)
    {
        $factures = Facture::with('client')
            ->when($request->statut, fn ($q, $v) => $q->where('statut', $v))
            ->when($request->q, fn ($q, $v) => $q->where('numero', 'like', "%$v%")
                ->orWhereHas('client', fn ($w) => $w->where('nom', 'like', "%$v%")))
            ->orderByDesc('id')->paginate(15)->withQueryString();

        return view('factures.index', compact('factures'));
    }

    public function create(Request $request)
    {
        $clients = Client::orderBy('nom')->get();
        $client = $request->client_id ? Client::find($request->client_id) : null;

        $bons = $client
            ? BonLivraison::with(['rotation.commande.produit'])
                ->whereNull('facture_id')
                ->whereHas('rotation.commande', fn ($q) => $q->where('client_id', $client->id))
                ->orderBy('id')->get()
            : collect();

        return view('factures.create', compact('clients', 'client', 'bons'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'client_id' => ['required', 'exists:clients,id'],
            'bons' => ['required', 'array', 'min:1'],
            'bons.*' => ['exists:bons_livraison,id'],
            'date_echeance' => ['nullable', 'date'],
        ]);

        $bons = BonLivraison::with('rotation.commande')
            ->whereIn('id', $data['bons'])->whereNull('facture_id')->get();

        if ($bons->isEmpty()) {
            return back()->with('error', 'Aucun bon de livraison valide sélectionné.');
        }

        $ht = round($bons->sum(fn ($b) => $b->montant()), 2);
        $tva = round($ht * 0.20, 2);

        $facture = Facture::create([
            'client_id' => $data['client_id'],
            'numero' => Facture::genererNumero(),
            'date_emission' => today(),
            'date_echeance' => $data['date_echeance'] ?? today()->addDays(30),
            'montant_ht' => $ht,
            'tva' => $tva,
            'montant_ttc' => $ht + $tva,
        ]);

        BonLivraison::whereIn('id', $bons->pluck('id'))->update(['facture_id' => $facture->id]);

        return redirect()->route('factures.show', $facture)->with('success', 'Facture générée avec succès.');
    }

    public function show(Facture $facture)
    {
        $facture->load(['client', 'bonsLivraison.rotation.commande.produit', 'paiements']);

        return view('factures.show', compact('facture'));
    }

    public function imprimer(Facture $facture)
    {
        $facture->load(['client', 'bonsLivraison.rotation.commande.produit']);

        return view('factures.print', compact('facture'));
    }

    public function destroy(Facture $facture)
    {
        if ($facture->paiements()->exists()) {
            return redirect()->route('factures.index')->with('error', 'Impossible : des paiements sont liés à cette facture.');
        }
        $facture->bonsLivraison()->update(['facture_id' => null]);
        $facture->delete();

        return redirect()->route('factures.index')->with('success', 'Facture supprimée.');
    }

    public function edit(Facture $facture)
    {
        return view('factures.edit', compact('facture'));
    }

    public function update(Request $request, Facture $facture)
    {
        $data = $request->validate([
            'date_emission' => ['required', 'date'],
            'date_echeance' => ['nullable', 'date'],
        ]);

        $facture->update($data);

        return redirect()->route('factures.show', $facture)->with('success', 'Facture modifiée avec succès.');
    }

    public function retirerBon(Facture $facture, BonLivraison $bon)
    {
        if ($bon->facture_id !== $facture->id) {
            abort(404);
        }

        $bon->update(['facture_id' => null]);
        $facture->load('bonsLivraison.rotation.commande');

        if ($facture->bonsLivraison->isEmpty()) {
            $facture->delete();

            return redirect()->route('factures.index')->with('success', 'Bon retiré : la facture ne contenait plus aucun bon et a été supprimée.');
        }

        $ht = round($facture->bonsLivraison->sum(fn ($b) => $b->montant()), 2);
        $tva = round($ht * 0.20, 2);

        $facture->update([
            'montant_ht' => $ht,
            'tva' => $tva,
            'montant_ttc' => round($ht + $tva, 2),
        ]);
        $facture->actualiserStatut();

        return redirect()->route('factures.show', $facture)->with('success', 'Bon de livraison retiré de la facture.');
    }
}
