<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Rapport d'Intervention — #{{ $dossier->num_dossier }}</title>
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
            border-bottom: 2px solid #10b981;
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
            color: #10b981;
            margin-bottom: 5px;
        }

        .invoice-title {
            font-size: 18px;
            font-weight: bold;
            color: #333;
        }

        .section-title {
            background: #f0fdf4;
            border-bottom: 1px solid #bbf7d0;
            padding: 6px 12px;
            font-weight: bold;
            font-size: 10px;
            text-transform: uppercase;
            color: #166534;
            margin-bottom: 12px;
            margin-top: 20px;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
        }

        .info-table td {
            padding: 8px;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: top;
        }

        .label {
            color: #64748b;
            font-weight: bold;
            width: 35%;
        }

        .val {
            font-weight: bold;
            color: #0f172a;
        }

        .box {
            border: 1px solid #e2e8f0;
            padding: 15px;
            border-radius: 8px;
            background-color: #fcfcfc;
            min-height: 40px;
            line-height: 1.6;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        .table th {
            background-color: #f0fdf4;
            color: #166534;
            padding: 8px;
            text-align: left;
            font-size: 9px;
            text-transform: uppercase;
        }

        .table td {
            padding: 8px;
            border-bottom: 1px solid #eee;
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

        .signature {
            margin-top: 40px;
        }

        .signature-box {
            float: left;
            width: 45%;
            border: 1px solid #eee;
            height: 100px;
            padding: 10px;
            color: #94a3b8;
            font-size: 10px;
        }
    </style>
</head>

<body>
    <div class="invoice-box">
        <table style="width: 100%; border-bottom: 2px solid #10b981; padding-bottom: 15px; margin-bottom: 20px;">
            <tr>
                <td style="width: 55%; vertical-align: top;">
                    @if($company && $company->logo)
                        <img src="{{ public_path('storage/' . $company->logo) }}" alt="Logo"
                            style="max-height: 80px; margin-bottom: 8px;"><br>
                    @else
                        <div class="company-name"
                            style="font-size: 22px; font-weight: bold; color: #10b981; margin-bottom: 5px;">
                            {{ $company->nom_societe ?? 'MAISON TEL' }}</div>
                    @endif
                    <div style="color: #555; font-size: 11px; line-height: 1.4;">
                        {{ $company->adresse ?? '' }}<br>
                        Tél : {{ $company->telephone ?? '' }}
                        @if($company && $company->numero_fiscal)
                            <br>Matricule Fiscal : {{ $company->numero_fiscal }}
                        @endif
                    </div>
                </td>
                <td style="width: 45%; text-align: right; vertical-align: top;">
                    <div class="invoice-title"
                        style="font-size: 22px; font-weight: bold; color: #10b981; margin-bottom: 5px;">RAPPORT
                        D'INTERVENTION</div>
                    <div style="font-size: 16px; font-weight: bold; color: #333; margin-bottom: 5px;">
                        #{{ $dossier->num_dossier }}</div>
                    <div style="color: #555; font-size: 11px; line-height: 1.4;">
                        Date d'édition : {{ now()->format('d/m/Y') }}
                    </div>
                </td>
            </tr>
        </table>

        <div class="section-title">Informations Dossier & Appareil</div>
        <table class="info-table">
            <tr>
                <td class="label">Client</td>
                <td class="val">{{ $dossier->client->name ?? '—' }}</td>
                <td class="label">Technicien</td>
                <td class="val">{{ $dossier->technicien->name ?? '—' }}</td>
            </tr>
            <tr>
                <td class="label">Appareil / Modèle</td>
                <td class="val">{{ $dossier->appareil->modele ?? '—' }}</td>
                <td class="label">IMEI / S/N</td>
                <td class="val">{{ $dossier->imei }}</td>
            </tr>
            <tr>
                <td class="label">Statut Final</td>
                <td class="val">{{ str_replace('_', ' ', $dossier->statut) }}</td>
                <td class="label">Date Clôture</td>
                <td class="val">{{ now()->format('d/m/Y') }}</td>
            </tr>
        </table>

        @if($dossier->intervention)
            @php $int = $dossier->intervention; @endphp
            <div class="section-title">Détails des Travaux Effectués</div>
            <div class="box">{{ $int->compte_rendu ?? 'Aucun compte rendu saisi.' }}</div>

            @if($int->pieces && $int->pieces->count())
                <div class="section-title">Pièces Remplacées</div>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Référence</th>
                            <th>Désignation</th>
                            <th style="text-align: center;">Qté</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($int->pieces as $p)
                            <tr>
                                <td>{{ $p->reference ?? '—' }}</td>
                                <td>{{ $p->nom }}</td>
                                <td style="text-align: center;">{{ $p->pivot->quantite ?? 1 }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif

            @if($int->tarifsMo && $int->tarifsMo->count())
                <div class="section-title">Main d'œuvre appliquée</div>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Désignation Prestation</th>
                            <th style="text-align: right; width: 120px;">Montant</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($int->tarifsMo as $mo)
                            <tr>
                                <td>{{ $mo->type_intervention }}</td>
                                <td style="text-align: right; font-weight: bold;">
                                    {{ number_format($mo->pivot->montant ?? $mo->montant, 3, '.', ' ') }}
                                    {{ $company->devise ?? 'TND' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        @else
            <div style="text-align: center; padding: 40px; color: #94a3b8;">
                Aucune intervention n'a encore été saisie pour ce dossier.
            </div>
        @endif

        <div class="signature">
            <div class="signature-box">Signature Technicien</div>
            <div class="signature-box" style="float: right;">Cachet & Signature SAV</div>
            <div class="clear"></div>
        </div>

        <div class="footer">
            {{ $company->nom_societe ?? 'MAISON TEL' }} — Rapport d'intervention généré le
            {{ now()->format('d/m/Y H:i') }}
        </div>
    </div>
</body>

</html>