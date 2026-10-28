<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Camion extends Model
{
    protected $fillable = ['matricule', 'capacite_m3', 'etat'];

    public function chauffeur()
    {
        return $this->hasOne(Chauffeur::class);
    }

    public function rotations()
    {
        return $this->hasMany(Rotation::class);
    }
}
