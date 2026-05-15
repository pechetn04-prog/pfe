<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Facture #{{ $facture->numero }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; color: #333; margin: 0; padding: 0; }
        .invoice-box { padding: 30px; }
        .header { border-bottom: 2px solid #2563eb; padding-bottom: 15px; margin-bottom: 20px; }
        .company-info { float: left; width: 50%; }
        .invoice-info { float: right; width: 50%; text-align: right; }
        .company-name { font-size: 24px; font-weight: bold; color: #2563eb; margin-bottom: 5px; }
        .invoice-title { font-size: 20px; font-weight: bold; color: #333; }
        .client-info { margin-top: 30px; border: 1px solid #eee; padding: 15px; background-color: #fafafa; }
        .table { width: 100%; border-collapse: collapse; margin-top: 30px; }
        .table th { background-color: #2563eb; color: white; padding: 10px; text-align: left; font-size: 11px; text-transform: uppercase; }
        .table td { padding: 10px; border-bottom: 1px solid #eee; }
        .totals { float: right; width: 300px; margin-top: 30px; }
        .total-row { padding: 8px 0; border-bottom: 1px solid #eee; display: flex; justify-content: space-between; }
        .grand-total { font-size: 16px; font-weight: bold; color: #2563eb; background-color: #eff6ff; padding: 10px; border-radius: 5px; margin-top: 10px; }
        .footer { position: fixed; bottom: 30px; left: 30px; right: 30px; text-align: center; border-top: 1px solid #eee; padding-top: 10px; font-size: 10px; color: #777; }
        .clear { clear: both; }
    </style>
</head>
<body>
    <div class="invoice-box">
        <div class="header">
            <div class="company-info">
                <div class="company-name">{{ $company->nom_societe ?? 'Maison Tel' }}</div>
                <div>{{ $company->adresse ?? 'Adresse non configurée' }}</div>
                <div>Tél : {{ $company->telephone ?? '—' }} | Email : {{ $company->email ?? '—' }}</div>
            </div>
            <div class="invoice-info">
                <div class="invoice-title">FACTURE</div>
                <div style="font-size: 18px; font-weight: bold; margin: 5px 0;">#{{ $facture->numero }}</div>
                <div>Date : {{ \Carbon\Carbon::parse($facture->date_facture)->format('d/m/Y') }}</div>
                <div>Dossier : #{{ $facture->dossier->num_dossier }}</div>
            </div>
            <div class="clear"></div>
        </div>

        <div class="client-info">
            <div style="font-weight: bold; color: #2563eb; margin-bottom: 5px;">CLIENT :</div>
            <div style="font-size: 14px; font-weight: bold;">{{ $facture->dossier->client->name ?? '—' }}</div>
            <div>{{ $facture->dossier->client->email ?? '—' }}</div>
            <div>Tél : {{ $facture->dossier->client->telephone ?? '—' }}</div>
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
                    @php
                        $qty = $piece->pivot->quantite;
                        $ttc = $piece->pivot->prix_unitaire;
                        $ht = $ttc / 1.19;
                        $tva = $ttc - $ht;
                        $totalLigne = $qty * $ttc;
                    @endphp
                    <tr>
                        <td><strong>[Pièce]</strong> {{ $piece->nom }}</td>
                        <td style="text-align: center;">{{ $qty }}</td>
                        <td style="text-align: right;">{{ number_format($ht, 3, ',', ' ') }}</td>
                        <td style="text-align: right;">{{ number_format($tva, 3, ',', ' ') }}</td>
                        <td style="text-align: right; font-weight: bold;">{{ number_format($totalLigne, 3, ',', ' ') }}</td>
                    </tr>
                @endforeach

                {{-- Main d'oeuvre --}}
                @foreach($facture->tarifsMo as $mo)
                    @php
                        $ttc = $mo->pivot->montant;
                        $ht = $ttc / 1.19;
                        $tva = $ttc - $ht;
                    @endphp
                    <tr>
                        <td><strong>[M.O]</strong> {{ $mo->type_intervention }}</td>
                        <td style="text-align: center;">1</td>
                        <td style="text-align: right;">{{ number_format($ht, 3, ',', ' ') }}</td>
                        <td style="text-align: right;">{{ number_format($tva, 3, ',', ' ') }}</td>
                        <td style="text-align: right; font-weight: bold;">{{ number_format($ttc, 3, ',', ' ') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        @php
            $ttcTotal = $facture->montant_total / (1 - $facture->remise/100);
            $htTotal = $ttcTotal / 1.19;
            $tvaTotal = $ttcTotal - $htTotal;
            $montantRemise = $ttcTotal * ($facture->remise / 100);
        @endphp

        <div class="totals">
            <div class="total-row">
                <span style="float: left;">Total Hors Taxe :</span>
                <span style="float: right;">{{ number_format($htTotal, 3, ',', ' ') }} DA</span>
                <div class="clear"></div>
            </div>
            <div class="total-row">
                <span style="float: left;">TVA (19%) :</span>
                <span style="float: right;">{{ number_format($tvaTotal, 3, ',', ' ') }} DA</span>
                <div class="clear"></div>
            </div>
            <div class="total-row">
                <span style="float: left; font-weight: bold;">Total Brut TTC :</span>
                <span style="float: right; font-weight: bold;">{{ number_format($ttcTotal, 3, ',', ' ') }} DA</span>
                <div class="clear"></div>
            </div>
            @if($facture->remise > 0)
            <div class="total-row" style="color: #dc2626;">
                <span style="float: left;">Remise ({{ $facture->remise }}%) :</span>
                <span style="float: right;">- {{ number_format($montantRemise, 3, ',', ' ') }} DA</span>
                <div class="clear"></div>
            </div>
            @endif
            <div class="grand-total">
                <span style="float: left;">NET À PAYER TTC :</span>
                <span style="float: right;">{{ number_format($facture->montant_total, 3, ',', ' ') }} DA</span>
                <div class="clear"></div>
            </div>
        </div>
        <div class="clear"></div>

        <div style="margin-top: 50px;">
            <p style="font-size: 11px; color: #555;">Arrêté la présente facture à la somme de : <br>
            <strong>{{ \Illuminate\Support\Str::upper((new NumberFormatter("fr", NumberFormatter::SPELLOUT))->format($facture->montant_total)) }} DINARS ALGERIENS</strong></p>
        </div>

        <div class="footer">
            {{ $company->nom_societe ?? 'Maison Tel' }} — {{ $company->numero_fiscal ?? 'RC' }} — 
            {{ $company->adresse ?? 'Algérie' }} <br>
            Merci de votre confiance !
        </div>
    </div>
</body>
</html>
