@extends('layout')
@section('title', $bon->numero)
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <h3><i class="bi bi-file-earmark-text me-2"></i>{{ $bon->numero }}</h3>
  <div>
    @if(!$bon->signe)
    <form class="d-inline" method="POST" action="{{ route('bons.signer', $bon) }}">@csrf
      <button class="btn btn-success"><i class="bi bi-pen me-1"></i>Marquer comme signé</button>
    </form>
    @endif
    <a href="{{ route('bons.edit', $bon) }}" class="btn btn-outline-primary"><i class="bi bi-pencil me-1"></i>Modifier</a>
    <a href="{{ route('bons.imprimer', $bon) }}" target="_blank" class="btn btn-outline-secondary"><i class="bi bi-printer me-1"></i>Imprimer / PDF</a>
  </div>
</div>
<div class="row g-3">
  <div class="col-md-3"><div class="card p-3"><div class="text-muted small">Date d'émission</div><div class="fw-bold">{{ $bon->date_emission->format('d/m/Y') }}</div></div></div>
  <div class="col-md-3"><div class="card p-3"><div class="text-muted small">Client</div><div class="fw-bold">{{ $bon->rotation->commande->client->nom }}</div></div></div>
  <div class="col-md-3"><div class="card p-3"><div class="text-muted small">Produit</div><div class="fw-bold">{{ $bon->rotation->commande->produit->code }} — {{ $bon->quantite_livree }} m³</div></div></div>
  <div class="col-md-3"><div class="card p-3"><div class="text-muted small">Camion / Chauffeur</div><div class="fw-bold">{{ $bon->rotation->camion->matricule }}<br>{{ $bon->rotation->chauffeur->nomComplet() }}</div></div></div>
</div>
<div class="card p-3 mt-3">
  <div class="text-muted small">Chantier</div>
  <div>{{ $bon->rotation->commande->adresse_chantier }}</div>
  <div class="text-muted small mt-2">Montant HT correspondant</div>
  <div class="fw-bold">{{ number_format($bon->montant(), 2, ',', ' ') }} DH</div>
  <div class="text-muted small mt-2">Statut de signature : {!! $bon->signe ? '<span class="badge bg-success">Signé sur chantier</span>' : '<span class="badge bg-secondary">Non signé</span>' !!}</div>
</div>
@endsection
