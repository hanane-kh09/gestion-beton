@extends('layout')
@section('title', 'Bons de livraison')
@section('content')
<h3 class="mb-3"><i class="bi bi-file-earmark-text me-2"></i>Bons de livraison</h3>
<div class="card p-3">
  <form class="row g-2 mb-3">
    <div class="col-md-4"><input name="q" class="form-control" placeholder="Rechercher (n° BL, client)..." value="{{ request('q') }}"></div>
    <div class="col-auto"><button class="btn btn-outline-secondary"><i class="bi bi-search"></i></button></div>
  </form>
  <table class="table align-middle">
    <thead><tr><th>N° BL</th><th>Date</th><th>Client</th><th>Camion</th><th>Qté livrée</th><th>Signé</th><th>Facture</th><th></th></tr></thead>
    <tbody>
    @forelse($bons as $b)
      <tr>
        <td class="fw-bold">{{ $b->numero }}</td>
        <td>{{ $b->date_emission->format('d/m/Y') }}</td>
        <td>{{ $b->rotation->commande->client->nom }}</td>
        <td>{{ $b->rotation->camion->matricule }}</td>
        <td>{{ $b->quantite_livree }} m³</td>
        <td>{!! $b->signe ? '<span class="badge bg-success">Oui</span>' : '<span class="badge bg-secondary">Non</span>' !!}</td>
        <td>{{ $b->facture?->numero ?: '—' }}</td>
        <td class="text-end">
          <a href="{{ route('bons.show', $b) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i></a>
          <a href="{{ route('bons.edit', $b) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
          <a href="{{ route('bons.imprimer', $b) }}" target="_blank" class="btn btn-sm btn-outline-secondary"><i class="bi bi-printer"></i></a>
        </td>
      </tr>
    @empty
      <tr><td colspan="8" class="text-center text-muted">Aucun bon de livraison.</td></tr>
    @endforelse
    </tbody>
  </table>
  {{ $bons->links('pagination::bootstrap-5') }}
</div>
@endsection
