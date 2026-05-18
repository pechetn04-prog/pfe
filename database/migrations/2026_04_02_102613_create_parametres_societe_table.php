<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('parametres_societe', function (Blueprint $table) {
            $table->id();
            $table->string('nom_societe')->nullable();
            $table->string('adresse')->nullable();
            $table->string('telephone')->nullable();
            $table->string('email')->nullable();
            $table->string('ville')->nullable();
            $table->string('pays')->nullable();
            $table->string('site_web')->nullable();
            $table->string('logo')->nullable();
            $table->string('numero_fiscal')->nullable();
            $table->string('devise')->default('TND');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('parametres_societe');
    }
};