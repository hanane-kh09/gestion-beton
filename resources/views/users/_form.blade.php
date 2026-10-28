<div class="row g-3">
  <div class="col-md-6">
    <label class="form-label">Nom complet *</label>
    <input name="name" class="form-control" value="{{ old('name', $user->name ?? '') }}" required>
  </div>
  <div class="col-md-6">
    <label class="form-label">E-mail *</label>
    <input type="email" name="email" class="form-control" value="{{ old('email', $user->email ?? '') }}" required>
  </div>
  <div class="col-md-6">
    <label class="form-label">Mot de passe {{ isset($user) ? '(laisser vide pour conserver)' : '*' }}</label>
    <input type="password" name="password" class="form-control" {{ isset($user) ? '' : 'required' }}>
  </div>
  <div class="col-md-6">
    <label class="form-label">Rôle *</label>
    <select name="role" class="form-select" required>
      @foreach(['admin' => 'Administrateur', 'commercial' => 'Commercial', 'logistique' => 'Responsable logistique', 'caissier' => 'Caissier / Comptable'] as $k => $v)
      <option value="{{ $k }}" @selected(old('role', $user->role ?? '') == $k)>{{ $v }}</option>
      @endforeach
    </select>
  </div>
</div>
<div class="mt-3">
  <button class="btn btn-brand"><i class="bi bi-check-lg me-1"></i>Enregistrer</button>
  <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">Annuler</a>
</div>
