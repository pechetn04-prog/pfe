<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('technicien_specialites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('specialite', [
                'Écran & Affichage',
                'Batterie & Alimentation',
                'Connectique & Ports',
                'Caméra',
                'Audio',
                'Connectivité',
                'Logiciel & Système',
                'Dommages Physiques',
                'Sécurité & Accès',
            ]);
            $table->integer('niveau')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('technicien_specialites');
    }
};
