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
        Schema::table('demandes_rejet', function (Blueprint $table) {
            $table->foreignId('nouveau_technicien_id')->nullable()->constrained('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('demandes_rejet', function (Blueprint $table) {
            $table->dropForeign(['nouveau_technicien_id']);
            $table->dropColumn('nouveau_technicien_id');
        });
    }
};
