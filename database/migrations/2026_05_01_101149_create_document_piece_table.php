<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ligne_pieces', function (Blueprint $table) {
            $table->id();
            $table->morphs('source'); // source_type and source_id
            $table->foreignId('piece_id')->constrained('pieces')->onDelete('cascade');
            $table->integer('quantite');
            $table->decimal('prix_unitaire', 10, 3);
            $table->timestamps();
        });

        // Migrate existing data
        if (Schema::hasTable('devis_piece')) {
            DB::table('devis_piece')->get()->each(function ($row) {
                DB::table('ligne_pieces')->insert([
                    'source_type' => 'App\Models\Devis',
                    'source_id' => $row->devis_id,
                    'piece_id' => $row->piece_id,
                    'quantite' => $row->quantite,
                    'prix_unitaire' => $row->prix_unitaire,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            });
            Schema::dropIfExists('devis_piece');
        }

        if (Schema::hasTable('intervention_piece')) {
            DB::table('intervention_piece')->get()->each(function ($row) {
                DB::table('ligne_pieces')->insert([
                    'source_type' => 'App\Models\Intervention',
                    'source_id' => $row->intervention_id,
                    'piece_id' => $row->piece_id,
                    'quantite' => $row->quantite,
                    'prix_unitaire' => $row->prix_unitaire,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            });
            Schema::dropIfExists('intervention_piece');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ligne_pieces');
    }
};
