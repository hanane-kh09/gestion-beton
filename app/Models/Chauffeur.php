<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Chauffeur extends Model
{
    protected $fillable = ['nom', 'prenom', 'cin', 'num_permis', 'telephone', 'camion_id'];

    public function camion()
    {
        return $this->belongsTo(Camion::class);
    }

    public function rotations()
    {
        return $this->hasMany(Rotation::class);
    }

    public function nomComplet(): string
    {
        return $this->prenom.' '.$this->nom;
    }
}
