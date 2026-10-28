<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mouvements_stock', function (Blueprint $table) {
            $table->id();
            $table->foreignId('matiere_premiere_id')->constrained('matieres_premieres')->cascadeOnDelete();
            $table->enum('type', ['entree', 'sortie']);
            $table->decimal('quantite', 12, 2);
            $table->date('date_mouvement');
            $table->foreignId('rotation_id')->nullable()->constrained('rotations')->nullOnDelete();
            $table->string('commentaire')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mouvements_stock');
    }
};
