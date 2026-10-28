<?php

namespace App\Http\Controllers;

use App\Models\Formulation;
use App\Models\MatierePremiere;
use App\Models\Produit;
use Illuminate\Http\Request;

class FormulationController extends Controller
{
    public function index()
    {
        $produits = Produit::withCount('formulations')->orderBy('code')->get();

        return view('formulations.index', compact('produits'));
    }

    public function edit(Produit $produit)
    {
        $matieres = MatierePremiere::orderBy('nom')->get();
        $formulations = $produit->formulations()->pluck('quantite_par_m3', 'matiere_premiere_id');

        return view('produits.formulation', compact('produit', 'matieres', 'formulations'));
    }

    public function update(Request $request, Produit $produit)
    {
        $data = $request->validate([
            'quantites' => ['required', 'array'],
            'quantites.*' => ['nullable', 'numeric', 'min:0'],
        ]);

        foreach ($data['quantites'] as $matiereId => $quantite) {
            if ($quantite === null || $quantite === '') {
                Formulation::where('produit_id', $produit->id)->where('matiere_premiere_id', $matiereId)->delete();

                continue;
            }

            Formulation::updateOrCreate(
                ['produit_id' => $produit->id, 'matiere_premiere_id' => $matiereId],
                ['quantite_par_m3' => $quantite]
            );
        }

        return redirect()->route('formulations.index')->with('success', 'Formulation mise à jour pour '.$produit->code.'.');
    }
}
