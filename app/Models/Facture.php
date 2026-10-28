<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Facture extends Model
{
    protected $fillable = [
        'client_id', 'numero', 'date_emission', 'date_echeance',
        'montant_ht', 'tva', 'montant_ttc', 'statut',
    ];

    protected $casts = ['date_emission' => 'date', 'date_echeance' => 'date'];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function bonsLivraison()
    {
        return $this->hasMany(BonLivraison::class);
    }

    public function paiements()
    {
        return $this->hasMany(Paiement::class);
    }

    public function totalPaye(): float
    {
        return (float) $this->paiements()->sum('montant');
    }

    public function resteAPayer(): float
    {
        return (float) $this->montant_ttc - $this->totalPaye();
    }

    public function actualiserStatut(): void
    {
        $paye = $this->totalPaye();
        $this->statut = $paye >= (float) $this->montant_ttc ? 'payee' : ($paye > 0 ? 'partielle' : 'impayee');
        $this->save();
    }

    public static function genererNumero(): string
    {
        $annee = now()->year;
        $count = static::whereYear('date_emission', $annee)->count() + 1;

        return sprintf('FA-%d-%04d', $annee, $count);
    }
}
