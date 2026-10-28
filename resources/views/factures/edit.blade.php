@extends('layout')
@section('title', 'Modifier '.$facture->numero)
@section('content')
<h3 class="mb-3"><i class="bi bi-pencil me-2"></i>Modifier la facture {{ $facture->numero }}</h3>
<div class="card p-4">
  <form method="POST" action="{{ route('factures.update', $facture) }}">
    @csrf @method('PUT')
    <div class="row g-3">
      <div class="col-md-6">
        <label class="form-label">Date d'émission *</label>
        <input type="date" name="date_emission" class="form-control" value="{{ old('date_emission', $facture->date_emission->toDateString()) }}" required>
      </div>
      <div class="col-md-6">
        <label class="form-label">Date d'échéance</label>
        <input type="date" name="date_echeance" class="form-control" value="{{ old('date_echeance', $facture->date_echeance?->toDateString()) }}">
      </div>
    </div>
    <div class="mt-3">
      <button class="btn btn-brand"><i class="bi bi-check-lg me-1"></i>Enregistrer</button>
      <a href="{{ route('factures.show', $facture) }}" class="btn btn-outline-secondary">Annuler</a>
    </div>
  </form>
</div>
@endsection
