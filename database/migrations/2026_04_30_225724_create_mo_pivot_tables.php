<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Pivot pour Devis et Main d'œuvre
        Schema::create('devis_tarif_mo', function (Blueprint $table) {
            $table->id();
            $table->foreignId('devis_id')->constrained('devis')->onDelete('cascade');
            $table->foreignId('tarif_mo_id')->constrained('tarif_mos')->onDelete('cascade');
            $table->decimal('montant', 15, 3); // On stocke le montant au moment du devis
            $table->timestamps();
        });

        // Pivot pour Facture et Main d'œuvre
        Schema::create('facture_tarif_mo', function (Blueprint $table) {
            $table->id();
            $table->foreignId('facture_id')->constrained('factures')->onDelete('cascade');
            $table->foreignId('tarif_mo_id')->constrained('tarif_mos')->onDelete('cascade');
            $table->decimal('montant', 15, 3);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('devis_tarif_mo');
        Schema::dropIfExists('facture_tarif_mo');
    }
};
