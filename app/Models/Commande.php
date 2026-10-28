<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Commande extends Model
{
    protected $fillable = [
        'client_id', 'produit_id', 'quantite_m3', 'prix_unitaire',
        'montant_total', 'date_livraison', 'adresse_chantier', 'statut', 'notes',
    ];

    protected $casts = ['date_livraison' => 'datetime'];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function produit()
    {
        return $this->belongsTo(Produit::class);
    }

    public function rotations()
    {
        return $this->hasMany(Rotation::class)->orderBy('num_ordre');
    }

    public function quantiteLivree(): float
    {
        return (float) $this->rotations()->where('statut', 'terminee')->sum('quantite_m3');
    }
}
