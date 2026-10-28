<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('factures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('clients')->cascadeOnDelete();
            $table->string('numero')->unique();
            $table->date('date_emission');
            $table->date('date_echeance')->nullable();
            $table->decimal('montant_ht', 12, 2);
            $table->decimal('tva', 12, 2);
            $table->decimal('montant_ttc', 12, 2);
            $table->enum('statut', ['impayee', 'partielle', 'payee'])->default('impayee');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('factures');
    }
};
