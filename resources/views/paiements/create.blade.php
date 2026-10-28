@extends('layout')
@section('title', 'Nouveau paiement')
@section('content')
<h3 class="mb-3"><i class="bi bi-cash-coin me-2"></i>Enregistrer un paiement</h3>
<div class="card p-4">
  <form class="row g-2 mb-3">
    <div class="col-md-6">
      <label class="form-label">Client *</label>
      <select name="client_id" class="form-select" onchange="this.form.submit()">
        <option value="">— Choisir un client —</option>
        @foreach($clients as $cl)
        <option value="{{ $cl->id }}" @selected(request('client_id') == $cl->id)>{{ $cl->nom }} (solde : {{ number_format($cl->solde(), 2, ',', ' ') }} DH)</option>
        @endforeach
      </select>
    </div>
  </form>
  @if(request('client_id'))
  <form method="POST" action="{{ route('paiements.store') }}">
    @csrf
    <input type="hidden" name="client_id" value="{{ request('client_id') }}">
    <div class="row g-3">
      <div class="col-md-4">
        <label class="form-label">Facture concernée</label>
        <select name="facture_id" class="form-select">
          <option value="">— Paiement libre / acompte —</option>
          @foreach($factures as $f)
          <option value="{{ $f->id }}">{{ $f->numero }} — reste {{ number_format($f->resteAPayer(), 2, ',', ' ') }} DH</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-2">
        <label class="form-label">Date *</label>
        <input type="date" name="date_paiement" class="form-control" value="{{ today()->toDateString() }}" required>
      </div>
      <div class="col-md-2">
        <label class="form-label">Montant (DH) *</label>
        <input type="number" step="0.01" min="0.01" name="montant" class="form-control" required>
      </div>
      <div class="col-md-2">
        <label class="form-label">Mode *</label>
        <select name="mode" id="mode" class="form-select" required>
          @foreach(['especes' => 'Espèces', 'cheque' => 'Chèque', 'virement' => 'Virement', 'effet' => 'Effet (traite)'] as $k => $v)
          <option value="{{ $k }}">{{ $v }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-2">
        <label class="form-label">Référence</label>
        <input name="reference" class="form-control" placeholder="N° chèque...">
      </div>
    </div>
    <div id="champsEcheance" class="row g-3 mt-0" style="display:none;">
      <div class="col-md-3">
        <label class="form-label">Date d'échéance / d'encaissement</label>
        <input type="date" name="date_echeance" class="form-control">
      </div>
      <div class="col-md-3">
        <label class="form-label">Statut d'encaissement</label>
        <select name="statut_encaissement" class="form-select">
          <option value="en_attente" selected>En attente</option>
          <option value="encaisse">Encaissé</option>
        </select>
      </div>
    </div>
    <div class="mt-3">
      <button class="btn btn-brand"><i class="bi bi-check-lg me-1"></i>Enregistrer</button>
      <a href="{{ route('paiements.index') }}" class="btn btn-outline-secondary">Annuler</a>
    </div>
  </form>
  @endif
</div>
@endsection
@section('scripts')
<script>
function toggleChampsEcheance() {
  const mode = document.getElementById('mode').value;
  document.getElementById('champsEcheance').style.display = (mode === 'cheque' || mode === 'effet') ? 'flex' : 'none';
}
const modeSelect = document.getElementById('mode');
if (modeSelect) {
  modeSelect.addEventListener('change', toggleChampsEcheance);
  toggleChampsEcheance();
}
</script>
@endsection
