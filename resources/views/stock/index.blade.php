@extends('layout')
@section('title', 'Stock des matières premières')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <h3><i class="bi bi-boxes me-2"></i>Stock des matières premières</h3>
  <div>
    <a href="{{ route('formulations.index') }}" class="btn btn-outline-secondary"><i class="bi bi-clipboard-data me-1"></i>Formulations</a>
    <a href="{{ route('stock.mouvements') }}" class="btn btn-outline-secondary"><i class="bi bi-clock-history me-1"></i>Historique</a>
    <a href="{{ route('stock.create') }}" class="btn btn-brand"><i class="bi bi-plus-lg me-1"></i>Nouvelle matière</a>
  </div>
</div>
<div class="row g-3">
@forelse($matieres as $m)
  @php $pct = $m->seuil_alerte > 0 ? max(3, min(100, round((float) $m->stock_actuel / ((float) $m->seuil_alerte * 3) * 100))) : 100; @endphp
  <div class="col-md-4">
    <div class="card p-3 h-100">
      <div class="d-flex justify-content-between align-items-start">
        <div>
          <div class="fw-bold">{{ $m->nom }}</div>
          <div class="text-muted small">Unité : {{ $m->unite }}</div>
        </div>
        @if($m->enAlerte())<span class="badge bg-danger">Alerte stock</span>@endif
      </div>
      <div class="mt-2">
        <div class="d-flex justify-content-between small">
          <span>Stock actuel</span>
          <span class="fw-bold">{{ number_format($m->stock_actuel, 2, ',', ' ') }} {{ $m->unite }}</span>
        </div>
        <div class="progress" style="height:8px;">
          <div class="progress-bar {{ $m->enAlerte() ? 'bg-danger' : 'bg-success' }}" style="width: {{ $pct }}%"></div>
        </div>
        <div class="text-muted small mt-1">Seuil d'alerte : {{ number_format($m->seuil_alerte, 2, ',', ' ') }} {{ $m->unite }}</div>
      </div>
      <div class="mt-3 d-flex gap-2">
        <a href="{{ route('stock.approvisionner', $m) }}" class="btn btn-sm btn-outline-success flex-fill"><i class="bi bi-box-arrow-in-down me-1"></i>Approvisionner</a>
        <a href="{{ route('stock.edit', $m) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
      </div>
    </div>
  </div>
@empty
  <div class="col-12"><div class="card p-4 text-center text-muted">Aucune matière première enregistrée.</div></div>
@endforelse
</div>
@endsection
