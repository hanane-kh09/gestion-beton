@extends('layout')
@section('title', 'Produits')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <h3><i class="bi bi-box me-2"></i>Catalogue produits</h3>
  <a href="{{ route('produits.create') }}" class="btn btn-brand"><i class="bi bi-plus-lg me-1"></i>Nouveau produit</a>
</div>
<div class="card p-3">
  <table class="table align-middle">
    <thead><tr><th>Code</th><th>Désignation</th><th>Prix / m³</th><th>Statut</th><th></th></tr></thead>
    <tbody>
    @forelse($produits as $p)
      <tr>
        <td class="fw-bold">{{ $p->code }}</td>
        <td>{{ $p->designation }}</td>
        <td>{{ number_format($p->prix_m3, 2, ',', ' ') }} DH</td>
        <td>{!! $p->actif ? '<span class="badge bg-success">Actif</span>' : '<span class="badge bg-secondary">Inactif</span>' !!}</td>
        <td class="text-end">
          <a href="{{ route('produits.edit', $p) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
          <form class="d-inline" method="POST" action="{{ route('produits.destroy', $p) }}" onsubmit="return confirm('Supprimer ce produit ?')">
            @csrf @method('DELETE')
            <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
          </form>
        </td>
      </tr>
    @empty
      <tr><td colspan="5" class="text-center text-muted">Aucun produit.</td></tr>
    @endforelse
    </tbody>
  </table>
  {{ $produits->links('pagination::bootstrap-5') }}
</div>
@endsection
