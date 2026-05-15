<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Vente;
use Carbon\Carbon;

class VenteSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            [
                'type' => 'Smartphone',
                'imei' => '354896102345678',
                'modele' => 'Samsung Galaxy S23 Ultra',
                'client_nom' => 'Mohamed Ben Ali',
                'date_vente' => Carbon::now()->subMonths(6),
                'duree_garantie_mois' => 12,
                'reference_produit' => 'SM-S918B',
                'numero_facture_vente' => 'FV-2023-001',
            ],
            [
                'type' => 'Smartphone',
                'imei' => '869405041234567',
                'modele' => 'iPhone 15 Pro Max',
                'client_nom' => 'Sonia Mansour',
                'date_vente' => Carbon::now()->subMonths(14),
                'duree_garantie_mois' => 12, // Hors garantie
                'reference_produit' => 'A3106',
                'numero_facture_vente' => 'FV-2023-045',
            ],
            [
                'type' => 'Tablette',
                'imei' => '357890123456789',
                'modele' => 'iPad Air (M1)',
                'client_nom' => 'Yassine Toumi',
                'date_vente' => Carbon::now()->subDays(15),
                'duree_garantie_mois' => 24,
                'reference_produit' => 'A2588',
                'numero_facture_vente' => 'FV-2024-012',
            ],
            [
                'type' => 'Smartphone',
                'imei' => '352211004455667',
                'modele' => 'Xiaomi Redmi Note 12',
                'client_nom' => 'Amine Ghorbel',
                'date_vente' => Carbon::now()->subMonths(3),
                'duree_garantie_mois' => 12,
                'reference_produit' => '22111317G',
                'numero_facture_vente' => 'FV-2024-005',
            ],
            [
                'type' => 'Smartphone',
                'imei' => '359988776655443',
                'modele' => 'Samsung Galaxy A54',
                'client_nom' => 'Faten Karoui',
                'date_vente' => Carbon::now()->subYears(2),
                'duree_garantie_mois' => 12, // Hors garantie
                'reference_produit' => 'SM-A546B',
                'numero_facture_vente' => 'FV-2022-118',
            ],
        ];

        foreach ($data as $item) {
            Vente::updateOrCreate(['imei' => $item['imei']], $item);
        }
    }
}
