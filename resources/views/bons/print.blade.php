<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>{{ $bon->numero }} — ALHAMRA BETON</title>
<style>
body { font-family: 'Times New Roman', serif; margin: 40px; color: #000; }
.entete { display: flex; justify-content: space-between; border-bottom: 3px solid #c0483c; padding-bottom: 10px; }
.entete h1 { margin: 0; color: #c0483c; }
.entete .beton { color: #555; letter-spacing: 3px; }
.titre { text-align: center; margin: 25px 0; font-size: 20px; font-weight: bold; text-decoration: underline; }
table { width: 100%; border-collapse: collapse; margin-top: 10px; }
td, th { border: 1px solid #333; padding: 8px; text-align: left; }
th { background: #eee; }
.signatures { display: flex; justify-content: space-between; margin-top: 60px; }
.signatures div { width: 40%; text-align: center; }
.ligne { border-top: 1px solid #000; margin-top: 60px; padding-top: 5px; }
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
    ICE : XXXXXXXXXXXXXXX
  </div>
</div>
<div class="titre">BON DE LIVRAISON N° {{ $bon->numero }}</div>
<table>
  <tr><th style="width:30%">Date d'émission</th><td>{{ $bon->date_emission->format('d/m/Y') }}</td></tr>
  <tr><th>Client</th><td>{{ $bon->rotation->commande->client->nom }} @if($bon->rotation->commande->client->ice) — ICE : {{ $bon->rotation->commande->client->ice }} @endif</td></tr>
  <tr><th>Chantier</th><td>{{ $bon->rotation->commande->adresse_chantier }}</td></tr>
  <tr><th>Commande</th><td>CMD-{{ $bon->rotation->commande->id }} (rotation n° {{ $bon->rotation->num_ordre }})</td></tr>
</table>
<table>
  <tr><th>Produit</th><th>Quantité livrée</th><th>Camion</th><th>Chauffeur</th><th>Heure départ</th><th>Heure arrivée</th></tr>
  <tr>
    <td>{{ $bon->rotation->commande->produit->designation }}</td>
    <td>{{ $bon->quantite_livree }} m³</td>
    <td>{{ $bon->rotation->camion->matricule }}</td>
    <td>{{ $bon->rotation->chauffeur->nomComplet() }}</td>
    <td>{{ $bon->rotation->heure_depart?->format('H:i') ?: '—' }}</td>
    <td>{{ $bon->rotation->heure_arrivee?->format('H:i') ?: '—' }}</td>
  </tr>
</table>
<div class="signatures">
  <div><strong>Le livreur</strong><div class="ligne">Signature</div></div>
  <div><strong>Le client</strong><div class="ligne">Signature et cachet</div></div>
</div>
</body>
</html>
