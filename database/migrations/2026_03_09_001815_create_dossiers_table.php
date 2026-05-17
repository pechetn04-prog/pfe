<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dossiers', function (Blueprint $table) {
            $table->id();
            $table->string('num_dossier')->unique();

            // Relations
            $table->foreignId('appareil_id')->constrained('appareils')->onDelete('cascade');
            $table->foreignId('client_id')->nullable()->constrained('users')->onDelete('set null');

            // Dates
            $table->date('date_reception');
            $table->date('date_vente')->nullable();
            $table->date('fin_garantie')->nullable();
            $table->date('date_diagnostic')->nullable();
            $table->date('date_reparation')->nullable();
            $table->dateTime('date_livraison')->nullable();
            $table->dateTime('date_cloture')->nullable();

            // Garantie
            $table->boolean('sous_garantie')->default(false);
            $table->boolean('garantie_annulee')->default(false);

            // Panne & Accessoires
            $table->text('panne_declaree')->nullable();
            $table->string('accessoires_remis')->nullable();
            $table->string('etat_appareil')->nullable();

            // Affectation
            $table->foreignId('technicien_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('agent_id')->nullable()->constrained('users')->nullOnDelete();

            // Statut
            $table->string('statut')->default('RECU');
            $table->text('commentaire_refus')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dossiers');
    }
};
