<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('diagnostics', function (Blueprint $table) {
            $table->id();

            $table->foreignId('dossier_id')
                ->constrained('dossiers')
                ->cascadeOnDelete();

            $table->foreignId('technicien_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // Contenu technique
            $table->text('constat')->nullable();
            $table->text('recommandation')->nullable();
            $table->string('photo_panne')->nullable();

            // Exclusion de garantie
            $table->string('motif_exclusion')->nullable();
            $table->text('exclusion_commentaire')->nullable();

            $table->timestamps();

            // Un seul diagnostic par dossier
            $table->unique('dossier_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('diagnostics');
    }
};