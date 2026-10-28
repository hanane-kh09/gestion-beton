@extends('layout')
@section('title', 'Approvisionner '.$matiere->nom)
@section('content')
<h3 class="mb-3"><i class="bi bi-box-arrow-in-down me-2"></i>Approvisionner : {{ $matiere->nom }}</h3>
<div class="alert alert-info">Stock actuel : <strong>{{ number_format($matiere->stock_actuel, 2, ',', ' ') }} {{ $matiere->unite }}</strong></div>
<div class="card p-4">
  <form method="POST" action="{{ route('stock.approvisionner.store', $matiere) }}">
    @csrf
    <div class="row g-3">
      <div class="col-md-4">
        <label class="form-label">Quantité reçue ({{ $matiere->unite }}) *</label>
        <input type="number" step="0.01" min="0.01" name="quantite" class="form-control" required autofocus>
      </div>
      <div class="col-md-4">
        <label class="form-label">Date *</label>
        <input type="date" name="date_mouvement" class="form-control" value="{{ today()->toDateString() }}" required>
      </div>
      <div class="col-md-4">
        <label class="form-label">Commentaire</label>
        <input name="commentaire" class="form-control" placeholder="N° bon de livraison fournisseur...">
      </div>
    </div>
    <div class="mt-3">
      <button class="btn btn-brand"><i class="bi bi-check-lg me-1"></i>Enregistrer l'approvisionnement</button>
      <a href="{{ route('stock.index') }}" class="btn btn-outline-secondary">Annuler</a>
    </div>
  </form>
</div>
@endsection
