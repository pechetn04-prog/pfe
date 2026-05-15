<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\TechnicienSpecialite;
use Illuminate\Database\Seeder;

class TechnicienSpecialiteSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Récupérer les techniciens existants
        $techniciens = User::where('role', 'Technicien')->get();

        if ($techniciens->isEmpty()) {
            echo "Aucun technicien trouvé. Veuillez d'abord créer des utilisateurs avec le rôle 'Technicien'.\n";
            return;
        }

        // 2. Assigner des spécialités au premier technicien
        $t1 = $techniciens->first();
        TechnicienSpecialite::create([
            'user_id' => $t1->id,
            'specialite' => 'Écran & Affichage',
            'niveau' => 5
        ]);
        TechnicienSpecialite::create([
            'user_id' => $t1->id,
            'specialite' => 'Casse physique',
            'niveau' => 4
        ]);

        // 3. Assigner des spécialités au deuxième technicien (s'il existe)
        if ($techniciens->count() > 1) {
            $t2 = $techniciens->get(1);
            TechnicienSpecialite::create([
                'user_id' => $t2->id,
                'specialite' => 'Batterie & Énergie',
                'niveau' => 5
            ]);
            TechnicienSpecialite::create([
                'user_id' => $t2->id,
                'specialite' => 'Connectique',
                'niveau' => 5
            ]);
            TechnicienSpecialite::create([
                'user_id' => $t2->id,
                'specialite' => 'Logiciel & Système',
                'niveau' => 4
            ]);
        }
        
        echo "Spécialités assignées avec succès aux techniciens.\n";
    }
}
