@extends('layout')
@section('title', $facture->numero)
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <h3><i class="bi bi-receipt me-2"></i>{{ $facture->numero }}
    <span class="badge fs-6 bg-{{ ['impayee' => 'danger', 'partielle' => 'warning', 'payee' => 'success'][$facture->statut] }}">{{ $facture->statut }}</span>
  </h3>
  <div>
    <a href="{{ route('factures.edit', $facture) }}" class="btn btn-outline-primary"><i class="bi bi-pencil me-1"></i>Modifier</a>
    <a href="{{ route('factures.imprimer', $facture) }}" target="_blank" class="btn btn-outline-secondary"><i class="bi bi-printer me-1"></i>Imprimer / PDF</a>
    <form class="d-inline" method="POST" action="{{ route('factures.destroy', $facture) }}" onsubmit="return confirm('Supprimer cette facture ? Les BL seront libérés.')">
      @csrf @method('DELETE')
      <button class="btn btn-outline-danger"><i class="bi bi-trash"></i></button>
    </form>
  </div>
</div>
<div class="row g-3 mb-3">
  <div class="col-md-3"><div class="card p-3"><div class="text-muted small">Client</div><div class="fw-bold">{{ $facture->client->nom }}</div></div></div>
  <div class="col-md-2"><div class="card p-3"><div class="text-muted small">Émission</div><div class="fw-bold">{{ $facture->date_emission->format('d/m/Y') }}</div></div></div>
  <div class="col-md-2"><div class="card p-3"><div class="text-muted small">Échéance</div><div class="fw-bold">{{ $facture->date_echeance?->format('d/m/Y') ?: '—' }}</div></div></div>
  <div class="col-md-2"><div class="card p-3"><div class="text-muted small">TTC</div><div class="fw-bold">{{ number_format($facture->montant_ttc, 2, ',', ' ') }} DH</div></div></div>
  <div class="col-md-3"><div class="card p-3"><div class="text-muted small">Reste à payer</div><div class="fw-bold {{ $facture->resteAPayer() > 0 ? 'text-danger' : 'text-success' }}">{{ number_format($facture->resteAPayer(), 2, ',', ' ') }} DH</div></div></div>
</div>
<div class="card p-3 mb-3">
  <h6>Bons de livraison facturés</h6>
  <table class="table table-sm mb-0">
    <thead><tr><th>N° BL</th><th>Date</th><th>Produit</th><th>Qté</th><th>Montant HT</th><th></th></tr></thead>
    <tbody>
    @foreach($facture->bonsLivraison as $b)
      <tr>
        <td><a href="{{ route('bons.show', $b) }}">{{ $b->numero }}</a></td>
        <td>{{ $b->date_emission->format('d/m/Y') }}</td>
        <td>{{ $b->rotation->commande->produit->code }}</td>
        <td>{{ $b->quantite_livree }} m³</td>
        <td>{{ number_format($b->montant(), 2, ',', ' ') }} DH</td>
        <td class="text-end">
          <form method="POST" action="{{ route('factures.bons.retirer', [$facture, $b]) }}" onsubmit="return confirm('Retirer ce bon de la facture ?')">
            @csrf
            <button class="btn btn-sm btn-outline-danger" title="Retirer de la facture"><i class="bi bi-x-lg"></i></button>
          </form>
        </td>
      </tr>
    @endforeach
    </tbody>
    <tfoot>
      <tr><th colspan="5" class="text-end">Total HT</th><th>{{ number_format($facture->montant_ht, 2, ',', ' ') }} DH</th></tr>
      <tr><th colspan="5" class="text-end">TVA 20%</th><th>{{ number_format($facture->tva, 2, ',', ' ') }} DH</th></tr>
      <tr><th colspan="5" class="text-end">Total TTC</th><th>{{ number_format($facture->montant_ttc, 2, ',', ' ') }} DH</th></tr>
    </tfoot>
  </table>
</div>
<div class="card p-3">
  <h6>Paiements reçus</h6>
  <table class="table table-sm mb-0">
    <thead><tr><th>Date</th><th>Montant</th><th>Mode</th><th>Référence</th></tr></thead>
    <tbody>
    @forelse($facture->paiements as $p)
      <tr><td>{{ $p->date_paiement->format('d/m/Y') }}</td><td>{{ number_format($p->montant, 2, ',', ' ') }} DH</td><td>{{ $p->modeLabel() }}</td><td>{{ $p->reference ?: '—' }}</td></tr>
    @empty
      <tr><td colspan="4" class="text-muted text-center">Aucun paiement reçu.</td></tr>
    @endforelse
    </tbody>
  </table>
</div>
@endsection
