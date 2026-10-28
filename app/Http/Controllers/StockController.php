<?php

namespace App\Http\Controllers;

use App\Models\MatierePremiere;
use App\Models\MouvementStock;
use Illuminate\Http\Request;

class StockController extends Controller
{
    public function index()
    {
        $matieres = MatierePremiere::orderBy('nom')->get();

        return view('stock.index', compact('matieres'));
    }

    public function create()
    {
        return view('stock.create');
    }

    public function store(Request $request)
    {
        MatierePremiere::create($this->validated($request));

        return redirect()->route('stock.index')->with('success', 'Matière première ajoutée avec succès.');
    }

    public function edit(MatierePremiere $matiere)
    {
        return view('stock.edit', compact('matiere'));
    }

    public function update(Request $request, MatierePremiere $matiere)
    {
        $matiere->update($this->validated($request));

        return redirect()->route('stock.index')->with('success', 'Matière première modifiée avec succès.');
    }

    public function approvisionner(MatierePremiere $matiere)
    {
        return view('stock.approvisionner', compact('matiere'));
    }

    public function storeApprovisionnement(Request $request, MatierePremiere $matiere)
    {
        $data = $request->validate([
            'quantite' => ['required', 'numeric', 'min:0.01'],
            'date_mouvement' => ['required', 'date'],
            'commentaire' => ['nullable', 'string', 'max:255'],
        ]);

        MouvementStock::create([
            'matiere_premiere_id' => $matiere->id,
            'type' => 'entree',
            'quantite' => $data['quantite'],
            'date_mouvement' => $data['date_mouvement'],
            'commentaire' => $data['commentaire'] ?: 'Approvisionnement',
        ]);

        $matiere->increment('stock_actuel', $data['quantite']);

        return redirect()->route('stock.index')->with('success', 'Approvisionnement enregistré, stock mis à jour.');
    }

    public function mouvements(Request $request)
    {
        $matieres = MatierePremiere::orderBy('nom')->get();

        $mouvements = MouvementStock::with(['matierePremiere', 'rotation.commande.client'])
            ->when($request->matiere_premiere_id, fn ($q, $v) => $q->where('matiere_premiere_id', $v))
            ->when($request->type, fn ($q, $v) => $q->where('type', $v))
            ->orderByDesc('date_mouvement')->orderByDesc('id')
            ->paginate(20)->withQueryString();

        return view('stock.mouvements', compact('mouvements', 'matieres'));
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'nom' => ['required', 'string', 'max:255'],
            'unite' => ['required', 'in:tonne,m3,litre,kg'],
            'stock_actuel' => ['required', 'numeric', 'min:0'],
            'seuil_alerte' => ['required', 'numeric', 'min:0'],
        ]);
    }
}
