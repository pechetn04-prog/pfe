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
        Schema::dropIfExists('technicien_specialites');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('technicien_specialites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('specialite');
            $table->string('niveau')->nullable();
            $table->timestamps();
        });
    }
};
