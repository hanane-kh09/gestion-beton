<div class="row g-3">
  <div class="col-md-4">
    <label class="form-label">Matricule *</label>
    <input name="matricule" class="form-control" value="{{ old('matricule', $camion->matricule ?? '') }}" placeholder="12345-A-40" required>
  </div>
  <div class="col-md-4">
    <label class="form-label">Capacité (m³) *</label>
    <input type="number" step="0.5" name="capacite_m3" class="form-control" value="{{ old('capacite_m3', $camion->capacite_m3 ?? 8) }}" required>
  </div>
  <div class="col-md-4">
    <label class="form-label">État *</label>
    <select name="etat" class="form-select" required>
      @foreach(['disponible', 'en_mission', 'en_panne', 'maintenance'] as $e)
      <option value="{{ $e }}" @selected(old('etat', $camion->etat ?? 'disponible') == $e)>{{ ucfirst(str_replace('_', ' ', $e)) }}</option>
      @endforeach
    </select>
  </div>
</div>
<div class="mt-3">
  <button class="btn btn-brand"><i class="bi bi-check-lg me-1"></i>Enregistrer</button>
  <a href="{{ route('camions.index') }}" class="btn btn-outline-secondary">Annuler</a>
</div>
