<div class="row g-3">
  <div class="col-md-4">
    <label class="form-label">Nom *</label>
    <input name="nom" class="form-control" value="{{ old('nom', $chauffeur->nom ?? '') }}" required>
  </div>
  <div class="col-md-4">
    <label class="form-label">Prénom *</label>
    <input name="prenom" class="form-control" value="{{ old('prenom', $chauffeur->prenom ?? '') }}" required>
  </div>
  <div class="col-md-4">
    <label class="form-label">CIN *</label>
    <input name="cin" class="form-control" value="{{ old('cin', $chauffeur->cin ?? '') }}" required>
  </div>
  <div class="col-md-4">
    <label class="form-label">N° permis *</label>
    <input name="num_permis" class="form-control" value="{{ old('num_permis', $chauffeur->num_permis ?? '') }}" required>
  </div>
  <div class="col-md-4">
    <label class="form-label">Téléphone</label>
    <input name="telephone" class="form-control" value="{{ old('telephone', $chauffeur->telephone ?? '') }}">
  </div>
  <div class="col-md-4">
    <label class="form-label">Camion affecté</label>
    <select name="camion_id" class="form-select">
      <option value="">— Aucun —</option>
      @foreach($camions as $c)
      <option value="{{ $c->id }}" @selected(old('camion_id', $chauffeur->camion_id ?? '') == $c->id)>{{ $c->matricule }} ({{ $c->capacite_m3 }} m³)</option>
      @endforeach
    </select>
  </div>
</div>
<div class="mt-3">
  <button class="btn btn-brand"><i class="bi bi-check-lg me-1"></i>Enregistrer</button>
  <a href="{{ route('chauffeurs.index') }}" class="btn btn-outline-secondary">Annuler</a>
</div>
