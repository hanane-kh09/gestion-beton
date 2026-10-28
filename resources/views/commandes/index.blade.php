@extends('layout')
@section('title', 'Commandes')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <h3><i class="bi bi-cart me-2"></i>Commandes</h3>
  <a href="{{ route('commandes.create') }}" class="btn btn-brand"><i class="bi bi-plus-lg me-1"></i>Nouvelle commande</a>
</div>
<div class="card p-3">
  <form class="row g-2 mb-3">
    <div class="col-md-4"><input name="q" class="form-control" placeholder="Rechercher par client..." value="{{ request('q') }}"></div>
    <div class="col-md-3">
      <select name="statut" class="form-select">
        <option value="">Tous les statuts</option>
        @foreach(['en_attente', 'confirmee', 'planifiee', 'en_livraison', 'livree', 'annulee'] as $s)
        <option value="{{ $s }}" @selected(request('statut') == $s)>{{ ucfirst(str_replace('_', ' ', $s)) }}</option>
        @endforeach
      </select>
    </div>
    <div class="col-auto"><button class="btn btn-outline-secondary"><i class="bi bi-search"></i></button></div>
  </form>
  <table class="table align-middle">
    <thead><tr><th>#</th><th>Client</th><th>Produit</th><th>Qté</th><th>Montant</th><th>Livraison</th><th>Statut</th><th></th></tr></thead>
    <tbody>
    @forelse($commandes as $c)
      <tr>
        <td>CMD-{{ $c->id }}</td>
        <td>{{ $c->client->nom }}</td>
        <td>{{ $c->produit->code }}</td>
        <td>{{ $c->quantite_m3 }} m³</td>
        <td>{{ number_format($c->montant_total, 2, ',', ' ') }} DH</td>
        <td>{{ $c->date_livraison->format('d/m/Y H:i') }}</td>
        <td><span class="badge bg-{{ ['en_attente' => 'secondary', 'confirmee' => 'info', 'planifiee' => 'primary', 'en_livraison' => 'warning', 'livree' => 'success', 'annulee' => 'danger'][$c->statut] }}">{{ str_replace('_', ' ', $c->statut) }}</span></td>
        <td class="text-end"><a href="{{ route('commandes.show', $c) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i></a></td>
      </tr>
    @empty
      <tr><td colspan="8" class="text-center text-muted">Aucune commande.</td></tr>
    @endforelse
    </tbody>
  </table>
  {{ $commandes->links('pagination::bootstrap-5') }}
</div>
@endsection
