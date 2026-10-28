@extends('layout')
@section('title', 'Commande CMD-'.$commande->id)
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <h3><i class="bi bi-cart me-2"></i>Commande CMD-{{ $commande->id }}
    <span class="badge fs-6 bg-{{ ['en_attente' => 'secondary', 'confirmee' => 'info', 'planifiee' => 'primary', 'en_livraison' => 'warning', 'livree' => 'success', 'annulee' => 'danger'][$commande->statut] }}">{{ str_replace('_', ' ', $commande->statut) }}</span>
  </h3>
  <div>
    @if($commande->statut === 'en_attente')
      <form class="d-inline" method="POST" action="{{ route('commandes.confirmer', $commande) }}">@csrf
        <button class="btn btn-success"><i class="bi bi-check-lg me-1"></i>Confirmer</button>
      </form>
    @endif
    @if(in_array($commande->statut, ['confirmee', 'planifiee']))
      <a href="{{ route('commandes.planifier', $commande) }}" class="btn btn-primary"><i class="bi bi-calendar-plus me-1"></i>Planifier les rotations</a>
    @endif
    @if(!in_array($commande->statut, ['livree', 'annulee']))
      <a href="{{ route('commandes.edit', $commande) }}" class="btn btn-outline-primary"><i class="bi bi-pencil"></i></a>
      <form class="d-inline" method="POST" action="{{ route('commandes.annuler', $commande) }}" onsubmit="return confirm('Annuler cette commande ?')">@csrf
        <button class="btn btn-outline-danger"><i class="bi bi-x-lg me-1"></i>Annuler</button>
      </form>
    @endif
  </div>
</div>

<div class="row g-3 mb-3">
  <div class="col-md-3"><div class="card p-3"><div class="text-muted small">Client</div><div class="fw-bold"><a href="{{ route('clients.show', $commande->client) }}">{{ $commande->client->nom }}</a></div></div></div>
  <div class="col-md-2"><div class="card p-3"><div class="text-muted small">Produit</div><div class="fw-bold">{{ $commande->produit->code }}</div></div></div>
  <div class="col-md-2"><div class="card p-3"><div class="text-muted small">Quantité</div><div class="fw-bold">{{ $commande->quantite_m3 }} m³</div></div></div>
  <div class="col-md-2"><div class="card p-3"><div class="text-muted small">Montant</div><div class="fw-bold">{{ number_format($commande->montant_total, 2, ',', ' ') }} DH</div></div></div>
  <div class="col-md-3"><div class="card p-3"><div class="text-muted small">Livraison</div><div class="fw-bold">{{ $commande->date_livraison->format('d/m/Y à H:i') }}</div></div></div>
</div>
<div class="card p-3 mb-3">
  <div class="text-muted small">Adresse du chantier</div>
  <div>{{ $commande->adresse_chantier }}</div>
  @if($commande->notes)<div class="text-muted small mt-2">Notes</div><div>{{ $commande->notes }}</div>@endif
</div>

<div class="card p-3">
  <h6><i class="bi bi-arrow-repeat me-1"></i>Rotations ({{ $commande->rotations->count() }}) — Livré : {{ $commande->quantiteLivree() }} / {{ $commande->quantite_m3 }} m³</h6>
  @if($commande->rotations->isEmpty())
    <p class="text-muted mb-0">Aucune rotation planifiée. Confirmez la commande puis cliquez sur « Planifier les rotations ».</p>
  @else
  <table class="table table-sm align-middle mb-0">
    <thead><tr><th>N°</th><th>Camion</th><th>Chauffeur</th><th>Qté</th><th>Départ</th><th>Arrivée</th><th>Statut</th><th>BL</th></tr></thead>
    <tbody>
    @foreach($commande->rotations as $r)
      <tr>
        <td>{{ $r->num_ordre }}</td>
        <td>{{ $r->camion->matricule }}</td>
        <td>{{ $r->chauffeur->nomComplet() }}</td>
        <td>{{ $r->quantite_m3 }} m³</td>
        <td>{{ $r->heure_depart?->format('H:i') ?: '—' }}</td>
        <td>{{ $r->heure_arrivee?->format('H:i') ?: '—' }}</td>
        <td><span class="badge bg-{{ ['planifiee' => 'secondary', 'chargement' => 'info', 'en_route' => 'primary', 'sur_chantier' => 'warning', 'terminee' => 'success'][$r->statut] }}">{{ str_replace('_', ' ', $r->statut) }}</span></td>
        <td>@if($r->bonLivraison)<a href="{{ route('bons.show', $r->bonLivraison) }}">{{ $r->bonLivraison->numero }}</a>@else — @endif</td>
      </tr>
    @endforeach
    </tbody>
  </table>
  @endif
</div>
@endsection
