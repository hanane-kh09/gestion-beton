<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>@yield('title', 'Gestion') — ALHAMRA BETON</title>
<link rel="icon" href="{{ asset('logo.png') }}">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link href="{{ asset('css/theme.css') }}" rel="stylesheet">
</head>
<body>
<div class="d-flex">
  <nav class="sidebar d-flex flex-column p-2" style="width:230px">
    <div class="brand text-center py-3 border-bottom border-secondary mb-2">
      <img src="{{ asset('logo.png') }}" alt="ALHAMRA BETON" style="max-width:160px;">
    </div>
    <ul class="nav nav-pills flex-column">
      <li><a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}"><i class="bi bi-speedometer2 me-2"></i>Tableau de bord</a></li>
      @if(in_array(auth()->user()->role, ['admin', 'commercial']))
      <li><a class="nav-link {{ request()->routeIs('clients.*') ? 'active' : '' }}" href="{{ route('clients.index') }}"><i class="bi bi-people me-2"></i>Clients</a></li>
      <li><a class="nav-link {{ request()->routeIs('commandes.*') ? 'active' : '' }}" href="{{ route('commandes.index') }}"><i class="bi bi-cart me-2"></i>Commandes</a></li>
      @endif
      @if(in_array(auth()->user()->role, ['admin', 'logistique']))
      <li><a class="nav-link {{ request()->routeIs('planning') ? 'active' : '' }}" href="{{ route('planning') }}"><i class="bi bi-calendar-week me-2"></i>Planning</a></li>
      <li><a class="nav-link {{ request()->routeIs('camions.*') ? 'active' : '' }}" href="{{ route('camions.index') }}"><i class="bi bi-truck-front me-2"></i>Camions</a></li>
      <li><a class="nav-link {{ request()->routeIs('chauffeurs.*') ? 'active' : '' }}" href="{{ route('chauffeurs.index') }}"><i class="bi bi-person-badge me-2"></i>Chauffeurs</a></li>
      <li><a class="nav-link {{ request()->routeIs('stock.*', 'formulations.*', 'produits.formulation.*') ? 'active' : '' }}" href="{{ route('stock.index') }}"><i class="bi bi-boxes me-2"></i>Stock</a></li>
      @endif
      @if(in_array(auth()->user()->role, ['admin', 'logistique', 'caissier', 'commercial']))
      <li><a class="nav-link {{ request()->routeIs('bons.*') ? 'active' : '' }}" href="{{ route('bons.index') }}"><i class="bi bi-file-earmark-text me-2"></i>Bons de livraison</a></li>
      @endif
      @if(in_array(auth()->user()->role, ['admin', 'caissier']))
      <li><a class="nav-link {{ request()->routeIs('factures.*') ? 'active' : '' }}" href="{{ route('factures.index') }}"><i class="bi bi-receipt me-2"></i>Factures</a></li>
      <li><a class="nav-link {{ request()->routeIs('paiements.*') ? 'active' : '' }}" href="{{ route('paiements.index') }}"><i class="bi bi-cash-coin me-2"></i>Paiements</a></li>
      @endif
      @if(auth()->user()->role === 'admin')
      <li><a class="nav-link {{ request()->routeIs('produits.*') ? 'active' : '' }}" href="{{ route('produits.index') }}"><i class="bi bi-box me-2"></i>Produits</a></li>
      <li><a class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}" href="{{ route('users.index') }}"><i class="bi bi-person-gear me-2"></i>Utilisateurs</a></li>
      @endif
    </ul>
    <div class="mt-auto p-2 border-top border-secondary">
      <div class="text-light small mb-2"><i class="bi bi-person-circle me-1"></i>{{ auth()->user()->name }}<br><span class="text-secondary">{{ ucfirst(auth()->user()->role) }}</span></div>
      <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button class="btn btn-outline-light btn-sm w-100"><i class="bi bi-box-arrow-right me-1"></i>Déconnexion</button>
      </form>
    </div>
  </nav>
  <main class="flex-grow-1 p-4">
    @if(session('success'))
      <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif
    @if(session('error'))
      <div class="alert alert-danger alert-dismissible fade show">{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif
    @if($errors->any())
      <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
    @endif
    @yield('content')
  </main>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@yield('scripts')
</body>
</html>
