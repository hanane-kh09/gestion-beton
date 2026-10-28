<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Connexion — ALHAMRA BETON</title>
<link rel="icon" href="{{ asset('logo.png') }}">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link href="{{ asset('css/theme.css') }}" rel="stylesheet">
</head>
<body class="login-bg d-flex align-items-center justify-content-center">
<div class="card login-card" style="width:400px;">
  <div class="card-body p-4">
    <div class="text-center mb-4">
      <img src="{{ asset('logo.png') }}" alt="ALHAMRA BETON" style="max-width:180px;">
      <h4 class="fw-bold mt-2 mb-0">ALHAMRA BETON</h4>
      <small class="text-muted">Gestion des ventes de béton</small>
    </div>
    @if($errors->any())
      <div class="alert alert-danger py-2">{{ $errors->first() }}</div>
    @endif
    <form method="POST" action="{{ route('login.post') }}">
      @csrf
      <div class="mb-3">
        <label class="form-label">Adresse e-mail</label>
        <input type="email" name="email" class="form-control" value="{{ old('email') }}" required autofocus>
      </div>
      <div class="mb-3">
        <label class="form-label">Mot de passe</label>
        <input type="password" name="password" class="form-control" required>
      </div>
      <div class="form-check mb-3">
        <input type="checkbox" name="remember" class="form-check-input" id="remember">
        <label class="form-check-label" for="remember">Se souvenir de moi</label>
      </div>
      <button class="btn btn-brand w-100">Se connecter</button>
    </form>
  </div>
</div>
</body>
</html>
