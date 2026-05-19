<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Facture #{{ $facture->numero }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
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
            width: 50%;
        }

        .invoice-info {
            float: right;
            width: 50%;
            text-align: right;
        }

        .company-name {
            font-size: 24px;
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
            margin-top: 30px;
            border: 1px solid #eee;
            padding: 15px;
            background-color: #fafafa;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
        }

        .table th {
            background-color: #2563eb;
            color: white;
            padding: 10px;
            text-align: left;
            font-size: 11px;
            text-transform: uppercase;
        }

        .table td {
            padding: 10px;
            border-bottom: 1px solid #eee;
        }

        .totals {
            float: right;
            width: 300px;
            margin-top: 30px;
        }

        .total-row {
            padding: 8px 0;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
        }

        .grand-total {
            font-size: 16px;
            font-weight: bold;
            color: #2563eb;
            background-color: #eff6ff;
            padding: 10px;
            border-radius: 5px;
            margin-top: 10px;
        }

        .footer {
            position: fixed;
            bottom: 30px;
            left: 30px;
            right: 30px;
            text-align: center;
            border-top: 1px solid #eee;
            padding-top: 10px;
            font-size: 10px;
            color: #777;
        }

        .clear {
            clear: both;
        }
    </style>
</head>

<body>
    <div class="invoice-box">
        <table style="width: 100%; border-bottom: 2px solid #2563eb; padding-bottom: 15px; margin-bottom: 20px;">
            <tr>
                <td style="width: 55%; vertical-align: top;">
                    @if($company && $company->logo)
                        <img src="{{ public_path('storage/' . $company->logo) }}" alt="Logo" style="max-height: 80px; margin-bottom: 5px;"><br>
                    @else
                        <div class="company-name" style="font-size: 22px; font-weight: bold; color: #2563eb; margin-bottom: 5px;">{{ optional($company)->nom_societe ?? 'Maison Tel' }}</div>
                    @endif
                    <div style="color: #555; font-size: 11px; line-height: 1.4;">
                        {{ optional($company)->adresse ?? 'Adresse non configurée' }}<br>
                        Tél : {{ optional($company)->telephone ?? '—' }} | Email : {{ optional($company)->email ?? '—' }}
                        @if($company && $company->numero_fiscal)
                            <br>Matricule Fiscal : {{ $company->numero_fiscal }}
                        @endif
                    </div>
                </td>
                <td style="width: 45%; text-align: right; vertical-align: top;">
                    <div class="invoice-title" style="font-size: 22px; font-weight: bold; color: #2563eb; margin-bottom: 5px;">FACTURE</div>
                    <div style="font-size: 16px; font-weight: bold; color: #333; margin-bottom: 5px;">#{{ $facture->numero }}</div>
                    <div style="color: #555; font-size: 11px; line-height: 1.4;">
                        Date : {{ \Carbon\Carbon::parse($facture->date_facture)->format('d/m/Y') }}<br>
                        Dossier : #{{ $facture->dossier->num_dossier }}
                    </div>
                </td>
            </tr>
        </table>

        <div class="client-info">
            <div style="float: left; width: 50%;">
                <div style="font-weight: bold; color: #2563eb; margin-bottom: 5px;">CLIENT :</div>
                <div style="font-size: 14px; font-weight: bold;">{{ $facture->dossier->client->name ?? '—' }}</div>
                <div>{{ $facture->dossier->client->email ?? '—' }}</div>
                <div>Tél : {{ $facture->dossier->client->telephone ?? '—' }}</div>
            </div>
            <div style="float: right; width: 50%; text-align: right;">
                <div style="font-weight: bold; color: #2563eb; margin-bottom: 5px;">APPAREIL & IDENTIFICATION :</div>
                <div style="font-size: 14px; font-weight: bold;">{{ $facture->dossier->appareil->modele ?? '—' }}</div>
                <div>IMEI : <span style="font-family: monospace;">{{ $facture->dossier->imei ?? '—' }}</span></div>
                @if($facture->dossier->garantie_annulee)
                    <div style="color: #f59e0b; font-weight: bold; margin-top: 5px; font-size: 10px;">[GARANTIE EXCLUE]</div>
                @elseif($facture->dossier->sous_garantie)
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
                    <th style="text-align: center; width: 60px;">Qté</th>
                    <th style="text-align: right; width: 120px;">Prix Unit. HT</th>
                    <th style="text-align: right; width: 80px;">TVA (19%)</th>
                    <th style="text-align: right; width: 120px;">Total TTC</th>
                </tr>
            </thead>
            <tbody>
                {{-- Pièces --}}
                @foreach($facture->pieces as $piece)
                    <tr>
                        <td><strong>[Pièce]</strong> {{ $piece->nom }}</td>
                        <td style="text-align: center;">{{ $piece->qty ?? $piece->pivot->quantite }}</td>
                        <td style="text-align: right;">
                            {{ number_format($piece->ht_unitaire ?? ($piece->pivot->prix_unitaire / 1.19), 3, ',', ' ') }}
                        </td>
                        <td style="text-align: right;">
                            {{ number_format($piece->tva_unitaire ?? ($piece->pivot->prix_unitaire - ($piece->pivot->prix_unitaire / 1.19)), 3, ',', ' ') }}
                        </td>
                        <td style="text-align: right; font-weight: bold;">
                            {{ number_format($piece->total_ligne ?? ($piece->pivot->quantite * $piece->pivot->prix_unitaire), 3, ',', ' ') }}
                        </td>
                    </tr>
                @endforeach

                {{-- Main d'oeuvre --}}
                @foreach($facture->tarifsMo as $mo)
                    <tr>
                        <td><strong>[M.O]</strong> {{ $mo->type_intervention }}</td>
                        <td style="text-align: center;">1</td>
                        <td style="text-align: right;">
                            {{ number_format($mo->ht ?? ($mo->pivot->montant / 1.19), 3, ',', ' ') }}
                        </td>
                        <td style="text-align: right;">
                            {{ number_format($mo->tva ?? ($mo->pivot->montant - ($mo->pivot->montant / 1.19)), 3, ',', ' ') }}
                        </td>
                        <td style="text-align: right; font-weight: bold;">
                            {{ number_format($mo->ttc ?? $mo->pivot->montant, 3, ',', ' ') }}
                        </td>
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
            <div class="total-row">
                <span style="float: left; font-weight: bold;">Total Brut TTC :</span>
                <span style="float: right; font-weight: bold;">{{ number_format($ttcTotal, 3, ',', ' ') }}
                    {{ optional($company)->devise ?? 'DT' }}</span>
                <div class="clear"></div>
            </div>
            @if($facture->remise > 0)
                <div class="total-row" style="color: #dc2626;">
                    <span style="float: left;">Remise ({{ $facture->remise }}%) :</span>
                    <span style="float: right;">- {{ number_format($montantRemise, 3, ',', ' ') }}
                        {{ optional($company)->devise ?? 'DT' }}</span>
                    <div class="clear"></div>
                </div>
            @endif
            <div class="grand-total">
                <span style="float: left;">NET À PAYER TTC :</span>
                <span style="float: right;">{{ number_format($facture->montant_total, 3, ',', ' ') }}
                    {{ optional($company)->devise ?? 'DT' }}</span>
                <div class="clear"></div>
            </div>
        </div>
        <div class="clear"></div>


        <div class="footer">
            {{ optional($company)->nom_societe ?? '-' }} — {{ optional($company)->numero_fiscal ?? '-' }} —
            {{ optional($company)->adresse ?? '-' }} <br>
            Merci de votre confiance !
        </div>
    </div>
</body>

</html>