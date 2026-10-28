@extends('layout')
@section('title', 'Planifier CMD-'.$commande->id)
@section('content')
<h3 class="mb-3"><i class="bi bi-calendar-plus me-2"></i>Planifier la commande CMD-{{ $commande->id }}</h3>
<div class="alert alert-info">
  <strong>{{ $commande->client->nom }}</strong> — {{ $commande->produit->code }}, {{ $commande->quantite_m3 }} m³,
  livraison le {{ $commande->date_livraison->format('d/m/Y à H:i') }} à « {{ $commande->adresse_chantier }} ».
</div>
<div class="card p-4">
  <form method="POST" action="{{ route('commandes.planifier.store', $commande) }}">
    @csrf
    <div class="row g-3">
      <div class="col-md-6">
        <label class="form-label">Camion toupie *</label>
        <select name="camion_id" id="camion" class="form-select" required>
          <option value="">— Choisir —</option>
          @foreach($camions as $c)
          <option value="{{ $c->id }}" data-cap="{{ $c->capacite_m3 }}">
            {{ $c->matricule }} — {{ $c->capacite_m3 }} m³ — {{ str_replace('_', ' ', $c->etat) }} — {{ $c->rotations_jour }} rotation(s) ce jour
          </option>
          @endforeach
        </select>
        <div class="form-text">Les camions en panne ou en maintenance sont exclus.</div>
      </div>
      <div class="col-md-6">
        <label class="form-label">Chauffeur *</label>
        <select name="chauffeur_id" class="form-select" required>
          <option value="">— Choisir —</option>
          @foreach($chauffeurs as $ch)
          <option value="{{ $ch->id }}">{{ $ch->nomComplet() }} @if($ch->camion) ({{ $ch->camion->matricule }}) @endif</option>
          @endforeach
        </select>
      </div>
      <div class="col-12">
        <div class="alert alert-secondary mb-0">
          Nombre de rotations nécessaires : <strong id="nbRotations">—</strong>
          <span class="text-muted">(quantité commandée ÷ capacité du camion)</span>
        </div>
      </div>
    </div>
    <div class="mt-3">
      <button class="btn btn-brand"><i class="bi bi-check-lg me-1"></i>Valider la planification</button>
      <a href="{{ route('commandes.show', $commande) }}" class="btn btn-outline-secondary">Annuler</a>
    </div>
  </form>
</div>
@endsection
@section('scripts')
<script>
document.getElementById('camion').addEventListener('change', function () {
  const cap = parseFloat(this.options[this.selectedIndex]?.dataset.cap || 0);
  const qte = {{ $commande->quantite_m3 }};
  document.getElementById('nbRotations').textContent = cap ? Math.ceil(qte / cap) : '—';
});
</script>
@endsection
