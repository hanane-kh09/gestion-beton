<?php

namespace App\Http\Controllers;

use App\Models\Camion;
use Illuminate\Http\Request;

class CamionController extends Controller
{
    public function index()
    {
        $camions = Camion::with('chauffeur')->orderBy('matricule')->paginate(15);

        return view('camions.index', compact('camions'));
    }

    public function create()
    {
        return view('camions.create');
    }

    public function store(Request $request)
    {
        Camion::create($this->validated($request));

        return redirect()->route('camions.index')->with('success', 'Camion ajouté avec succès.');
    }

    public function edit(Camion $camion)
    {
        return view('camions.edit', compact('camion'));
    }

    public function update(Request $request, Camion $camion)
    {
        $camion->update($this->validated($request, $camion->id));

        return redirect()->route('camions.index')->with('success', 'Camion modifié avec succès.');
    }

    public function destroy(Camion $camion)
    {
        if ($camion->rotations()->exists()) {
            return redirect()->route('camions.index')->with('error', 'Impossible : des rotations sont liées à ce camion.');
        }
        $camion->delete();

        return redirect()->route('camions.index')->with('success', 'Camion supprimé.');
    }

    private function validated(Request $request, ?int $id = null): array
    {
        return $request->validate([
            'matricule' => ['required', 'string', 'max:20', 'unique:camions,matricule'.($id ? ",$id" : '')],
            'capacite_m3' => ['required', 'numeric', 'min:1', 'max:20'],
            'etat' => ['required', 'in:disponible,en_mission,en_panne,maintenance'],
        ]);
    }
}
