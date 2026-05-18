<?php

namespace App\Imports;

use App\Models\Vente;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Carbon\Carbon;

class VentesImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        // Vérification de la présence de l'IMEI
        if (empty($row['imei'])) {
            return null;
        }

        // Gestion et conversion robuste de la date de vente
        $dateRaw = $row['date_vente'] ?? null;
        if (empty($dateRaw)) {
            $dateVente = now()->format('Y-m-d');
        } elseif (is_numeric($dateRaw)) {
            // Gestion native des dates numériques générées par Excel (PhpSpreadsheet Shared Date)
            try {
                $dateVente = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($dateRaw)->format('Y-m-d');
            } catch (\Exception $e) {
                $dateVente = now()->format('Y-m-d');
            }
        } else {
            try {
                $dateVente = Carbon::parse(trim($dateRaw))->format('Y-m-d');
            } catch (\Exception $e) {
                $dateVente = now()->format('Y-m-d');
            }
        }

        // Insertion unifiée (Update or Create) par rapport à l'IMEI unique
        return Vente::updateOrCreate(
            ['imei' => strval(trim($row['imei']))],
            [
                'type'                 => !empty($row['type']) ? trim($row['type']) : 'INITIALE',
                'modele'               => trim($row['modele'] ?? 'Inconnu'),
                'client_nom'           => trim($row['client_nom'] ?? 'Client Inconnu'),
                'date_vente'           => $dateVente,
                'duree_garantie_mois'  => intval($row['duree_garantie_mois'] ?? 12),
                'reference_produit'    => !empty($row['reference_produit']) ? trim($row['reference_produit']) : null,
                'numero_facture_vente' => !empty($row['numero_facture_vente']) ? trim($row['numero_facture_vente']) : null,
            ]
        );
    }
}
