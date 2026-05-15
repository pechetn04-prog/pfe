<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ventes', function (Blueprint $table) {
            $table->id();
            $table->string('type')->default('VENTE_DIRECTE');
            $table->string('imei')->unique();
            $table->string('modele')->nullable();
            $table->string('client_nom')->nullable();
            $table->date('date_vente');
            $table->integer('duree_garantie_mois')->default(12);
            $table->string('reference_produit')->nullable();
            $table->string('numero_facture_vente')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ventes');
    }
};
