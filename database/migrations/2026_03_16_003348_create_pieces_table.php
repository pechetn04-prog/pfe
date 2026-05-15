<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pieces', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('reference')->nullable();
            $table->string('categorie')->nullable();
            $table->integer('quantite')->default(0);
            $table->decimal('prix_unitaire', 10, 2)->default(0);
            $table->integer('seuil_alerte')->default(5);
            $table->boolean('actif')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pieces');
    }
};
