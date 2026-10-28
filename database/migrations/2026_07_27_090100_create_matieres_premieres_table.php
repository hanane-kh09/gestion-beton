<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('matieres_premieres', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->enum('unite', ['tonne', 'm3', 'litre', 'kg']);
            $table->decimal('stock_actuel', 12, 2)->default(0);
            $table->decimal('seuil_alerte', 12, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('matieres_premieres');
    }
};
