<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Rapport de Diagnostic — #{{ $dossier->num_dossier }}</title>
    <style>
        /* Styles de base du document PDF */
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            color: #333;
            margin: 0;
            padding: 0;
        }

        /* Conteneur principal */
        .invoice-box {
            padding: 30px;
        }

        /* En-tête avec bordure Smartec */
        .header {
            border-bottom: 2px solid #dc2626;
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
            color: #dc2626;
            margin-bottom: 5px;
        }

        .invoice-title {
            font-size: 18px;
            font-weight: bold;
            color: #333;
        }

        /* Titres de sections stylisés */
        .section-title {
            background: #fef2f2;
            border-bottom: 1px solid #fecaca;
            padding: 6px 12px;
            font-weight: bold;
            font-size: 10px;
            text-transform: uppercase;
            color: #991b1b;
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
            background-color: #f1f5f9;
            color: #475569;
            padding: 8px;
            text-align: left;
            font-size: 9px;
            text-transform: uppercase;
        }

        .table td {
            padding: 8px;
            border-bottom: 1px solid #eee;
        }

        .decision-box {
            margin-top: 20px;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
            font-size: 14px;
            font-weight: bold;
        }

        /* Badges de décision finale */
        .reparable {
            background-color: #dcfce7;
            color: #166534;
            border: 1px solid #86efac;
        }

        .irreparable {
            background-color: #fee2e2;
            color: #991b1b;
            border: 1px solid #fca5a5;
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
        <!-- Section : En-tête (Logo et Infos Société) -->
        <div class="header">
            <div class="company-info">
                @if($company && $company->logo)
                    <img src="{{ public_path('storage/' . $company->logo) }}" alt="Logo"
                        style="max-height: 120px; margin-bottom: 8px;"><br>
                @else
                    <div class="company-name">{{ $company->nom_societe ?? 'MAISON TEL' }}</div>
                @endif
                <div>{{ $company->adresse ?? '' }}</div>
                <div>Tél : {{ $company->telephone ?? '' }}</div>
            </div>
            <div class="invoice-info">
                <div class="invoice-title">RAPPORT DE DIAGNOSTIC</div>
                <div style="font-size: 18px; font-weight: bold; margin: 5px 0; color: #dc2626;">
                    #{{ $dossier->num_dossier }}</div>
                <div>Date : {{ now()->format('d/m/Y') }}</div>
            </div>
            <div class="clear"></div>
        </div>

        <!-- Section : Client et Appareil -->
        <div class="section-title">Informations Dossier & Client</div>
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
                <td class="label">Garantie</td>
                <td class="val">{{ $dossier->sous_garantie ? 'SOUS GARANTIE' : 'HORS GARANTIE' }}</td>
                <td class="label">Panne déclarée</td>
                <td class="val">{{ $dossier->panne_declaree }}</td>
            </tr>
        </table>

        <!-- Section : Analyse Technique (si existante) -->
        @if($dossier->diagnostic)
            @php $diag = $dossier->diagnostic; @endphp
            <div class="section-title">Constat Technique</div>
            <div class="box">{{ $diag->constat ?? 'Aucun constat renseigné.' }}</div>

            <div class="section-title">Recommandation du Technicien</div>
            <div class="box">{{ $diag->recommandation ?? 'Aucune recommandation.' }}</div>

            @if($diag->pieces && $diag->pieces->count())
                <div class="section-title">Pièces & Composants à prévoir</div>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Référence</th>
                            <th>Désignation</th>
                            <th style="text-align: center;">Qté</th>
                            <th style="text-align: right;">Prix Unit. TTC</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($diag->pieces as $p)
                            <tr>
                                <td>{{ $p->reference ?? '—' }}</td>
                                <td>{{ $p->nom }}</td>
                                <td style="text-align: center;">{{ $p->pivot->quantite ?? 1 }}</td>
                                <td style="text-align: right;">{{ number_format($p->prix_vente ?? 0, 3, ',', ' ') }}
                                    {{ $company->devise ?? 'DT' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif

            <!-- Résultat Final de l'expertise -->
            <div class="decision-box {{ ($diag->is_reparable ?? true) ? 'reparable' : 'irreparable' }}">
                DÉCISION : {{ ($diag->is_reparable ?? true) ? 'APPAREIL RÉPARABLE' : 'APPAREIL IRRÉPARABLE' }}
            </div>
        @else
            <div style="text-align: center; padding: 40px; color: #94a3b8;">
                Aucun diagnostic n'a encore été saisi pour ce dossier.
            </div>
        @endif

        <div class="signature">
            <div class="signature-box">Signature Technicien</div>
            <div class="signature-box" style="float: right;">Cachet & Signature SAV</div>
            <div class="clear"></div>
        </div>

        <div class="footer">
            {{ $company->nom_societe ?? 'MAISON TEL' }} — Rapport généré le {{ now()->format('d/m/Y H:i') }}
        </div>
    </div>
</body>

</html>