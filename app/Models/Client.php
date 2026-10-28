<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $fillable = ['type', 'nom', 'ice', 'telephone', 'email', 'adresse'];

    public function commandes()
    {
        return $this->hasMany(Commande::class);
    }

    public function factures()
    {
        return $this->hasMany(Facture::class);
    }

    public function paiements()
    {
        return $this->hasMany(Paiement::class);
    }

    public function solde(): float
    {
        return (float) $this->factures()->sum('montant_ttc') - (float) $this->paiements()->sum('montant');
    }
}
