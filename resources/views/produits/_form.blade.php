<div class="row g-3">
  <div class="col-md-3">
    <label class="form-label">Code *</label>
    <input name="code" class="form-control" value="{{ old('code', $produit->code ?? '') }}" placeholder="B25" required>
  </div>
  <div class="col-md-6">
    <label class="form-label">Désignation *</label>
    <input name="designation" class="form-control" value="{{ old('designation', $produit->designation ?? '') }}" placeholder="Béton B25 dosé à 350 kg/m³" required>
  </div>
  <div class="col-md-3">
    <label class="form-label">Prix par m³ (DH) *</label>
    <input type="number" step="0.01" name="prix_m3" class="form-control" value="{{ old('prix_m3', $produit->prix_m3 ?? '') }}" required>
  </div>
  <div class="col-12">
    <div class="form-check">
      <input type="hidden" name="actif" value="0">
      <input type="checkbox" name="actif" value="1" class="form-check-input" id="actif" @checked(old('actif', $produit->actif ?? true))>
      <label class="form-check-label" for="actif">Produit actif</label>
    </div>
  </div>
</div>
<div class="mt-3">
  <button class="btn btn-brand"><i class="bi bi-check-lg me-1"></i>Enregistrer</button>
  <a href="{{ route('produits.index') }}" class="btn btn-outline-secondary">Annuler</a>
</div>
