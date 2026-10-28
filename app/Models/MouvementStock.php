<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MouvementStock extends Model
{
    protected $table = 'mouvements_stock';

    protected $fillable = ['matiere_premiere_id', 'type', 'quantite', 'date_mouvement', 'rotation_id', 'commentaire'];

    protected $casts = ['date_mouvement' => 'date'];

    public function matierePremiere()
    {
        return $this->belongsTo(MatierePremiere::class);
    }

    public function rotation()
    {
        return $this->belongsTo(Rotation::class);
    }
}
