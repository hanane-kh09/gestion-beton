<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Paiement extends Model
{
    protected $fillable = [
        'client_id', 'facture_id', 'date_paiement', 'montant', 'mode', 'reference',
        'date_echeance', 'statut_encaissement',
    ];

    protected $casts = ['date_paiement' => 'date', 'date_echeance' => 'date'];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function facture()
    {
        return $this->belongsTo(Facture::class);
    }

    public function modeLabel(): string
    {
        return [
            'especes' => 'Espèces',
            'cheque' => 'Chèque',
            'virement' => 'Virement',
            'effet' => 'Effet (traite)',
        ][$this->mode] ?? ucfirst($this->mode);
    }

    public function encaissementDepasse(): bool
    {
        return $this->statut_encaissement === 'en_attente' && $this->date_echeance && $this->date_echeance->isPast();
    }
}
