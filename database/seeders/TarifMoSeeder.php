<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TarifMo;

class TarifMoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tarifs = [
            ['type_intervention' => 'Diagnostic simple / Expertise', 'montant' => 15.000],
            ['type_intervention' => 'Changement Écran (Main d\'œuvre)', 'montant' => 30.000],
            ['type_intervention' => 'Changement Batterie (Main d\'œuvre)', 'montant' => 20.000],
            ['type_intervention' => 'Réparation Connecteur de charge', 'montant' => 25.000],
            ['type_intervention' => 'Flash / Déblocage Logiciel', 'montant' => 40.000],
            ['type_intervention' => 'Désoxydation (Nettoyage liquide)', 'montant' => 45.000],
            ['type_intervention' => 'Changement Micro / Haut-parleur', 'montant' => 20.000],
            ['type_intervention' => 'Soudure carte mère / Micro-soudure', 'montant' => 60.000],
        ];

        foreach ($tarifs as $tarif) {
            TarifMo::updateOrCreate(
                ['type_intervention' => $tarif['type_intervention']],
                ['montant' => $tarif['montant'], 'actif' => true]
            );
        }
    }
}
