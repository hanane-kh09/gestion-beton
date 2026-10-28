<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Facture;
use App\Models\Paiement;
use Illuminate\Http\Request;

class PaiementController extends Controller
{
    public function index(Request $request)
    {
        $paiements = Paiement::with(['client', 'facture'])
            ->when($request->q, fn ($q, $v) => $q->whereHas('client', fn ($w) => $w->where('nom', 'like', "%$v%")))
            ->orderByDesc('date_paiement')->paginate(15)->withQueryString();

        return view('paiements.index', compact('paiements'));
    }

    public function create(Request $request)
    {
        $clients = Client::orderBy('nom')->get();
        $factures = $request->client_id
            ? Facture::where('client_id', $request->client_id)->where('statut', '!=', 'payee')->get()
            : collect();

        return view('paiements.create', compact('clients', 'factures'));
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);

        $paiement = Paiement::create($data);

        if ($paiement->facture) {
            $paiement->facture->actualiserStatut();
        }

        return redirect()->route('paiements.index')->with('success', 'Paiement enregistré avec succès.');
    }

    public function edit(Paiement $paiement)
    {
        $clients = Client::orderBy('nom')->get();
        $factures = Facture::where('client_id', $paiement->client_id)->orderByDesc('id')->get();

        return view('paiements.edit', compact('paiement', 'clients', 'factures'));
    }

    public function update(Request $request, Paiement $paiement)
    {
        $data = $this->validated($request);
        $ancienneFacture = $paiement->facture;

        $paiement->update($data);

        $ancienneFacture?->actualiserStatut();
        $paiement->refresh()->facture?->actualiserStatut();

        return redirect()->route('paiements.index')->with('success', 'Paiement modifié avec succès.');
    }

    public function destroy(Paiement $paiement)
    {
        $facture = $paiement->facture;
        $paiement->delete();

        if ($facture) {
            $facture->actualiserStatut();
        }

        return redirect()->route('paiements.index')->with('success', 'Paiement supprimé.');
    }

    public function marquerEncaisse(Paiement $paiement)
    {
        $paiement->update(['statut_encaissement' => 'encaisse']);

        return back()->with('success', 'Paiement marqué comme encaissé.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'client_id' => ['required', 'exists:clients,id'],
            'facture_id' => ['nullable', 'exists:factures,id'],
            'date_paiement' => ['required', 'date'],
            'montant' => ['required', 'numeric', 'min:0.01'],
            'mode' => ['required', 'in:especes,cheque,virement,effet'],
            'reference' => ['nullable', 'string', 'max:100'],
            'date_echeance' => ['nullable', 'date'],
            'statut_encaissement' => ['nullable', 'in:encaisse,en_attente'],
        ]);

        if (! in_array($data['mode'], ['cheque', 'effet'])) {
            $data['date_echeance'] = null;
            $data['statut_encaissement'] = null;
        } elseif (empty($data['statut_encaissement'])) {
            $data['statut_encaissement'] = 'en_attente';
        }

        return $data;
    }
}
