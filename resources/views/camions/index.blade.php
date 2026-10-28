@extends('layout')
@section('title', 'Camions')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <h3><i class="bi bi-truck-front me-2"></i>Flotte de camions</h3>
  <a href="{{ route('camions.create') }}" class="btn btn-brand"><i class="bi bi-plus-lg me-1"></i>Nouveau camion</a>
</div>
<div class="card p-3">
  <table class="table align-middle">
    <thead><tr><th>Matricule</th><th>Capacité</th><th>État</th><th>Chauffeur affecté</th><th></th></tr></thead>
    <tbody>
    @forelse($camions as $c)
      <tr>
        <td class="fw-bold">{{ $c->matricule }}</td>
        <td>{{ $c->capacite_m3 }} m³</td>
        <td><span class="badge bg-{{ ['disponible' => 'success', 'en_mission' => 'primary', 'en_panne' => 'danger', 'maintenance' => 'warning'][$c->etat] }}">{{ str_replace('_', ' ', $c->etat) }}</span></td>
        <td>{{ $c->chauffeur?->nomComplet() ?: '—' }}</td>
        <td class="text-end">
          <a href="{{ route('camions.edit', $c) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
          <form class="d-inline" method="POST" action="{{ route('camions.destroy', $c) }}" onsubmit="return confirm('Supprimer ce camion ?')">
            @csrf @method('DELETE')
            <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
          </form>
        </td>
      </tr>
    @empty
      <tr><td colspan="5" class="text-center text-muted">Aucun camion.</td></tr>
    @endforelse
    </tbody>
  </table>
  {{ $camions->links('pagination::bootstrap-5') }}
</div>
@endsection
