@extends('layout')
@section('title', 'Formulations des produits')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <h3><i class="bi bi-clipboard-data me-2"></i>Formulations des produits</h3>
  <a href="{{ route('stock.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Retour au stock</a>
</div>
<div class="card p-3">
  <table class="table align-middle">
    <thead><tr><th>Code</th><th>Désignation</th><th>Matières configurées</th><th></th></tr></thead>
    <tbody>
    @forelse($produits as $p)
      <tr>
        <td class="fw-bold">{{ $p->code }}</td>
        <td>{{ $p->designation }}</td>
        <td>{{ $p->formulations_count }} matière(s)</td>
        <td class="text-end"><a href="{{ route('produits.formulation.edit', $p) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil me-1"></i>Configurer</a></td>
      </tr>
    @empty
      <tr><td colspan="4" class="text-center text-muted">Aucun produit.</td></tr>
    @endforelse
    </tbody>
  </table>
</div>
@endsection
