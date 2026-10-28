@extends('layout')
@section('title', 'Factures')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <h3><i class="bi bi-receipt me-2"></i>Factures</h3>
  <a href="{{ route('factures.create') }}" class="btn btn-brand"><i class="bi bi-plus-lg me-1"></i>Nouvelle facture</a>
</div>
<div class="card p-3">
  <form class="row g-2 mb-3">
    <div class="col-md-4"><input name="q" class="form-control" placeholder="Rechercher (n°, client)..." value="{{ request('q') }}"></div>
    <div class="col-md-3">
      <select name="statut" class="form-select">
        <option value="">Tous les statuts</option>
        @foreach(['impayee', 'partielle', 'payee'] as $s)
        <option value="{{ $s }}" @selected(request('statut') == $s)>{{ ucfirst($s) }}</option>
        @endforeach
      </select>
    </div>
    <div class="col-auto"><button class="btn btn-outline-secondary"><i class="bi bi-search"></i></button></div>
  </form>
  <table class="table align-middle">
    <thead><tr><th>N°</th><th>Date</th><th>Client</th><th>HT</th><th>TTC</th><th>Payé</th><th>Statut</th><th></th></tr></thead>
    <tbody>
    @forelse($factures as $f)
      <tr>
        <td class="fw-bold">{{ $f->numero }}</td>
        <td>{{ $f->date_emission->format('d/m/Y') }}</td>
        <td>{{ $f->client->nom }}</td>
        <td>{{ number_format($f->montant_ht, 2, ',', ' ') }}</td>
        <td>{{ number_format($f->montant_ttc, 2, ',', ' ') }}</td>
        <td>{{ number_format($f->totalPaye(), 2, ',', ' ') }}</td>
        <td><span class="badge bg-{{ ['impayee' => 'danger', 'partielle' => 'warning', 'payee' => 'success'][$f->statut] }}">{{ $f->statut }}</span></td>
        <td class="text-end">
          <a href="{{ route('factures.show', $f) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i></a>
          <a href="{{ route('factures.imprimer', $f) }}" target="_blank" class="btn btn-sm btn-outline-secondary"><i class="bi bi-printer"></i></a>
        </td>
      </tr>
    @empty
      <tr><td colspan="8" class="text-center text-muted">Aucune facture.</td></tr>
    @endforelse
    </tbody>
  </table>
  {{ $factures->links('pagination::bootstrap-5') }}
</div>
@endsection
