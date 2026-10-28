@extends('layout')
@section('title', 'Clients')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <h3><i class="bi bi-people me-2"></i>Clients</h3>
  <a href="{{ route('clients.create') }}" class="btn btn-brand"><i class="bi bi-plus-lg me-1"></i>Nouveau client</a>
</div>
<div class="card p-3">
  <form class="row g-2 mb-3">
    <div class="col-md-4"><input name="q" class="form-control" placeholder="Rechercher (nom, téléphone, ICE)..." value="{{ request('q') }}"></div>
    <div class="col-md-3">
      <select name="type" class="form-select">
        <option value="">Tous les types</option>
        @foreach(['particulier', 'entreprise', 'promoteur'] as $t)
        <option value="{{ $t }}" @selected(request('type') == $t)>{{ ucfirst($t) }}</option>
        @endforeach
      </select>
    </div>
    <div class="col-auto"><button class="btn btn-outline-secondary"><i class="bi bi-search"></i></button></div>
  </form>
  <table class="table align-middle">
    <thead><tr><th>Nom</th><th>Type</th><th>ICE</th><th>Téléphone</th><th>Solde</th><th></th></tr></thead>
    <tbody>
    @forelse($clients as $c)
      <tr>
        <td><a href="{{ route('clients.show', $c) }}">{{ $c->nom }}</a></td>
        <td><span class="badge bg-secondary">{{ ucfirst($c->type) }}</span></td>
        <td>{{ $c->ice ?: '—' }}</td>
        <td>{{ $c->telephone }}</td>
        <td class="{{ $c->solde() > 0 ? 'text-danger fw-bold' : 'text-success' }}">{{ number_format($c->solde(), 2, ',', ' ') }} DH</td>
        <td class="text-end">
          <a href="{{ route('clients.edit', $c) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
          <form class="d-inline" method="POST" action="{{ route('clients.destroy', $c) }}" onsubmit="return confirm('Supprimer ce client ?')">
            @csrf @method('DELETE')
            <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
          </form>
        </td>
      </tr>
    @empty
      <tr><td colspan="6" class="text-center text-muted">Aucun client trouvé.</td></tr>
    @endforelse
    </tbody>
  </table>
  {{ $clients->links('pagination::bootstrap-5') }}
</div>
@endsection
