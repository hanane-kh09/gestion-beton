<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>{{ $facture->numero }} — ALHAMRA BETON</title>
<style>
body { font-family: 'Times New Roman', serif; margin: 40px; color: #000; }
.entete { display: flex; justify-content: space-between; border-bottom: 3px solid #c0483c; padding-bottom: 10px; }
.entete h1 { margin: 0; color: #c0483c; }
.entete .beton { color: #555; letter-spacing: 3px; }
.titre { text-align: center; margin: 25px 0; font-size: 20px; font-weight: bold; text-decoration: underline; }
.infos { display: flex; justify-content: space-between; margin-bottom: 15px; }
table { width: 100%; border-collapse: collapse; margin-top: 10px; }
td, th { border: 1px solid #333; padding: 8px; }
th { background: #eee; }
.totaux td { border: none; text-align: right; padding: 4px 8px; }
.totaux .grand { font-size: 17px; font-weight: bold; border-top: 2px solid #000; }
.mentions { margin-top: 40px; font-size: 12px; color: #444; border-top: 1px solid #999; padding-top: 8px; }
.no-print { text-align: center; margin-bottom: 20px; }
@media print { .no-print { display: none; } }
</style>
</head>
<body>
<div class="no-print"><button onclick="window.print()" style="padding:8px 24px;font-size:15px;cursor:pointer;">🖨 Imprimer / Enregistrer en PDF</button></div>
<div class="entete">
  <img src="{{ asset('logo.png') }}" alt="ALHAMRA BETON" style="max-height:80px;">
  <div style="text-align:right">
    Centrale à béton — Marrakech<br>
    Tél : 05 24 XX XX XX<br>
    ICE : XXXXXXXXXXXXXXX — IF : XXXXXXX<br>
    RC : XXXXX — Patente : XXXXXXX
  </div>
</div>
<div class="titre">FACTURE N° {{ $facture->numero }}</div>
<div class="infos">
  <div>
    <strong>Facturé à :</strong><br>
    {{ $facture->client->nom }}<br>
    @if($facture->client->adresse){{ $facture->client->adresse }}<br>@endif
    @if($facture->client->ice)ICE : {{ $facture->client->ice }}@endif
  </div>
  <div style="text-align:right">
    Date d'émission : {{ $facture->date_emission->format('d/m/Y') }}<br>
    Date d'échéance : {{ $facture->date_echeance?->format('d/m/Y') ?: '—' }}
  </div>
</div>
<table>
  <thead><tr><th>N° BL</th><th>Date</th><th>Désignation</th><th>Quantité</th><th>P.U. HT (DH)</th><th>Montant HT (DH)</th></tr></thead>
  <tbody>
  @foreach($facture->bonsLivraison as $b)
    <tr>
      <td>{{ $b->numero }}</td>
      <td>{{ $b->date_emission->format('d/m/Y') }}</td>
      <td>{{ $b->rotation->commande->produit->designation }}</td>
      <td>{{ $b->quantite_livree }} m³</td>
      <td style="text-align:right">{{ number_format($b->rotation->commande->prix_unitaire, 2, ',', ' ') }}</td>
      <td style="text-align:right">{{ number_format($b->montant(), 2, ',', ' ') }}</td>
    </tr>
  @endforeach
  </tbody>
</table>
<table class="totaux" style="width:45%; margin-left:auto; margin-top:15px;">
  <tr><td>Total HT :</td><td>{{ number_format($facture->montant_ht, 2, ',', ' ') }} DH</td></tr>
  <tr><td>TVA (20%) :</td><td>{{ number_format($facture->tva, 2, ',', ' ') }} DH</td></tr>
  <tr class="grand"><td>Total TTC :</td><td>{{ number_format($facture->montant_ttc, 2, ',', ' ') }} DH</td></tr>
</table>
<div class="mentions">
  Arrêtée la présente facture à la somme de : {{ number_format($facture->montant_ttc, 2, ',', ' ') }} dirhams TTC.<br>
  Paiement à réception, au plus tard le {{ $facture->date_echeance?->format('d/m/Y') ?: '—' }}.
</div>
</body>
</html>
