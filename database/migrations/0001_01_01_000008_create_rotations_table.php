<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rotations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('commande_id')->constrained('commandes')->cascadeOnDelete();
            $table->foreignId('camion_id')->constrained('camions');
            $table->foreignId('chauffeur_id')->constrained('chauffeurs');
            $table->unsignedInteger('num_ordre');
            $table->decimal('quantite_m3', 8, 2);
            $table->dateTime('heure_depart')->nullable();
            $table->dateTime('heure_arrivee')->nullable();
            $table->enum('statut', ['planifiee', 'chargement', 'en_route', 'sur_chantier', 'terminee'])->default('planifiee');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rotations');
    }
};
