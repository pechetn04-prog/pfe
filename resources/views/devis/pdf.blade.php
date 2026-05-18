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
            border-bottom: 2px solid #2563eb;
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
            color: #2563eb;
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
            background-color: #2563eb;
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
            color: #2563eb;
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
        <div class="header">
            <div class="company-info">
                @if($company && $company->logo)
                    <img src="{{ public_path('storage/' . $company->logo) }}" alt="Logo"
                        style="max-height: 120px; margin-bottom: 8px;"><br>
                @else
                    <div class="company-name">{{ optional($company)->nom_societe ?? 'MAISON TEL' }}</div>
                @endif
                <div>{{ optional($company)->adresse ?? 'Adresse non configurée' }}</div>
                <div>Tél : {{ optional($company)->telephone ?? '—' }} @if($company && $company->email) | Email : {{ $company->email }} @endif
                </div>
                @if($company && $company->numero_fiscal)
                    <div style="margin-top: 3px; font-weight: bold;">MF/RC : {{ $company->numero_fiscal }}</div>
                @endif
            </div>
            <div class="invoice-info">
                <div class="invoice-title">DEVIS DE RÉPARATION</div>
                <div style="font-size: 18px; font-weight: bold; margin: 5px 0; color: #2563eb;">#{{ $devis->numero }}
                </div>
                <div>Date : {{ \Carbon\Carbon::parse($devis->created_at)->format('d/m/Y') }}</div>
                <div>Validité : 30 jours</div>
                <div>Dossier : #{{ $devis->dossier->num_dossier }}</div>
            </div>
            <div class="clear"></div>
        </div>

        <div class="client-info">
            <div
                style="font-weight: bold; color: #2563eb; margin-bottom: 5px; font-size: 10px; text-transform: uppercase;">
                CLIENT :</div>
            <div style="font-size: 13px; font-weight: bold; color: #1a2332;">{{ $devis->dossier->client->name ?? '—' }}
            </div>
            <div style="margin-top: 3px;">Tél : {{ $devis->dossier->client->telephone ?? '—' }}</div>
            <div>Appareil : <strong>{{ $devis->dossier->appareil->modele ?? '—' }}</strong></div>
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



        <div class="totals">
            <div class="total-row">
                <span style="float: left;">Total Hors Taxe :</span>
                <span style="float: right;">{{ number_format($htTotal, 3, ',', ' ') }}
                    {{ optional($company)->devise ?? 'DT' }}</span>
                <div class="clear"></div>
            </div>
            <div class="total-row">
                <span style="float: left;">TVA (19%) :</span>
                <span style="float: right;">{{ number_format($tvaTotal, 3, ',', ' ') }}
                    {{ optional($company)->devise ?? 'DT' }}</span>
                <div class="clear"></div>
            </div>

            <div class="grand-total">
                <span style="float: left;">TOTAL TTC :</span>
                <span style="float: right;">{{ number_format($devis->montant_total, 3, ',', ' ') }}
                    {{ optional($company)->devise ?? 'DT' }}</span>
                <div class="clear"></div>
            </div>
        </div>
        <div class="clear"></div>

        <div class="footer">
            {{ optional($company)->nom_societe ?? 'MAISON TEL' }} — {{ optional($company)->numero_fiscal ?? '' }} —
            {{ optional($company)->adresse ?? '' }} <br>
        </div>
    </div>
</body>

</html>