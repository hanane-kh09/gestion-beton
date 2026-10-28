@extends('layout')
@section('title', 'Historique des mouvements de stock')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <h3><i class="bi bi-clock-history me-2"></i>Historique des mouvements de stock</h3>
  <a href="{{ route('stock.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Retour au stock</a>
</div>
<div class="card p-3">
  <form class="row g-2 mb-3">
    <div class="col-md-4">
      <select name="matiere_premiere_id" class="form-select" onchange="this.form.submit()">
        <option value="">Toutes les matières</option>
        @foreach($matieres as $m)
        <option value="{{ $m->id }}" @selected(request('matiere_premiere_id') == $m->id)>{{ $m->nom }}</option>
        @endforeach
      </select>
    </div>
    <div class="col-md-3">
      <select name="type" class="form-select" onchange="this.form.submit()">
        <option value="">Tous types</option>
        <option value="entree" @selected(request('type') == 'entree')>Entrée</option>
        <option value="sortie" @selected(request('type') == 'sortie')>Sortie</option>
      </select>
    </div>
  </form>
  <table class="table align-middle">
    <thead><tr><th>Date</th><th>Matière</th><th>Type</th><th>Quantité</th><th>Origine</th><th>Commentaire</th></tr></thead>
    <tbody>
    @forelse($mouvements as $mvt)
      <tr>
        <td>{{ $mvt->date_mouvement->format('d/m/Y') }}</td>
        <td>{{ $mvt->matierePremiere->nom }}</td>
        <td><span class="badge bg-{{ $mvt->type === 'entree' ? 'success' : 'secondary' }}">{{ $mvt->type === 'entree' ? 'Entrée' : 'Sortie' }}</span></td>
        <td class="fw-bold">{{ $mvt->type === 'entree' ? '+' : '-' }}{{ number_format($mvt->quantite, 2, ',', ' ') }} {{ $mvt->matierePremiere->unite }}</td>
        <td>
          @if($mvt->rotation)
            Rotation n°{{ $mvt->rotation->num_ordre }} — {{ $mvt->rotation->commande->client->nom }}
          @else
            Manuel
          @endif
        </td>
        <td>{{ $mvt->commentaire ?: '—' }}</td>
      </tr>
    @empty
      <tr><td colspan="6" class="text-center text-muted">Aucun mouvement de stock.</td></tr>
    @endforelse
    </tbody>
  </table>
  {{ $mouvements->links('pagination::bootstrap-5') }}
</div>
@endsection
