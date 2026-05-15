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
                        style="max-height: 50px; margin-bottom: 8px;"><br>
                @endif
                <div class="company-name">{{ $company->nom_societe ?? 'MAISON TEL' }}</div>
                <div>{{ $company->adresse ?? 'Adresse non configurée' }}</div>
                <div>Tél : {{ $company->telephone ?? '—' }} @if($company->email) | Email : {{ $company->email }} @endif
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
            <tbody>
                @foreach($devis->pieces as $piece)
                    @php
                        $qty = $piece->pivot->quantite;
                        $ttc = $piece->pivot->prix_unitaire;
                        $ht = $ttc / 1.19;
                        $tva = $ttc - $ht;
                        $totalLigne = $qty * $ttc;
                    @endphp
                    <tr>
                        <td>{{ $piece->nom }}</td>
                        <td style="text-align: center;">{{ $qty }}</td>
                        <td style="text-align: right;">{{ number_format($ht, 3, ',', ' ') }}</td>
                        <td style="text-align: right;">{{ number_format($tva, 3, ',', ' ') }}</td>
                        <td style="text-align: right; font-weight: bold;">{{ number_format($totalLigne, 3, ',', ' ') }}</td>
                    </tr>
                @endforeach

                @foreach($devis->tarifsMo as $mo)
                    @php
                        $ttc = $mo->pivot->montant;
                        $ht = $ttc / 1.19;
                        $tva = $ttc - $ht;
                    @endphp
                    <tr>
                        <td>Main d'œuvre : {{ $mo->type_intervention }}</td>
                        <td style="text-align: center;">1</td>
                        <td style="text-align: right;">{{ number_format($ht, 3, ',', ' ') }}</td>
                        <td style="text-align: right;">{{ number_format($tva, 3, ',', ' ') }}</td>
                        <td style="text-align: right; font-weight: bold;">{{ number_format($ttc, 3, ',', ' ') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        @php
            $ttcTotal = $devis->montant_total / (1 - $devis->remise / 100);
            $htTotal = $ttcTotal / 1.19;
            $tvaTotal = $ttcTotal - $htTotal;
            $montantRemise = $ttcTotal * ($devis->remise / 100);
        @endphp

        <div class="totals">
            <div class="total-row">
                <span style="float: left;">Total Hors Taxe :</span>
                <span style="float: right;">{{ number_format($htTotal, 3, ',', ' ') }}
                    {{ $company->devise ?? 'DT' }}</span>
                <div class="clear"></div>
            </div>
            <div class="total-row">
                <span style="float: left;">TVA (19%) :</span>
                <span style="float: right;">{{ number_format($tvaTotal, 3, ',', ' ') }}
                    {{ $company->devise ?? 'DT' }}</span>
                <div class="clear"></div>
            </div>
            @if($devis->remise > 0)
                <div class="total-row" style="color: #dc2626;">
                    <span style="float: left;">Remise ({{ $devis->remise }}%) :</span>
                    <span style="float: right;">- {{ number_format($montantRemise, 3, ',', ' ') }}
                        {{ $company->devise ?? 'DT' }}</span>
                    <div class="clear"></div>
                </div>
            @endif
            <div class="grand-total">
                <span style="float: left;">TOTAL TTC :</span>
                <span style="float: right;">{{ number_format($devis->montant_total, 3, ',', ' ') }}
                    {{ $company->devise ?? 'DT' }}</span>
                <div class="clear"></div>
            </div>
        </div>
        <div class="clear"></div>

        <div class="footer">
            {{ $company->nom_societe ?? 'MAISON TEL' }} — {{ $company->numero_fiscal ?? '' }} —
            {{ $company->adresse ?? '' }} <br>
        </div>
    </div>
</body>

</html>