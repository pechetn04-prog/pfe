<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Piece;
use App\Models\TarifMo;

class SmartecDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Remplissage des Pièces Smartec
        $pieces = [
            ['nom' => 'Écran Smartec S50', 'reference' => 'SCR-S50-ORG', 'categorie' => 'Écran', 'quantite' => 15, 'prix_unitaire' => 125.000, 'seuil_alerte' => 3],
            ['nom' => 'Batterie Smartec S50', 'reference' => 'BAT-S50-ORG', 'categorie' => 'Batterie', 'quantite' => 20, 'prix_unitaire' => 45.000, 'seuil_alerte' => 5],
            ['nom' => 'Connecteur Charge Smartec S50', 'reference' => 'CHG-S50', 'categorie' => 'Connecteur de charge', 'quantite' => 30, 'prix_unitaire' => 15.000, 'seuil_alerte' => 5],
            ['nom' => 'Écran Smartec Alpha 7', 'reference' => 'SCR-A7-ORG', 'categorie' => 'Écran', 'quantite' => 10, 'prix_unitaire' => 145.000, 'seuil_alerte' => 2],
            ['nom' => 'Batterie Smartec Alpha 7', 'reference' => 'BAT-A7-ORG', 'categorie' => 'Batterie', 'quantite' => 12, 'prix_unitaire' => 55.000, 'seuil_alerte' => 3],
            ['nom' => 'Caméra Arrière Smartec S50', 'reference' => 'CAM-S50-BCK', 'categorie' => 'Caméra', 'quantite' => 5, 'prix_unitaire' => 35.000, 'seuil_alerte' => 2],
            ['nom' => 'Haut-parleur Smartec S50', 'reference' => 'SPK-S50', 'categorie' => 'Haut-parleur', 'quantite' => 25, 'prix_unitaire' => 12.500, 'seuil_alerte' => 5],
            ['nom' => 'Bouton Power/Volume Smartec S50', 'reference' => 'BTN-S50', 'categorie' => 'Boutons', 'quantite' => 40, 'prix_unitaire' => 8.000, 'seuil_alerte' => 10],
        ];

        foreach ($pieces as $p) {
            Piece::updateOrCreate(['reference' => $p['reference']], $p);
        }

        // 2. Remplissage des Tarifs Main d'Oeuvre (MOD) Smartec
        $tarifs = [
            ['type_intervention' => 'Remplacement Écran Smartec', 'montant' => 35.000, 'actif' => true],
            ['type_intervention' => 'Remplacement Batterie Smartec', 'montant' => 15.000, 'actif' => true],
            ['type_intervention' => 'Réparation Connecteur Charge Smartec', 'montant' => 25.000, 'actif' => true],
            ['type_intervention' => 'Mise à jour Logicielle / Flash Smartec', 'montant' => 20.000, 'actif' => true],
            ['type_intervention' => 'Déblocage Compte Google (FRP) Smartec', 'montant' => 30.000, 'actif' => true],
            ['type_intervention' => 'Soudure / Micro-soudure Smartec', 'montant' => 50.000, 'actif' => true],
            ['type_intervention' => 'Diagnostic Approfondi Smartec', 'montant' => 10.000, 'actif' => true],
        ];

        foreach ($tarifs as $t) {
            TarifMo::updateOrCreate(['type_intervention' => $t['type_intervention']], $t);
        }
    }
}
