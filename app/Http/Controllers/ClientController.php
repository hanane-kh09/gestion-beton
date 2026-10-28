<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        $clients = Client::query()
            ->when($request->q, fn ($q, $v) => $q->where(fn ($w) => $w
                ->where('nom', 'like', "%$v%")
                ->orWhere('telephone', 'like', "%$v%")
                ->orWhere('ice', 'like', "%$v%")))
            ->when($request->type, fn ($q, $v) => $q->where('type', $v))
            ->orderBy('nom')->paginate(15)->withQueryString();

        return view('clients.index', compact('clients'));
    }

    public function create()
    {
        return view('clients.create');
    }

    public function store(Request $request)
    {
        Client::create($this->validated($request));

        return redirect()->route('clients.index')->with('success', 'Client créé avec succès.');
    }

    public function show(Client $client)
    {
        $client->load(['commandes.produit', 'factures', 'paiements.facture']);

        return view('clients.show', compact('client'));
    }

    public function edit(Client $client)
    {
        return view('clients.edit', compact('client'));
    }

    public function update(Request $request, Client $client)
    {
        $client->update($this->validated($request));

        return redirect()->route('clients.index')->with('success', 'Client modifié avec succès.');
    }

    public function destroy(Client $client)
    {
        $client->delete();

        return redirect()->route('clients.index')->with('success', 'Client supprimé.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'type' => ['required', 'in:particulier,entreprise,promoteur'],
            'nom' => ['required', 'string', 'max:255'],
            'ice' => ['nullable', 'string', 'max:30'],
            'telephone' => ['required', 'string', 'max:20'],
            'email' => ['nullable', 'email'],
            'adresse' => ['nullable', 'string', 'max:255'],
        ]);
    }
}
