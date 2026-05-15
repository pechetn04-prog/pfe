<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Devis #{{ $devis->num_devis }}</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; font-size: 12px; color: #333; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #2563eb; padding-bottom: 10px; }
        .company-name { font-size: 24px; font-weight: bold; color: #2563eb; }
        .document-title { font-size: 20px; font-weight: bold; margin-top: 10px; }
        
        .section { margin-bottom: 20px; }
        .row { width: 100%; display: table; }
        .col { display: table-cell; width: 50%; vertical-align: top; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { bg-color: #f8f9fa; font-weight: bold; }
        
        .totals { float: right; width: 250px; margin-top: 20px; }
        .total-row { padding: 5px 0; border-bottom: 1px solid #eee; }
        .total-final { font-size: 16px; font-weight: bold; color: #2563eb; border-top: 2px solid #2563eb; margin-top: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <div class="company-name">MAISON TEL</div>
        <div class="document-title">DEVIS DE RÉPARATION</div>
    </div>

    <div class="section">
        <div class="row">
            <div class="col">
                <strong>À l'attention de :</strong><br>
                {{ $devis->dossier->client->name ?? 'Client' }}<br>
                Tél : {{ $devis->dossier->client->telephone ?? '—' }}
            </div>
            <div class="col" style="text-align: right;">
                <strong>Référence :</strong> #{{ $devis->num_devis }}<br>
                <strong>Date :</strong> {{ \Carbon\Carbon::parse($devis->created_at)->format('d/m/Y') }}<br>
                <strong>Appareil :</strong> {{ $devis->dossier->appareil->modele ?? '—' }}
            </div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Désignation</th>
                <th style="text-align: center;">Qté</th>
                <th style="text-align: right;">Prix Unit. HT</th>
                <th style="text-align: right;">Total HT</th>
            </tr>
        </thead>
        <tbody>
            @foreach($devis->dossier->diagnostic->pieces as $piece)
            <tr>
                <td>{{ $piece->nom }}</td>
                <td style="text-align: center;">1</td>
                <td style="text-align: right;">{{ number_format($piece->prix_vente, 2, ',', ' ') }} TND</td>
                <td style="text-align: right;">{{ number_format($piece->prix_vente, 2, ',', ' ') }} TND</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals">
        <div class="total-row">
            <span style="display:inline-block; width: 150px;">Total Hors Taxes:</span>
            <span style="float: right;">{{ number_format($devis->montant_ht, 2, ',', ' ') }} TND</span>
        </div>
        <div class="total-row">
            <span style="display:inline-block; width: 150px;">TVA (19%):</span>
            <span style="float: right;">{{ number_format($devis->tva, 2, ',', ' ') }} TND</span>
        </div>
        <div class="total-final">
            <span style="display:inline-block; width: 150px;">TOTAL TTC:</span>
            <span style="float: right;">{{ number_format($devis->montant_ttc, 2, ',', ' ') }} TND</span>
        </div>
    </div>

    <div style="margin-top: 100px; font-size: 10px; text-align: center; color: #777;">
        Ce devis est valable 30 jours à compter de sa date d'émission.
    </div>
</body>
</html>
