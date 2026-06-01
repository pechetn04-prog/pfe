<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Exécute les migrations pour initialiser la base de données.
     */
    public function up(): void
    {
        // Table des utilisateurs : Gère l'ensemble des comptes de l'application (Admin, Agent SAV, Technicien, Client)
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->enum('role', ['Admin', 'Agent', 'Technicien', 'Client'])->default('Agent');
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->boolean('actif')->default(true);
            $table->string('telephone')->nullable();
            $table->string('specialite')->nullable();

            $table->rememberToken();
            $table->timestamps();
        });

        // Table des jetons : Stocke temporairement les tokens de réinitialisation des mots de passe oubliés
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        // Table des sessions : Gère le stockage et l'état des sessions actives des utilisateurs connectés
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Annule les migrations en supprimant toutes les tables associées.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
