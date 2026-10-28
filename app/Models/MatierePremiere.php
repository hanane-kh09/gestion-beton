<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MatierePremiere extends Model
{
    protected $table = 'matieres_premieres';

    protected $fillable = ['nom', 'unite', 'stock_actuel', 'seuil_alerte'];

    public function formulations()
    {
        return $this->hasMany(Formulation::class);
    }

    public function mouvements()
    {
        return $this->hasMany(MouvementStock::class);
    }

    public function enAlerte(): bool
    {
        return (float) $this->stock_actuel <= (float) $this->seuil_alerte;
    }
}
