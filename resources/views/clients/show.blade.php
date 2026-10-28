@extends('layout')
@section('title', $client->nom)
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <h3><i class="bi bi-person me-2"></i>{{ $client->nom }} <span class="badge bg-secondary fs-6">{{ ucfirst($client->type) }}</span></h3>
  <a href="{{ route('clients.edit', $client) }}" class="btn btn-outline-primary"><i class="bi bi-pencil me-1"></i>Modifier</a>
</div>
<div class="row g-3 mb-3">
  <div class="col-md-3"><div class="card p-3"><div class="text-muted small">Téléphone</div><div>{{ $client->telephone }}</div></div></div>
  <div class="col-md-3"><div class="card p-3"><div class="text-muted small">ICE</div><div>{{ $client->ice ?: '—' }}</div></div></div>
  <div class="col-md-3"><div class="card p-3"><div class="text-muted small">Total facturé</div><div>{{ number_format($client->factures->sum('montant_ttc'), 2, ',', ' ') }} DH</div></div></div>
  <div class="col-md-3"><div class="card p-3"><div class="text-muted small">Solde restant dû</div><div class="fw-bold {{ $client->solde() > 0 ? 'text-danger' : 'text-success' }}">{{ number_format($client->solde(), 2, ',', ' ') }} DH</div></div></div>
</div>
<div class="card p-3 mb-3">
  <h6>Commandes</h6>
  <table class="table table-sm align-middle mb-0">
    <thead><tr><th>#</th><th>Produit</th><th>Qté</th><th>Montant</th><th>Livraison</th><th>Statut</th></tr></thead>
    <tbody>
    @forelse($client->commandes->sortByDesc('date_livraison') as $c)
      <tr>
        <td><a href="{{ route('commandes.show', $c) }}">CMD-{{ $c->id }}</a></td>
        <td>{{ $c->produit->code }}</td>
        <td>{{ $c->quantite_m3 }} m³</td>
        <td>{{ number_format($c->montant_total, 2, ',', ' ') }} DH</td>
        <td>{{ $c->date_livraison->format('d/m/Y H:i') }}</td>
        <td><span class="badge bg-info text-dark">{{ str_replace('_', ' ', $c->statut) }}</span></td>
      </tr>
    @empty
      <tr><td colspan="6" class="text-muted text-center">Aucune commande.</td></tr>
    @endforelse
    </tbody>
  </table>
</div>
<div class="row g-3">
  <div class="col-md-6">
    <div class="card p-3">
      <h6>Factures</h6>
      <table class="table table-sm mb-0">
        <thead><tr><th>N°</th><th>TTC</th><th>Statut</th></tr></thead>
        <tbody>
        @forelse($client->factures as $f)
          <tr><td>{{ $f->numero }}</td><td>{{ number_format($f->montant_ttc, 2, ',', ' ') }} DH</td>
          <td><span class="badge bg-{{ ['impayee' => 'danger', 'partielle' => 'warning', 'payee' => 'success'][$f->statut] }}">{{ $f->statut }}</span></td></tr>
        @empty
          <tr><td colspan="3" class="text-muted text-center">Aucune facture.</td></tr>
        @endforelse
        </tbody>
      </table>
    </div>
  </div>
  <div class="col-md-6">
    <div class="card p-3">
      <h6>Paiements</h6>
      <table class="table table-sm mb-0">
        <thead><tr><th>Date</th><th>Montant</th><th>Mode</th></tr></thead>
        <tbody>
        @forelse($client->paiements->sortByDesc('date_paiement') as $p)
          <tr><td>{{ $p->date_paiement->format('d/m/Y') }}</td><td>{{ number_format($p->montant, 2, ',', ' ') }} DH</td><td>{{ $p->modeLabel() }}</td></tr>
        @empty
          <tr><td colspan="3" class="text-muted text-center">Aucun paiement.</td></tr>
        @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
