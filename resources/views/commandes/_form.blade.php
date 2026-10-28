<div class="row g-3">
  <div class="col-md-6">
    <label class="form-label">Client *</label>
    <select name="client_id" class="form-select" required>
      <option value="">— Choisir un client —</option>
      @foreach($clients as $cl)
      <option value="{{ $cl->id }}" @selected(old('client_id', $commande->client_id ?? '') == $cl->id)>{{ $cl->nom }} ({{ ucfirst($cl->type) }})</option>
      @endforeach
    </select>
  </div>
  <div class="col-md-3">
    <label class="form-label">Type de béton *</label>
    <select name="produit_id" id="produit" class="form-select" required>
      <option value="">— Choisir —</option>
      @foreach($produits as $p)
      <option value="{{ $p->id }}" data-prix="{{ $p->prix_m3 }}" @selected(old('produit_id', $commande->produit_id ?? '') == $p->id)>{{ $p->code }} — {{ number_format($p->prix_m3, 2) }} DH/m³</option>
      @endforeach
    </select>
  </div>
  <div class="col-md-3">
    <label class="form-label">Quantité (m³) *</label>
    <input type="number" step="0.5" min="0.5" name="quantite_m3" id="quantite" class="form-control" value="{{ old('quantite_m3', $commande->quantite_m3 ?? '') }}" required>
  </div>
  <div class="col-md-4">
    <label class="form-label">Date et heure de livraison *</label>
    <input type="datetime-local" name="date_livraison" class="form-control" value="{{ old('date_livraison', isset($commande) ? $commande->date_livraison->format('Y-m-d\TH:i') : '') }}" required>
  </div>
  <div class="col-md-8">
    <label class="form-label">Adresse du chantier *</label>
    <input name="adresse_chantier" class="form-control" value="{{ old('adresse_chantier', $commande->adresse_chantier ?? '') }}" required>
  </div>
  <div class="col-md-8">
    <label class="form-label">Notes</label>
    <textarea name="notes" class="form-control" rows="2">{{ old('notes', $commande->notes ?? '') }}</textarea>
  </div>
  <div class="col-md-4">
    <label class="form-label">Montant estimé</label>
    <div class="form-control bg-light fw-bold" id="montant">—</div>
  </div>
</div>
<div class="mt-3">
  <button class="btn btn-brand"><i class="bi bi-check-lg me-1"></i>Enregistrer</button>
  <a href="{{ route('commandes.index') }}" class="btn btn-outline-secondary">Annuler</a>
</div>
@section('scripts')
<script>
function calc() {
  const opt = document.querySelector('#produit option:checked');
  const prix = opt ? parseFloat(opt.dataset.prix || 0) : 0;
  const qte = parseFloat(document.getElementById('quantite').value || 0);
  document.getElementById('montant').textContent = prix && qte ? (prix * qte).toFixed(2) + ' DH' : '—';
}
document.getElementById('produit').addEventListener('change', calc);
document.getElementById('quantite').addEventListener('input', calc);
calc();
</script>
@endsection
