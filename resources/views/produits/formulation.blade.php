@extends('layout')
@section('title', 'Formulation '.$produit->code)
@section('content')
<h3 class="mb-3"><i class="bi bi-clipboard-data me-2"></i>Formulation — {{ $produit->code }}</h3>
<div class="alert alert-info">{{ $produit->designation }}</div>
<div class="card p-4">
  <form method="POST" action="{{ route('produits.formulation.update', $produit) }}">
    @csrf @method('PUT')
    <table class="table align-middle">
      <thead><tr><th>Matière première</th><th>Unité</th><th>Quantité par m³</th></tr></thead>
      <tbody>
      @forelse($matieres as $mat)
        <tr>
          <td>{{ $mat->nom }}</td>
          <td>{{ $mat->unite }}</td>
          <td><input type="number" step="0.001" min="0" name="quantites[{{ $mat->id }}]" class="form-control" value="{{ old('quantites.'.$mat->id, $formulations[$mat->id] ?? '') }}"></td>
        </tr>
      @empty
        <tr><td colspan="3" class="text-center text-muted">Aucune matière première enregistrée. Ajoutez-en depuis la page Stock.</td></tr>
      @endforelse
      </tbody>
    </table>
    <div class="mt-3">
      <button class="btn btn-brand"><i class="bi bi-check-lg me-1"></i>Enregistrer</button>
      <a href="{{ route('formulations.index') }}" class="btn btn-outline-secondary">Annuler</a>
    </div>
  </form>
</div>
@endsection
