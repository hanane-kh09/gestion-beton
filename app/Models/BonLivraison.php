<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BonLivraison extends Model
{
    protected $table = 'bons_livraison';

    protected $fillable = ['rotation_id', 'facture_id', 'numero', 'date_emission', 'quantite_livree', 'signe'];

    protected $casts = ['date_emission' => 'date', 'signe' => 'boolean'];

    public function rotation()
    {
        return $this->belongsTo(Rotation::class);
    }

    public function facture()
    {
        return $this->belongsTo(Facture::class);
    }

    public function montant(): float
    {
        return (float) $this->quantite_livree * (float) $this->rotation->commande->prix_unitaire;
    }

    public static function genererNumero(): string
    {
        $annee = now()->year;
        $count = static::whereYear('date_emission', $annee)->count() + 1;

        return sprintf('BL-%d-%04d', $annee, $count);
    }
}
