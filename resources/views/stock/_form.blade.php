<div class="row g-3">
  <div class="col-md-6">
    <label class="form-label">Nom *</label>
    <input name="nom" class="form-control" value="{{ old('nom', $matiere->nom ?? '') }}" required>
  </div>
  <div class="col-md-2">
    <label class="form-label">Unité *</label>
    <select name="unite" class="form-select" required>
      @foreach(['tonne', 'm3', 'litre', 'kg'] as $u)
      <option value="{{ $u }}" @selected(old('unite', $matiere->unite ?? '') == $u)>{{ $u }}</option>
      @endforeach
    </select>
  </div>
  <div class="col-md-2">
    <label class="form-label">Stock actuel *</label>
    <input type="number" step="0.01" min="0" name="stock_actuel" class="form-control" value="{{ old('stock_actuel', $matiere->stock_actuel ?? 0) }}" required>
  </div>
  <div class="col-md-2">
    <label class="form-label">Seuil d'alerte *</label>
    <input type="number" step="0.01" min="0" name="seuil_alerte" class="form-control" value="{{ old('seuil_alerte', $matiere->seuil_alerte ?? 0) }}" required>
  </div>
</div>
<div class="mt-3">
  <button class="btn btn-brand"><i class="bi bi-check-lg me-1"></i>Enregistrer</button>
  <a href="{{ route('stock.index') }}" class="btn btn-outline-secondary">Annuler</a>
</div>
