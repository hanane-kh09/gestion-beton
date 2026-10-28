@extends('layout')
@section('title', 'Tableau de bord')
@section('content')
<h3 class="mb-4"><i class="bi bi-speedometer2 me-2"></i>Tableau de bord</h3>

<div class="row g-3 mb-4">
  <div class="col-md-3"><div class="card p-3"><div class="text-muted small">Clients</div><div class="fs-3 fw-bold">{{ $stats['clients'] }}</div></div></div>
  <div class="col-md-3"><div class="card p-3"><div class="text-muted small">Commandes en cours</div><div class="fs-3 fw-bold">{{ $stats['commandesEnCours'] }}</div></div></div>
  <div class="col-md-2"><div class="card p-3"><div class="text-muted small">CA {{ now()->year }}</div><div class="fs-5 fw-bold">{{ number_format($stats['caAnnee'], 0, ',', ' ') }} DH</div></div></div>
  <div class="col-md-2"><div class="card p-3"><div class="text-muted small">Impayés</div><div class="fs-5 fw-bold text-danger">{{ number_format($stats['impayes'], 0, ',', ' ') }} DH</div></div></div>
  <div class="col-md-2"><div class="card p-3"><div class="text-muted small">Flotte utilisée</div><div class="fs-3 fw-bold">{{ $stats['tauxFlotte'] }}%</div></div></div>
</div>

<div class="row g-3 mb-4">
  <div class="col-md-6">
    <div class="card p-3">
      <div class="text-muted small">Chèques / effets en attente d'encaissement</div>
      <div class="fs-3 fw-bold {{ $stats['chequesEnAttenteNb'] ? 'text-warning' : '' }}">{{ $stats['chequesEnAttenteNb'] }}</div>
      <div class="text-muted small">{{ number_format($stats['chequesEnAttenteMontant'], 2, ',', ' ') }} DH au total</div>
    </div>
  </div>
  <div class="col-md-6">
    <div class="card p-3">
      <div class="text-muted small">Matières en alerte de stock</div>
      <div class="fs-3 fw-bold {{ $matieresEnAlerte->count() ? 'text-danger' : '' }}">{{ $matieresEnAlerte->count() }}</div>
      @if($matieresEnAlerte->isNotEmpty())
      <div class="mt-1 d-flex flex-wrap gap-1">
        @foreach($matieresEnAlerte->take(4) as $m)
          <span class="badge bg-danger">{{ $m->nom }} ({{ number_format($m->stock_actuel, 0, ',', ' ') }} {{ $m->unite }})</span>
        @endforeach
      </div>
      @else
      <div class="text-muted small">Aucune matière sous le seuil d'alerte.</div>
      @endif
    </div>
  </div>
</div>

<div class="row g-3 mb-4">
  <div class="col-md-7"><div class="card p-3"><h6>Chiffre d'affaires mensuel ({{ now()->year }})</h6><canvas id="caChart" height="110"></canvas></div></div>
  <div class="col-md-5"><div class="card p-3"><h6>Quantités vendues par type de béton (m³)</h6><canvas id="produitChart" height="150"></canvas></div></div>
</div>

<div class="row g-3">
  <div class="col-md-7">
    <div class="card p-3">
      <h6><i class="bi bi-calendar-day me-1"></i>Livraisons du jour</h6>
      @if($rotationsJour->isEmpty())
        <p class="text-muted mb-0">Aucune livraison planifiée aujourd'hui.</p>
      @else
      <table class="table table-sm align-middle mb-0">
        <thead><tr><th>Client</th><th>Camion</th><th>Rot.</th><th>Qté</th><th>Statut</th></tr></thead>
        <tbody>
        @foreach($rotationsJour as $r)
          <tr>
            <td>{{ $r->commande->client->nom }}</td>
            <td>{{ $r->camion->matricule }}</td>
            <td>{{ $r->num_ordre }}</td>
            <td>{{ $r->quantite_m3 }} m³</td>
            <td><span class="badge badge-statut bg-{{ ['planifiee' => 'secondary', 'chargement' => 'info', 'en_route' => 'primary', 'sur_chantier' => 'warning', 'terminee' => 'success'][$r->statut] }}">{{ str_replace('_', ' ', $r->statut) }}</span>
            @if($r->delaiDepasse())<span class="badge bg-danger">+90 min !</span>@endif</td>
          </tr>
        @endforeach
        </tbody>
      </table>
      @endif
    </div>
  </div>
  <div class="col-md-5">
    <div class="card p-3">
      <h6><i class="bi bi-exclamation-triangle me-1"></i>Factures impayées</h6>
      @if($facturesImpayees->isEmpty())
        <p class="text-muted mb-0">Aucune facture impayée.</p>
      @else
      <table class="table table-sm align-middle mb-0">
        <thead><tr><th>N°</th><th>Client</th><th>Reste</th><th>Échéance</th></tr></thead>
        <tbody>
        @foreach($facturesImpayees as $f)
          <tr class="{{ $f->date_echeance && $f->date_echeance->isPast() ? 'table-danger' : '' }}">
            <td>{{ $f->numero }}</td>
            <td>{{ $f->client->nom }}</td>
            <td>{{ number_format($f->resteAPayer(), 2, ',', ' ') }} DH</td>
            <td>{{ $f->date_echeance?->format('d/m/Y') }}</td>
          </tr>
        @endforeach
        </tbody>
      </table>
      @endif
    </div>
  </div>
</div>
@endsection
@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
new Chart(document.getElementById('caChart'), {
  type: 'bar',
  data: {
    labels: ['Jan','Fév','Mar','Avr','Mai','Juin','Juil','Août','Sep','Oct','Nov','Déc'],
    datasets: [{ label: 'CA (DH TTC)', data: @json(array_values($caParMois)), backgroundColor: '#c0483c' }]
  },
  options: { plugins: { legend: { display: false } } }
});
new Chart(document.getElementById('produitChart'), {
  type: 'doughnut',
  data: {
    labels: @json(array_keys($ventesParProduit)),
    datasets: [{ data: @json(array_values($ventesParProduit)), backgroundColor: ['#c0483c','#55565a','#e08e6b','#8a8b8f','#9c392f','#c9a86a','#3d3e41'] }]
  }
});
</script>
@endsection
