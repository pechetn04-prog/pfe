<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class VentesTemplateExport implements FromArray, WithHeadings, WithTitle, ShouldAutoSize
{
    /**
     * @return array
     */
    public function array(): array
    {
        // Deux exemples de lignes types dans le modèle pour guider l'utilisateur.
        return [
            [
                'INITIALE',
                '358206110344581',
                'iPhone 15 Pro Max',
                'Jean Dupont',
                '2026-05-18',
                '12',
                'IPH15PM-256-GR',
                'FAC-2026-0498'
            ],
            [
                'REMPLACEMENT',
                '869274051028345',
                'Samsung Galaxy S24 Ultra',
                'Alice Martin',
                '2026-05-15',
                '24',
                'SAMS24U-512-BL',
                'FAC-2026-0512'
            ]
        ];
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'type',
            'imei',
            'modele',
            'client_nom',
            'date_vente',
            'duree_garantie_mois',
            'reference_produit',
            'numero_facture_vente'
        ];
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return 'Modèle Import Ventes';
    }
}
