<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Produit extends Model
{
    protected $fillable = ['code', 'designation', 'prix_m3', 'actif'];

    protected $casts = ['actif' => 'boolean'];

    public function commandes()
    {
        return $this->hasMany(Commande::class);
    }

    public function formulations()
    {
        return $this->hasMany(Formulation::class);
    }
}
