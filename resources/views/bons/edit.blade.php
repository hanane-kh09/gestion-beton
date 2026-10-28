@extends('layout')
@section('title', 'Modifier '.$bon->numero)
@section('content')
<h3 class="mb-3"><i class="bi bi-pencil me-2"></i>Modifier le bon {{ $bon->numero }}</h3>
<div class="card p-4">
  <form method="POST" action="{{ route('bons.update', $bon) }}">
    @csrf @method('PUT')
    <div class="row g-3">
      <div class="col-md-4">
        <label class="form-label">Date d'émission *</label>
        <input type="date" name="date_emission" class="form-control" value="{{ old('date_emission', $bon->date_emission->toDateString()) }}" required>
      </div>
      <div class="col-md-4">
        <label class="form-label">Quantité livrée (m³) *</label>
        <input type="number" step="0.01" min="0.01" name="quantite_livree" class="form-control" value="{{ old('quantite_livree', $bon->quantite_livree) }}" required>
      </div>
      <div class="col-md-4">
        <label class="form-label d-block">Signature</label>
        <div class="form-check form-switch mt-2">
          <input type="hidden" name="signe" value="0">
          <input type="checkbox" name="signe" value="1" class="form-check-input" id="signe" @checked(old('signe', $bon->signe))>
          <label class="form-check-label" for="signe">Signé sur chantier</label>
        </div>
      </div>
    </div>
    @if($bon->facture_id)
    <div class="alert alert-warning mt-3 mb-0">
      Ce bon est rattaché à la facture <strong>{{ $bon->facture->numero }}</strong> : la modification de la quantité livrée recalculera automatiquement le montant HT/TVA/TTC de cette facture.
    </div>
    @endif
    <div class="mt-3">
      <button class="btn btn-brand"><i class="bi bi-check-lg me-1"></i>Enregistrer</button>
      <a href="{{ route('bons.show', $bon) }}" class="btn btn-outline-secondary">Annuler</a>
    </div>
  </form>
</div>
@endsection
