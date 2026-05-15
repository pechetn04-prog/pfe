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
        Schema::create('ligne_mod', function (Blueprint $table) {
            $table->id();
            $table->morphs('source'); // source_type and source_id
            $table->foreignId('tarif_mo_id')->constrained('tarif_mos')->onDelete('cascade');
            $table->decimal('montant', 10, 3);
            $table->timestamps();
        });

        // Migrate existing data
        if (Schema::hasTable('devis_tarif_mo')) {
            DB::table('devis_tarif_mo')->get()->each(function ($row) {
                DB::table('ligne_mod')->insert([
                    'source_type' => 'App\Models\Devis',
                    'source_id' => $row->devis_id,
                    'tarif_mo_id' => $row->tarif_mo_id,
                    'montant' => $row->montant,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            });
            Schema::dropIfExists('devis_tarif_mo');
        }

        if (Schema::hasTable('intervention_tarif_mo')) {
            DB::table('intervention_tarif_mo')->get()->each(function ($row) {
                DB::table('ligne_mod')->insert([
                    'source_type' => 'App\Models\Intervention',
                    'source_id' => $row->intervention_id,
                    'tarif_mo_id' => $row->tarif_mo_id,
                    'montant' => $row->montant,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            });
            Schema::dropIfExists('intervention_tarif_mo');
        }

        if (Schema::hasTable('facture_tarif_mo')) {
            DB::table('facture_tarif_mo')->get()->each(function ($row) {
                DB::table('ligne_mod')->insert([
                    'source_type' => 'App\Models\Facture',
                    'source_id' => $row->facture_id,
                    'tarif_mo_id' => $row->tarif_mo_id,
                    'montant' => $row->montant,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            });
            Schema::dropIfExists('facture_tarif_mo');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ligne_mod');
    }
};
