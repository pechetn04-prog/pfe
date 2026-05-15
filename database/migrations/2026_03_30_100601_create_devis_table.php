<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('devis', function (Blueprint $table) {
            $table->id();
            $table->string('numero')->unique()->nullable();
            $table->foreignId('ticket_id')->unique()->constrained('tickets')->cascadeOnDelete();
            $table->decimal('montant_total', 12, 2)->default(0);
            $table->decimal('frais_mod', 12, 2)->default(0);
            $table->decimal('remise', 12, 2)->default(0);
            $table->string('statut')->default('en_attente');
            $table->date('date_creation')->nullable();
            $table->date('date_decision')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('devis');
    }
};