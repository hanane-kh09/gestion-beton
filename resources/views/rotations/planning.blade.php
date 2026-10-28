@extends('layout')
@section('title', 'Planning des livraisons')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <h3><i class="bi bi-calendar-week me-2"></i>Planning des livraisons</h3>
  <form class="d-flex gap-2">
    <input type="date" name="date" class="form-control" value="{{ $date }}">
    <button class="btn btn-outline-secondary">Afficher</button>
  </form>
</div>
@if($rotations->isEmpty())
  <div class="card p-4 text-center text-muted">Aucune rotation planifiée pour le {{ \Carbon\Carbon::parse($date)->format('d/m/Y') }}.</div>
@endif
@foreach($rotations as $matricule => $liste)
<div class="card p-3 mb-3">
  <h6><i class="bi bi-truck-front me-1"></i>Camion {{ $matricule }} <span class="text-muted">— {{ $liste->count() }} rotation(s)</span></h6>
  <table class="table table-sm align-middle mb-0">
    <thead><tr><th>N°</th><th>Client / Chantier</th><th>Produit</th><th>Qté</th><th>Chauffeur</th><th>Départ</th><th>Arrivée</th><th>Durée</th><th>Statut</th><th>Action</th></tr></thead>
    <tbody>
    @foreach($liste as $r)
      <tr class="{{ $r->delaiDepasse() ? 'table-danger' : '' }}">
        <td>{{ $r->num_ordre }}</td>
        <td>{{ $r->commande->client->nom }}<br><small class="text-muted">{{ $r->commande->adresse_chantier }}</small></td>
        <td>{{ $r->commande->produit->code }}</td>
        <td>{{ $r->quantite_m3 }} m³</td>
        <td>{{ $r->chauffeur->nomComplet() }}</td>
        <td>{{ $r->heure_depart?->format('H:i') ?: '—' }}</td>
        <td>{{ $r->heure_arrivee?->format('H:i') ?: '—' }}</td>
        <td>
          @if($r->dureeMinutes() !== null)
            {{ $r->dureeMinutes() }} min
            @if($r->delaiDepasse())<i class="bi bi-exclamation-triangle-fill text-danger" title="Délai critique de 90 min dépassé !"></i>@endif
          @else — @endif
        </td>
        <td><span class="badge bg-{{ ['planifiee' => 'secondary', 'chargement' => 'info', 'en_route' => 'primary', 'sur_chantier' => 'warning', 'terminee' => 'success'][$r->statut] }}">{{ str_replace('_', ' ', $r->statut) }}</span></td>
        <td>
          @php $suivant = ['planifiee' => 'chargement', 'chargement' => 'en_route', 'en_route' => 'sur_chantier', 'sur_chantier' => 'terminee'][$r->statut] ?? null; @endphp
          @if($suivant)
          <form method="POST" action="{{ route('rotations.statut', $r) }}">
            @csrf
            <input type="hidden" name="statut" value="{{ $suivant }}">
            <button class="btn btn-sm btn-outline-primary">→ {{ str_replace('_', ' ', $suivant) }}</button>
          </form>
          @else
            <i class="bi bi-check-circle-fill text-success"></i>
          @endif
          @if($r->statut === 'planifiee')
          <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#editRotation{{ $r->id }}" title="Modifier l'affectation"><i class="bi bi-pencil"></i></button>
          @endif
        </td>
      </tr>
    @endforeach
    </tbody>
  </table>
</div>
@foreach($liste as $r)
  @if($r->statut === 'planifiee')
  <div class="modal fade" id="editRotation{{ $r->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <form method="POST" action="{{ route('rotations.affectation', $r) }}">
          @csrf @method('PUT')
          <div class="modal-header">
            <h5 class="modal-title">Modifier l'affectation — Rotation n°{{ $r->num_ordre }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
          </div>
          <div class="modal-body">
            <div class="mb-3">
              <label class="form-label">Camion *</label>
              <select name="camion_id" class="form-select" required>
                @foreach($camions as $c)
                <option value="{{ $c->id }}" @selected($c->id === $r->camion_id)>{{ $c->matricule }} — {{ $c->capacite_m3 }} m³</option>
                @endforeach
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label">Chauffeur *</label>
              <select name="chauffeur_id" class="form-select" required>
                @foreach($chauffeurs as $ch)
                <option value="{{ $ch->id }}" @selected($ch->id === $r->chauffeur_id)>{{ $ch->nomComplet() }}</option>
                @endforeach
              </select>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
            <button class="btn btn-brand">Enregistrer</button>
          </div>
        </form>
      </div>
    </div>
  </div>
  @endif
@endforeach
@endforeach
@endsection
