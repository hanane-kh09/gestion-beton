@extends('layout')
@section('title', 'Nouvelle facture')
@section('content')
<h3 class="mb-3"><i class="bi bi-receipt me-2"></i>Nouvelle facture</h3>
<div class="card p-4 mb-3">
  <form class="row g-2">
    <div class="col-md-6">
      <label class="form-label">Client</label>
      <select name="client_id" class="form-select" onchange="this.form.submit()">
        <option value="">— Choisir un client —</option>
        @foreach($clients as $cl)
        <option value="{{ $cl->id }}" @selected(($client->id ?? null) == $cl->id)>{{ $cl->nom }}</option>
        @endforeach
      </select>
    </div>
  </form>
</div>
@if($client)
<div class="card p-4">
  <h6>Bons de livraison non facturés de {{ $client->nom }}</h6>
  @if($bons->isEmpty())
    <p class="text-muted mb-0">Aucun bon de livraison à facturer pour ce client.</p>
  @else
  <form method="POST" action="{{ route('factures.store') }}">
    @csrf
    <input type="hidden" name="client_id" value="{{ $client->id }}">
    <table class="table align-middle">
      <thead><tr><th></th><th>N° BL</th><th>Date</th><th>Produit</th><th>Qté</th><th>Prix unitaire</th><th>Montant HT</th></tr></thead>
      <tbody>
      @foreach($bons as $b)
        <tr>
          <td><input type="checkbox" name="bons[]" value="{{ $b->id }}" class="form-check-input bl-check" data-montant="{{ $b->montant() }}" checked></td>
          <td>{{ $b->numero }}</td>
          <td>{{ $b->date_emission->format('d/m/Y') }}</td>
          <td>{{ $b->rotation->commande->produit->code }}</td>
          <td>{{ $b->quantite_livree }} m³</td>
          <td>{{ number_format($b->rotation->commande->prix_unitaire, 2, ',', ' ') }} DH</td>
          <td>{{ number_format($b->montant(), 2, ',', ' ') }} DH</td>
        </tr>
      @endforeach
      </tbody>
    </table>
    <div class="row g-3 align-items-end">
      <div class="col-md-3">
        <label class="form-label">Date d'échéance</label>
        <input type="date" name="date_echeance" class="form-control" value="{{ now()->addDays(30)->toDateString() }}">
      </div>
      <div class="col-md-9 text-end">
        <div>Total HT : <strong id="ht">0,00</strong> DH — TVA 20% : <strong id="tva">0,00</strong> DH — <span class="fs-5">TTC : <strong id="ttc">0,00</strong> DH</span></div>
      </div>
    </div>
    <div class="mt-3">
      <button class="btn btn-brand"><i class="bi bi-check-lg me-1"></i>Générer la facture</button>
      <a href="{{ route('factures.index') }}" class="btn btn-outline-secondary">Annuler</a>
    </div>
  </form>
  @endif
</div>
@endif
@endsection
@section('scripts')
<script>
function fmt(n) { return n.toLocaleString('fr-FR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }); }
function totaux() {
  let ht = 0;
  document.querySelectorAll('.bl-check:checked').forEach(c => ht += parseFloat(c.dataset.montant));
  const tva = ht * 0.20;
  document.getElementById('ht').textContent = fmt(ht);
  document.getElementById('tva').textContent = fmt(tva);
  document.getElementById('ttc').textContent = fmt(ht + tva);
}
document.querySelectorAll('.bl-check').forEach(c => c.addEventListener('change', totaux));
totaux();
</script>
@endsection
