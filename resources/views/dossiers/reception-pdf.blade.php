<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Bon de Réception — #{{ $dossier->num_dossier }}</title>
    <style>
        @page { margin: 2cm; }
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 11px; color: #333; line-height: 1.5; margin: 0; padding: 0; }
        .table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .table th, .table td { padding: 8px; text-align: left; vertical-align: top; }
        .header-table { border-bottom: 2px solid #2563eb; margin-bottom: 30px; }
        .logo { font-size: 32px; font-weight: bold; color: #2563eb; }
        .doc-info { text-align: right; }
        .doc-info h1 { font-size: 18px; margin: 0; color: #000; }
        .doc-info .ref { font-size: 20px; font-weight: bold; color: #2563eb; }
        
        .section-title { 
            background-color: #f8fafc; 
            border-bottom: 1px solid #e2e8f0; 
            padding: 5px 10px; 
            font-weight: bold; 
            text-transform: uppercase; 
            color: #1e293b;
            margin-bottom: 10px;
            font-size: 10px;
        }

        .info-box { border: 1px solid #e2e8f0; border-radius: 4px; padding: 0; margin-bottom: 20px; }
        .info-table td { border-bottom: 1px solid #f1f5f9; }
        .info-table td:last-child { border-bottom: none; }
        .label { color: #64748b; font-weight: bold; width: 35%; }
        
        .garantie-badge { 
            padding: 2px 8px; 
            border-radius: 10px; 
            font-size: 9px; 
            font-weight: bold;
            display: inline-block;
        }
        .bg-success { background-color: #dcfce7; color: #166534; }
        .bg-danger { background-color: #fee2e2; color: #991b1b; }
        .bg-warning { background-color: #fef3c7; color: #92400e; }

        .panne-box { 
            background: #fdfaf3; 
            border: 1px solid #fef3c7; 
            padding: 10px; 
            border-radius: 4px; 
            margin-bottom: 20px;
        }

        .signature-table { margin-top: 50px; }
        .signature-table td { width: 50%; height: 80px; border: 1px solid #e2e8f0; vertical-align: top; padding: 10px; color: #94a3b8; }
        
        .terms { font-size: 8px; color: #64748b; margin-top: 30px; border-top: 1px solid #e2e8f0; padding-top: 10px; }
        .footer { position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 8px; color: #94a3b8; }
    </style>
</head>
<body>
    <table class="table header-table">
        <tr>
            <td class="logo">
                @if($company && $company->logo)
                    <img src="{{ public_path('storage/' . $company->logo) }}" alt="Logo" style="max-height: 120px;">
                @else
                    {{ $company->nom_societe ?? 'MAISON TEL' }}<br>
                    <span style="font-size: 10px; font-weight: normal; color: #64748b;">SERVICE APRÈS-VENTE</span>
                @endif
            </td>
            <td class="doc-info">
                <h1>BON DE RÉCEPTION</h1>
                <div class="ref">#{{ $dossier->num_dossier }}</div>
                <div style="margin-top: 5px;">Date : {{ $dossier->date_reception->format('d/m/Y H:i') }}</div>
                @if($company && $company->numero_fiscal)
                    <div style="font-size: 8px; color: #64748b; margin-top: 5px;">MF/RC : {{ $company->numero_fiscal }}</div>
                @endif
            </td>
        </tr>
    </table>

    <table class="table" style="margin-bottom: 0;">
        <tr>
            <td style="width: 50%; padding-left: 0;">
                <div class="section-title">Coordonnées Client</div>
                <table class="table info-table" style="border: 1px solid #e2e8f0;">
                    <tr>
                        <td class="label">Nom</td>
                        <td>{{ $dossier->client->name ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td class="label">Téléphone</td>
                        <td>{{ $dossier->client->telephone ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td class="label">Email</td>
                        <td>{{ $dossier->client->email ?? '—' }}</td>
                    </tr>
                </table>
            </td>
            <td style="width: 50%; padding-right: 0;">
                <div class="section-title">Détails Appareil</div>
                <table class="table info-table" style="border: 1px solid #e2e8f0;">
                    <tr>
                        <td class="label">Modèle</td>
                        <td style="font-weight: bold;">{{ $dossier->appareil->modele ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td class="label">IMEI / S/N</td>
                        <td>{{ $dossier->imei }}</td>
                    </tr>
                    <tr>
                        <td class="label">Garantie</td>
                        <td>
                            @if($dossier->garantie_annulee)
                                <span class="garantie-badge bg-warning">GARANTIE EXCLUE</span>
                            @elseif($dossier->sous_garantie)
                                <span class="garantie-badge bg-success">SOUS GARANTIE</span>
                            @else
                                <span class="garantie-badge bg-danger">HORS GARANTIE</span>
                            @endif
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <div class="section-title">État et Panne Déclarée</div>
    <div class="panne-box">
        <strong>Description de la panne :</strong><br>
        {{ $dossier->panne_declaree }}
        <br><br>
        <strong>État physique :</strong> {{ $dossier->etat_appareil }}<br>
        <strong>Accessoires :</strong> {{ $dossier->accessoires_remis ?? 'Aucun' }}
    </div>

    <table class="table signature-table">
        <tr>
            <td>Signature & Cachet Maison Tel</td>
            <td>Signature du Client (Lu et approuvé)</td>
        </tr>
    </table>

    <div class="terms">
        <strong>Conditions Générales de Réception :</strong><br>
        1. Le client reconnaît l'état de l'appareil tel que décrit ci-dessus. 
        2. Maison Tel n'est pas responsable de la perte de données stockées dans l'appareil. Veuillez effectuer une sauvegarde avant dépôt.
        3. Tout appareil non récupéré après 3 mois sera considéré comme abandonné.
        4. Pour les appareils hors garantie, un devis sera établi. Les frais de diagnostic peuvent être facturés en cas de refus du devis.
    </div>

    <div class="footer">
        {{ $company->nom_societe ?? 'Maison Tel' }} — {{ $company->adresse ?? '' }} — Tél: {{ $company->telephone ?? '' }} — Email: {{ $company->email ?? '' }}
    </div>
</body>
</html>
