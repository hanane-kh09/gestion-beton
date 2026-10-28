@extends('layout')
@section('title', 'Modifier un paiement')
@section('content')
<h3 class="mb-3"><i class="bi bi-cash-coin me-2"></i>Modifier le paiement</h3>
<div class="card p-4">
  <form method="POST" action="{{ route('paiements.update', $paiement) }}">
    @csrf @method('PUT')
    <div class="row g-3">
      <div class="col-md-4">
        <label class="form-label">Client *</label>
        <select name="client_id" class="form-select" required>
          @foreach($clients as $cl)
          <option value="{{ $cl->id }}" @selected(old('client_id', $paiement->client_id) == $cl->id)>{{ $cl->nom }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-4">
        <label class="form-label">Facture concernée</label>
        <select name="facture_id" class="form-select">
          <option value="">— Paiement libre / acompte —</option>
          @foreach($factures as $f)
          <option value="{{ $f->id }}" @selected(old('facture_id', $paiement->facture_id) == $f->id)>{{ $f->numero }} — reste {{ number_format($f->resteAPayer(), 2, ',', ' ') }} DH</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-2">
        <label class="form-label">Date *</label>
        <input type="date" name="date_paiement" class="form-control" value="{{ old('date_paiement', $paiement->date_paiement->toDateString()) }}" required>
      </div>
      <div class="col-md-2">
        <label class="form-label">Montant (DH) *</label>
        <input type="number" step="0.01" min="0.01" name="montant" class="form-control" value="{{ old('montant', $paiement->montant) }}" required>
      </div>
      <div class="col-md-3">
        <label class="form-label">Mode *</label>
        <select name="mode" id="mode" class="form-select" required>
          @foreach(['especes' => 'Espèces', 'cheque' => 'Chèque', 'virement' => 'Virement', 'effet' => 'Effet (traite)'] as $k => $v)
          <option value="{{ $k }}" @selected(old('mode', $paiement->mode) == $k)>{{ $v }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-3">
        <label class="form-label">Référence</label>
        <input name="reference" class="form-control" placeholder="N° chèque..." value="{{ old('reference', $paiement->reference) }}">
      </div>
    </div>
    <div id="champsEcheance" class="row g-3 mt-0" style="display:none;">
      <div class="col-md-3">
        <label class="form-label">Date d'échéance / d'encaissement</label>
        <input type="date" name="date_echeance" class="form-control" value="{{ old('date_echeance', $paiement->date_echeance?->toDateString()) }}">
      </div>
      <div class="col-md-3">
        <label class="form-label">Statut d'encaissement</label>
        <select name="statut_encaissement" class="form-select">
          <option value="en_attente" @selected(old('statut_encaissement', $paiement->statut_encaissement ?: 'en_attente') == 'en_attente')>En attente</option>
          <option value="encaisse" @selected(old('statut_encaissement', $paiement->statut_encaissement) == 'encaisse')>Encaissé</option>
        </select>
      </div>
    </div>
    <div class="mt-3">
      <button class="btn btn-brand"><i class="bi bi-check-lg me-1"></i>Enregistrer</button>
      <a href="{{ route('paiements.index') }}" class="btn btn-outline-secondary">Annuler</a>
    </div>
  </form>
</div>
@endsection
@section('scripts')
<script>
function toggleChampsEcheance() {
  const mode = document.getElementById('mode').value;
  document.getElementById('champsEcheance').style.display = (mode === 'cheque' || mode === 'effet') ? 'flex' : 'none';
}
document.getElementById('mode').addEventListener('change', toggleChampsEcheance);
toggleChampsEcheance();
</script>
@endsection
