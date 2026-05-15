<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class TechnicienSeeder extends Seeder
{
    public function run(): void
    {
        $techs = [
            [
                'name' => 'Firas Ben Hamouda',
                'email' => 'firas@sav.com',
                'password' => Hash::make('tech12345'),
                'role' => 'Technicien',
                'specialite' => 'Écran & Affichage, Dommages Physiques',
            ],
            [
                'name' => 'Walid Trabelsi',
                'email' => 'walid@sav.com',
                'password' => Hash::make('tech12345'),
                'role' => 'Technicien',
                'specialite' => 'Batterie & Alimentation, Connectique & Ports',
            ],
            [
                'name' => 'Amine Mahjoub',
                'email' => 'amine@sav.com',
                'password' => Hash::make('tech12345'),
                'role' => 'Technicien',
                'specialite' => 'Logiciel & Système, Sécurité & Accès',
            ],
            [
                'name' => 'Mourad Gheribi',
                'email' => 'mourad@sav.com',
                'password' => Hash::make('tech12345'),
                'role' => 'Technicien',
                'specialite' => 'Caméra, Audio, Connectivité',
            ],
        ];

        foreach ($techs as $tech) {
            User::updateOrCreate(['email' => $tech['email']], $tech);
        }
    }
}
