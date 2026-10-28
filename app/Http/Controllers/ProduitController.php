<?php

namespace App\Http\Controllers;

use App\Models\Produit;
use Illuminate\Http\Request;

class ProduitController extends Controller
{
    public function index()
    {
        $produits = Produit::orderBy('code')->paginate(15);

        return view('produits.index', compact('produits'));
    }

    public function create()
    {
        return view('produits.create');
    }

    public function store(Request $request)
    {
        Produit::create($this->validated($request));

        return redirect()->route('produits.index')->with('success', 'Produit créé avec succès.');
    }

    public function edit(Produit $produit)
    {
        return view('produits.edit', compact('produit'));
    }

    public function update(Request $request, Produit $produit)
    {
        $produit->update($this->validated($request, $produit->id));

        return redirect()->route('produits.index')->with('success', 'Produit modifié avec succès.');
    }

    public function destroy(Produit $produit)
    {
        if ($produit->commandes()->exists()) {
            $produit->update(['actif' => false]);

            return redirect()->route('produits.index')->with('success', 'Produit désactivé (des commandes y sont liées).');
        }
        $produit->delete();

        return redirect()->route('produits.index')->with('success', 'Produit supprimé.');
    }

    private function validated(Request $request, ?int $id = null): array
    {
        return $request->validate([
            'code' => ['required', 'string', 'max:20', 'unique:produits,code'.($id ? ",$id" : '')],
            'designation' => ['required', 'string', 'max:255'],
            'prix_m3' => ['required', 'numeric', 'min:0'],
            'actif' => ['boolean'],
        ]);
    }
}
