@extends('layout')
@section('title', 'Chauffeurs')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <h3><i class="bi bi-person-badge me-2"></i>Chauffeurs</h3>
  <a href="{{ route('chauffeurs.create') }}" class="btn btn-brand"><i class="bi bi-plus-lg me-1"></i>Nouveau chauffeur</a>
</div>
<div class="card p-3">
  <table class="table align-middle">
    <thead><tr><th>Nom complet</th><th>CIN</th><th>Permis</th><th>Téléphone</th><th>Camion</th><th></th></tr></thead>
    <tbody>
    @forelse($chauffeurs as $ch)
      <tr>
        <td>{{ $ch->nomComplet() }}</td>
        <td>{{ $ch->cin }}</td>
        <td>{{ $ch->num_permis }}</td>
        <td>{{ $ch->telephone ?: '—' }}</td>
        <td>{{ $ch->camion?->matricule ?: '—' }}</td>
        <td class="text-end">
          <a href="{{ route('chauffeurs.edit', $ch) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
          <form class="d-inline" method="POST" action="{{ route('chauffeurs.destroy', $ch) }}" onsubmit="return confirm('Supprimer ce chauffeur ?')">
            @csrf @method('DELETE')
            <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
          </form>
        </td>
      </tr>
    @empty
      <tr><td colspan="6" class="text-center text-muted">Aucun chauffeur.</td></tr>
    @endforelse
    </tbody>
  </table>
  {{ $chauffeurs->links('pagination::bootstrap-5') }}
</div>
@endsection
