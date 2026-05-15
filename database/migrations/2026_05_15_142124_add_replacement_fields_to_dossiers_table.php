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
        Schema::table('dossiers', function (Blueprint $table) {
            if (!Schema::hasColumn('dossiers', 'imei_remplacement')) {
                $table->string('imei_remplacement')->nullable()->after('etat_appareil');
            }
            if (!Schema::hasColumn('dossiers', 'modele_remplacement')) {
                $table->string('modele_remplacement')->nullable()->after('imei_remplacement');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dossiers', function (Blueprint $table) {
            $table->dropColumn(['imei_remplacement', 'modele_remplacement']);
        });
    }
};
