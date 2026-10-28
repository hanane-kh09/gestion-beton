<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('paiements', function (Blueprint $table) {
            $table->date('date_echeance')->nullable()->after('reference');
            $table->enum('statut_encaissement', ['encaisse', 'en_attente'])->nullable()->after('date_echeance');
        });
    }

    public function down(): void
    {
        Schema::table('paiements', function (Blueprint $table) {
            $table->dropColumn(['date_echeance', 'statut_encaissement']);
        });
    }
};
