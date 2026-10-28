@extends('layout')
@section('title', 'Paiements')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <h3><i class="bi bi-cash-coin me-2"></i>Paiements</h3>
  <a href="{{ route('paiements.create') }}" class="btn btn-brand"><i class="bi bi-plus-lg me-1"></i>Nouveau paiement</a>
</div>
<div class="card p-3">
  <form class="row g-2 mb-3">
    <div class="col-md-4"><input name="q" class="form-control" placeholder="Rechercher par client..." value="{{ request('q') }}"></div>
    <div class="col-auto"><button class="btn btn-outline-secondary"><i class="bi bi-search"></i></button></div>
  </form>
  <table class="table align-middle">
    <thead><tr><th>Date</th><th>Client</th><th>Facture</th><th>Montant</th><th>Mode</th><th>Référence</th><th>Échéance</th><th>Encaissement</th><th></th></tr></thead>
    <tbody>
    @forelse($paiements as $p)
      <tr class="{{ $p->encaissementDepasse() ? 'table-danger' : '' }}">
        <td>{{ $p->date_paiement->format('d/m/Y') }}</td>
        <td>{{ $p->client->nom }}</td>
        <td>{{ $p->facture?->numero ?: '— (acompte)' }}</td>
        <td class="fw-bold">{{ number_format($p->montant, 2, ',', ' ') }} DH</td>
        <td><span class="badge bg-secondary">{{ $p->modeLabel() }}</span></td>
        <td>{{ $p->reference ?: '—' }}</td>
        <td>{{ $p->date_echeance?->format('d/m/Y') ?: '—' }}</td>
        <td>
          @if($p->statut_encaissement === 'encaisse')
            <span class="badge bg-success">Encaissé</span>
          @elseif($p->statut_encaissement === 'en_attente')
            <span class="badge bg-warning text-dark">En attente</span>
          @else
            —
          @endif
        </td>
        <td class="text-end">
          @if($p->statut_encaissement === 'en_attente')
          <form class="d-inline" method="POST" action="{{ route('paiements.encaisser', $p) }}">
            @csrf
            <button class="btn btn-sm btn-outline-success" title="Marquer encaissé"><i class="bi bi-check2-circle"></i></button>
          </form>
          @endif
          <a href="{{ route('paiements.edit', $p) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
          <form class="d-inline" method="POST" action="{{ route('paiements.destroy', $p) }}" onsubmit="return confirm('Supprimer ce paiement ?')">
            @csrf @method('DELETE')
            <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
          </form>
        </td>
      </tr>
    @empty
      <tr><td colspan="9" class="text-center text-muted">Aucun paiement.</td></tr>
    @endforelse
    </tbody>
  </table>
  {{ $paiements->links('pagination::bootstrap-5') }}
</div>
@endsection
