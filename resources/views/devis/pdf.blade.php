<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Devis #{{ $devis->numero }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .invoice-box {
            padding: 30px;
        }

        .header {
            border-bottom: 2px solid #ff0000;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }

        .company-info {
            float: left;
            width: 55%;
        }

        .invoice-info {
            float: right;
            width: 45%;
            text-align: right;
        }

        .company-name {
            font-size: 22px;
            font-weight: bold;
            color: #ff0000;
            margin-bottom: 5px;
        }

        .invoice-title {
            font-size: 20px;
            font-weight: bold;
            color: #333;
        }

        .client-info {
            margin-top: 20px;
            border: 1px solid #eee;
            padding: 15px;
            background-color: #fafafa;
            border-radius: 8px;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 25px;
        }

        .table th {
            background-color: #000000;
            color: white;
            padding: 10px;
            text-align: left;
            font-size: 10px;
            text-transform: uppercase;
        }

        .table td {
            padding: 10px;
            border-bottom: 1px solid #eee;
        }

        .totals {
            float: right;
            width: 280px;
            margin-top: 25px;
        }

        .total-row {
            padding: 6px 0;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
        }

        .grand-total {
            font-size: 15px;
            font-weight: bold;
            color: #ff0000;
            background-color: #eff6ff;
            padding: 10px;
            border-radius: 5px;
            margin-top: 8px;
        }

        .footer {
            position: fixed;
            bottom: 30px;
            left: 30px;
            right: 30px;
            text-align: center;
            border-top: 1px solid #eee;
            padding-top: 10px;
            font-size: 9px;
            color: #777;
        }

        .clear {
            clear: both;
        }
    </style>
</head>

<body>
    <div class="invoice-box">
        <table style="width: 100%; border-bottom: 2px solid #fc0000; padding-bottom: 15px; margin-bottom: 20px;">
            <tr>
                <td style="width: 55%; vertical-align: top;">
                    @if($company && $company->logo)
                        <img src="{{ public_path('storage/' . $company->logo) }}" alt="Logo" style="max-height: 80px; margin-bottom: 8px;"><br>
                    @else
                        <div class="company-name" style="font-size: 22px; font-weight: bold; 
                        color: #ff0000; margin-bottom: 5px;">{{ optional($company)->nom_societe ?? 'MAISON TEL' }}</div>
                    @endif
                    <div style="color: #555; font-size: 11px; line-height: 1.4;">
                        {{ optional($company)->adresse ?? 'Adresse non configurée' }}<br>
                        Tél : {{ optional($company)->telephone ?? '—' }} @if($company && $company->email) | Email : {{ $company->email }} @endif
                        @if($company && $company->numero_fiscal)
                            <br>Matricule Fiscal : {{ $company->numero_fiscal }}
                        @endif
                    </div>
                </td>
                <td style="width: 45%; text-align: right; vertical-align: top;">
                    <div class="invoice-title" style="font-size: 22px; font-weight: bold; 
                    color: #ff0000; margin-bottom: 5px;">DEVIS DE RÉPARATION</div>
                    <div style="font-size: 16px; font-weight: bold; color: #333; margin-bottom: 5px;">#{{ $devis->numero }}</div>
                    <div style="color: #555; font-size: 11px; line-height: 1.4;">
                        Date : {{ \Carbon\Carbon::parse($devis->created_at)->format('d/m/Y') }}<br>
                        Validité : 30 jours<br>
                        Dossier : #{{ $devis->dossier->num_dossier }}
                    </div>
                </td>
            </tr>
        </table>

        <div class="client-info">
            <div style="float: left; width: 50%;">
                <div style="font-weight: bold; color: #ff0000; margin-bottom: 5px; font-size: 10px; text-transform: uppercase;">CLIENT :</div>
                <div style="font-size: 13px; font-weight: bold; color: #1a2332;">{{ $devis->dossier->client->name ?? '—' }}</div>
                @if($devis->dossier->client->email)
                    <div style="margin-top: 3px;">Email : {{ $devis->dossier->client->email }}</div>
                @endif
                <div style="margin-top: 3px;">Tél : {{ $devis->dossier->client->telephone ?? '—' }}</div>
            </div>
            <div style="float: right; width: 50%; text-align: right;">
                <div style="font-weight: bold; color: #ff0000; margin-bottom: 5px; font-size: 10px; text-transform: uppercase;">APPAREIL & IDENTIFICATION :</div>
                <div style="font-size: 13px; font-weight: bold; color: #1a2332;">{{ $devis->dossier->appareil->modele ?? '—' }}</div>
                <div style="margin-top: 3px;">IMEI : <span style="font-family: monospace;">{{ $devis->dossier->imei ?? '—' }}</span></div>
                @if($devis->dossier->garantie_annulee)
                    <div style="color: #f59e0b; font-weight: bold; margin-top: 5px; font-size: 10px;">[GARANTIE EXCLUE]</div>
                @elseif($devis->dossier->sous_garantie)
                    <div style="color: #16a34a; font-weight: bold; margin-top: 5px; font-size: 10px;">[SOUS GARANTIE]</div>
                @else
                    <div style="color: #dc2626; font-weight: bold; margin-top: 5px; font-size: 10px;">[HORS GARANTIE]</div>
                @endif
            </div>
            <div class="clear"></div>
        </div>

        <table class="table">
            <thead>
                <tr>
                    <th>Désignation</th>
                    <th style="text-align: center; width: 50px;">Qté</th>
                    <th style="text-align: right; width: 100px;">Prix U. HT</th>
                    <th style="text-align: right; width: 80px;">TVA (19%)</th>
                    <th style="text-align: right; width: 100px;">Total TTC</th>
                </tr>
            </thead>
                @foreach($lignes as $ligne)
                    <tr>
                        <td>{{ $ligne['designation'] }}</td>
                        <td style="text-align: center;">{{ $ligne['quantite'] }}</td>
                        <td style="text-align: right;">{{ number_format($ligne['ht'], 3, ',', ' ') }}</td>
                        <td style="text-align: right;">{{ number_format($ligne['tva'], 3, ',', ' ') }}</td>
                        <td style="text-align: right; font-weight: bold;">{{ number_format($ligne['totalLigne'], 3, ',', ' ') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>



        <table style="float: right; width: 280px; margin-top: 25px; border-collapse: collapse;">
            <tr>
                <td style="padding: 6px 0; border-bottom: 1px solid #eee; text-align: left; font-size: 11px;">Total Hors Taxe :</td>
                <td style="padding: 6px 0; border-bottom: 1px solid #eee; text-align: right; font-size: 11px;">{{ number_format($htTotal, 3, ',', ' ') }} {{ optional($company)->devise ?? 'DT' }}</td>
            </tr>
            <tr>
                <td style="padding: 6px 0; border-bottom: 1px solid #eee; text-align: left; font-size: 11px;">TVA (19%) :</td>
                <td style="padding: 6px 0; border-bottom: 1px solid #eee; text-align: right; font-size: 11px;">{{ number_format($tvaTotal, 3, ',', ' ') }} {{ optional($company)->devise ?? 'DT' }}</td>
            </tr>
            <tr>
                <td colspan="2" style="padding-top: 8px;">
                    <table style="width: 100%; background-color: #eff6ff; border-radius: 5px; padding: 10px; border-collapse: collapse;">
                        <tr>
                            <td style="font-size: 15px; font-weight: bold; color: #ff0000; text-align: left; padding: 0;">TOTAL TTC :</td>
                            <td style="font-size: 15px; font-weight: bold; color: #ff0000; text-align: right; padding: 0;">{{ number_format($devis->montant_total, 3, ',', ' ') }} {{ optional($company)->devise ?? 'DT' }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        <div class="clear"></div>

        <div style="margin-top: 50px; border-top: 1px dashed #ddd; padding-top: 15px;">
            <p style="font-size: 10px; color: #555; line-height: 1.5; font-style: italic; margin: 0;">
                * <strong>Note importante :</strong> Ce devis est établi sur la base d'une expertise technique initiale. Les tarifs indiqués (pièces et main-d'œuvre) sont susceptibles d'être ajustés ou modifiés en cas de constatation d'autres pannes ou anomalies cachées lors du démontage ou de la phase active de réparation. Si un ajustement de prix s'avère nécessaire, le client en sera informé pour approbation avant toute intervention complémentaire.
            </p>
        </div>

        <div class="footer">
            {{ optional($company)->nom_societe ?? 'MAISON TEL' }} — {{ optional($company)->numero_fiscal ?? '' }} —
            {{ optional($company)->adresse ?? '' }} <br>
        </div>
    </div>
</body>

</html>