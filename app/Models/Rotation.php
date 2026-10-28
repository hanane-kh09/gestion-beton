<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rotation extends Model
{
    protected $fillable = [
        'commande_id', 'camion_id', 'chauffeur_id', 'num_ordre',
        'quantite_m3', 'heure_depart', 'heure_arrivee', 'statut',
    ];

    protected $casts = ['heure_depart' => 'datetime', 'heure_arrivee' => 'datetime'];

    public function commande()
    {
        return $this->belongsTo(Commande::class);
    }

    public function camion()
    {
        return $this->belongsTo(Camion::class);
    }

    public function chauffeur()
    {
        return $this->belongsTo(Chauffeur::class);
    }

    public function bonLivraison()
    {
        return $this->hasOne(BonLivraison::class);
    }

    public function dureeMinutes(): ?int
    {
        if (! $this->heure_depart) {
            return null;
        }
        $fin = $this->heure_arrivee ?? now();

        return (int) $this->heure_depart->diffInMinutes($fin);
    }

    public function delaiDepasse(): bool
    {
        $duree = $this->dureeMinutes();

        return $duree !== null && $duree > 90 && in_array($this->statut, ['en_route', 'sur_chantier']);
    }
}
