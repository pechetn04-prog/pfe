<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Renommage des tables
        Schema::rename('tickets', 'dossiers');
        Schema::rename('suivi_tickets', 'suivi_dossiers');
        Schema::rename('ticket_messages', 'dossier_messages');
        Schema::rename('ticket_piece_manquante', 'dossier_piece_manquante');

        // Renommage des colonnes dans la table principale
        Schema::table('dossiers', function (Blueprint $table) {
            $table->renameColumn('num_ticket', 'num_dossier');
        });

        // Renommage des clés étrangères dans toutes les tables liées
        $tables = [
            'suivi_dossiers', 'dossier_messages', 'dossier_piece_manquante',
            'diagnostics', 'interventions', 'devis', 'factures', 'avis', 'demandes_rejet'
        ];

        foreach ($tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->renameColumn('ticket_id', 'dossier_id');
            });
        }
    }

    public function down(): void
    {
        foreach ([
            'suivi_dossiers', 'dossier_messages', 'dossier_piece_manquante',
            'diagnostics', 'interventions', 'devis', 'factures', 'avis', 'demandes_rejet'
        ] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->renameColumn('dossier_id', 'ticket_id');
            });
        }

        Schema::table('dossiers', function (Blueprint $table) {
            $table->renameColumn('num_dossier', 'num_ticket');
        });

        Schema::rename('dossier_piece_manquante', 'ticket_piece_manquante');
        Schema::rename('dossier_messages', 'ticket_messages');
        Schema::rename('suivi_dossiers', 'suivi_tickets');
        Schema::rename('dossiers', 'tickets');
    }
};
