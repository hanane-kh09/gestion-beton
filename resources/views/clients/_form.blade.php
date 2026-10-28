<div class="row g-3">
  <div class="col-md-4">
    <label class="form-label">Type *</label>
    <select name="type" class="form-select" required>
      @foreach(['particulier', 'entreprise', 'promoteur'] as $t)
      <option value="{{ $t }}" @selected(old('type', $client->type ?? '') == $t)>{{ ucfirst($t) }}</option>
      @endforeach
    </select>
  </div>
  <div class="col-md-8">
    <label class="form-label">Nom / Raison sociale *</label>
    <input name="nom" class="form-control" value="{{ old('nom', $client->nom ?? '') }}" required>
  </div>
  <div class="col-md-4">
    <label class="form-label">ICE</label>
    <input name="ice" class="form-control" value="{{ old('ice', $client->ice ?? '') }}">
  </div>
  <div class="col-md-4">
    <label class="form-label">Téléphone *</label>
    <input name="telephone" class="form-control" value="{{ old('telephone', $client->telephone ?? '') }}" required>
  </div>
  <div class="col-md-4">
    <label class="form-label">E-mail</label>
    <input type="email" name="email" class="form-control" value="{{ old('email', $client->email ?? '') }}">
  </div>
  <div class="col-12">
    <label class="form-label">Adresse</label>
    <input name="adresse" class="form-control" value="{{ old('adresse', $client->adresse ?? '') }}">
  </div>
</div>
<div class="mt-3">
  <button class="btn btn-brand"><i class="bi bi-check-lg me-1"></i>Enregistrer</button>
  <a href="{{ route('clients.index') }}" class="btn btn-outline-secondary">Annuler</a>
</div>
