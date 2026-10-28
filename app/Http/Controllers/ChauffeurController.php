<?php

namespace App\Http\Controllers;

use App\Models\Camion;
use App\Models\Chauffeur;
use Illuminate\Http\Request;

class ChauffeurController extends Controller
{
    public function index()
    {
        $chauffeurs = Chauffeur::with('camion')->orderBy('nom')->paginate(15);

        return view('chauffeurs.index', compact('chauffeurs'));
    }

    public function create()
    {
        $camions = Camion::orderBy('matricule')->get();

        return view('chauffeurs.create', compact('camions'));
    }

    public function store(Request $request)
    {
        Chauffeur::create($this->validated($request));

        return redirect()->route('chauffeurs.index')->with('success', 'Chauffeur ajouté avec succès.');
    }

    public function edit(Chauffeur $chauffeur)
    {
        $camions = Camion::orderBy('matricule')->get();

        return view('chauffeurs.edit', compact('chauffeur', 'camions'));
    }

    public function update(Request $request, Chauffeur $chauffeur)
    {
        $chauffeur->update($this->validated($request, $chauffeur->id));

        return redirect()->route('chauffeurs.index')->with('success', 'Chauffeur modifié avec succès.');
    }

    public function destroy(Chauffeur $chauffeur)
    {
        if ($chauffeur->rotations()->exists()) {
            return redirect()->route('chauffeurs.index')->with('error', 'Impossible : des rotations sont liées à ce chauffeur.');
        }
        $chauffeur->delete();

        return redirect()->route('chauffeurs.index')->with('success', 'Chauffeur supprimé.');
    }

    private function validated(Request $request, ?int $id = null): array
    {
        return $request->validate([
            'nom' => ['required', 'string', 'max:100'],
            'prenom' => ['required', 'string', 'max:100'],
            'cin' => ['required', 'string', 'max:20', 'unique:chauffeurs,cin'.($id ? ",$id" : '')],
            'num_permis' => ['required', 'string', 'max:30'],
            'telephone' => ['nullable', 'string', 'max:20'],
            'camion_id' => ['nullable', 'exists:camions,id'],
        ]);
    }
}
