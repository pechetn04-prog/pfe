<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Rapport d'Intervention — #{{ $dossier->num_dossier }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; color: #1e293b; margin: 0; padding: 20px; }
        .header { display: flex; justify-content: space-between; border-bottom: 3px solid #7c3aed; padding-bottom: 15px; margin-bottom: 25px; }
        .company h1 { font-size: 20px; color: #7c3aed; margin: 0 0 4px 0; }
        .doc-title h2 { font-size: 16px; margin: 0; text-align: right; }
        .doc-title .num { font-size: 22px; font-weight: 900; color: #7c3aed; text-align: right; }
        .section { margin-bottom: 20px; }
        .section-title { background: #4c1d95; color: white; padding: 6px 12px; font-weight: bold; font-size: 11px; text-transform: uppercase; border-radius: 4px; margin-bottom: 12px; }
        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
        .field .label { color: #64748b; font-size: 10px; text-transform: uppercase; margin-bottom: 2px; }
        .field .val { font-weight: bold; border-bottom: 1px solid #e2e8f0; padding-bottom: 3px; min-height: 18px; }
        table { width: 100%; border-collapse: collapse; font-size: 11px; margin-top: 8px; }
        th { background: #f5f3ff; padding: 6px 10px; text-align: left; font-weight: bold; color: #6d28d9; text-transform: uppercase; font-size: 10px; }
        td { padding: 6px 10px; border-bottom: 1px solid #e2e8f0; }
        .total-row { background: #f5f3ff; font-weight: bold; }
        .result-box { padding: 10px 16px; border-radius: 6px; font-weight: bold; font-size: 13px; display: inline-block; }
        .result-repare { background: #dcfce7; color: #166534; border: 1px solid #86efac; }
        .footer { margin-top: 30px; border-top: 1px solid #e2e8f0; padding-top: 10px; font-size: 9px; color: #94a3b8; text-align: center; }
        .signature { display: grid; grid-template-columns: 1fr 1fr; gap: 40px; margin-top: 40px; }
        .sign-box { border-top: 1px solid #94a3b8; padding-top: 8px; color: #64748b; font-size: 10px; }
    </style>
</head>
<body>

<div class="header">
    <div class="company">
        <h1>📱 Maison Tel</h1>
        <div>{{ $company->adresse ?? '' }}</div>
        <div>Tél : {{ $company->telephone ?? '' }}</div>
    </div>
    <div class="doc-title">
        <h2>RAPPORT D'INTERVENTION</h2>
        <div class="num">#{{ $dossier->num_dossier }}</div>
        <div style="text-align:right;">Date : {{ now()->format('d/m/Y') }}</div>
    </div>
</div>

<div class="section">
    <div class="section-title">Informations Dossier</div>
    <div class="grid-2">
        <div class="field"><div class="label">Client</div><div class="val">{{ $dossier->client->name ?? '—' }}</div></div>
        <div class="field"><div class="label">IMEI</div><div class="val">{{ $dossier->imei }}</div></div>
        <div class="field"><div class="label">Technicien</div><div class="val">{{ $dossier->technicien->name ?? '—' }}</div></div>
        <div class="field"><div class="label">Date intervention</div><div class="val">
            {{ $dossier->intervention ? \Carbon\Carbon::parse($dossier->intervention->date_intervention)->format('d/m/Y') : now()->format('d/m/Y') }}
        </div></div>
    </div>
</div>

@if($dossier->intervention)
@php $inter = $dossier->intervention; @endphp

<div class="section">
    <div class="section-title">Compte-Rendu Technique</div>
    <div style="border: 1px solid #e2e8f0; padding: 10px; border-radius: 6px; min-height: 60px;">
        {{ $inter->rapport_technique ?? $inter->compte_rendu ?? 'Non renseigné' }}
    </div>
</div>

@if($inter->pieces && $inter->pieces->count())
<div class="section">
    <div class="section-title">Pièces Consommées</div>
    <table>
        <thead>
            <tr>
                <th>Désignation</th>
                <th>Référence</th>
                <th>Qté</th>
                <th>PU TTC</th>
                <th>Total TTC</th>
            </tr>
        </thead>
        <tbody>
            @php $totalPieces = 0; @endphp
            @foreach($inter->pieces as $p)
            @php
                $pu    = $p->pivot->prix_unitaire ?? $p->prix_vente ?? 0;
                $qty   = $p->pivot->quantite ?? 1;
                $ligne = $pu * $qty;
                $totalPieces += $ligne;
            @endphp
            <tr>
                <td>{{ $p->nom }}</td>
                <td>{{ $p->reference ?? '—' }}</td>
                <td>{{ $qty }}</td>
                <td>{{ number_format($pu, 3, ',', ' ') }} DA</td>
                <td>{{ number_format($ligne, 3, ',', ' ') }} DA</td>
            </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="4" style="text-align:right;">Total Pièces :</td>
                <td>{{ number_format($totalPieces, 3, ',', ' ') }} DA</td>
            </tr>
        </tbody>
    </table>
</div>
@endif

@if($inter->tarifsMo && $inter->tarifsMo->count())
<div class="section">
    <div class="section-title">Main d'Œuvre</div>
    <table>
        <thead><tr><th>Prestation</th><th>Montant TTC</th></tr></thead>
        <tbody>
            @php $totalMO = 0; @endphp
            @foreach($inter->tarifsMo as $mo)
            @php $montant = $mo->pivot->montant ?? $mo->montant; $totalMO += $montant; @endphp
            <tr>
                <td>{{ $mo->type_intervention }}</td>
                <td>{{ number_format($montant, 3, ',', ' ') }} DA</td>
            </tr>
            @endforeach
            <tr class="total-row">
                <td style="text-align:right;">Total MO :</td>
                <td>{{ number_format($totalMO, 3, ',', ' ') }} DA</td>
            </tr>
        </tbody>
    </table>
</div>
@endif

<div>
    <span class="result-box result-repare">✓ Appareil RÉPARÉ</span>
</div>
@endif

<div class="signature">
    <div class="sign-box">Signature Technicien</div>
    <div class="sign-box">Cachet & Signature SAV</div>
</div>

<div class="footer">
    Document confidentiel — Généré le {{ now()->format('d/m/Y à H:i') }} — Maison Tel SAV
</div>

</body>
</html>
