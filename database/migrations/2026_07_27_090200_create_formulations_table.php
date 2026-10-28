<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('formulations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('produit_id')->constrained('produits')->cascadeOnDelete();
            $table->foreignId('matiere_premiere_id')->constrained('matieres_premieres')->cascadeOnDelete();
            $table->decimal('quantite_par_m3', 10, 3);
            $table->timestamps();
            $table->unique(['produit_id', 'matiere_premiere_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('formulations');
    }
};
