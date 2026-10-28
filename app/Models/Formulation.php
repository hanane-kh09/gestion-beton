<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Formulation extends Model
{
    protected $fillable = ['produit_id', 'matiere_premiere_id', 'quantite_par_m3'];

    public function produit()
    {
        return $this->belongsTo(Produit::class);
    }

    public function matierePremiere()
    {
        return $this->belongsTo(MatierePremiere::class);
    }
}
