<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Rapport de Diagnostic — #{{ $dossier->num_dossier }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; color: #1e293b; margin: 0; padding: 20px; }
        .header { display: flex; justify-content: space-between; border-bottom: 3px solid #2563eb; padding-bottom: 15px; margin-bottom: 25px; }
        .company h1 { font-size: 20px; color: #2563eb; margin: 0 0 4px 0; }
        .doc-title h2 { font-size: 16px; margin: 0; color: #0f172a; text-align: right; }
        .doc-title .num { font-size: 22px; font-weight: 900; color: #2563eb; text-align: right; }
        .section { margin-bottom: 20px; }
        .section-title { background: #1e3a5f; color: white; padding: 6px 12px; font-weight: bold; font-size: 11px; text-transform: uppercase; border-radius: 4px; margin-bottom: 12px; }
        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
        .field .label { color: #64748b; font-size: 10px; text-transform: uppercase; margin-bottom: 2px; }
        .field .val { font-weight: bold; border-bottom: 1px solid #e2e8f0; padding-bottom: 3px; min-height: 18px; }
        .decision-box { padding: 10px 16px; border-radius: 6px; font-weight: bold; font-size: 13px; display: inline-block; margin-top: 8px; }
        .decision-reparable { background: #dcfce7; color: #166534; border: 1px solid #86efac; }
        .decision-irreparable { background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; }
        .garantie-ok { background: #dcfce7; color: #166534; padding: 2px 10px; border-radius: 10px; font-size: 10px; }
        .garantie-non { background: #fee2e2; color: #991b1b; padding: 2px 10px; border-radius: 10px; font-size: 10px; }
        .exclusion-box { background: #fef3c7; border: 1px solid #fcd34d; padding: 8px 12px; border-radius: 6px; font-size: 11px; margin-top: 6px; }
        table { width: 100%; border-collapse: collapse; font-size: 11px; margin-top: 8px; }
        th { background: #f1f5f9; padding: 6px 10px; text-align: left; font-weight: bold; color: #475569; text-transform: uppercase; font-size: 10px; }
        td { padding: 6px 10px; border-bottom: 1px solid #e2e8f0; }
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
        <h2>RAPPORT DE DIAGNOSTIC</h2>
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
        <div class="field"><div class="label">Garantie</div><div class="val">
            <span class="{{ $dossier->sous_garantie ? 'garantie-ok' : 'garantie-non' }}">
                {{ $dossier->sous_garantie ? 'Sous garantie' : 'Hors garantie' }}
            </span>
        </div></div>
        <div class="field"><div class="label">Panne déclarée</div><div class="val">{{ $dossier->panne_declaree }}</div></div>
        <div class="field"><div class="label">Date diagnostic</div><div class="val">{{ now()->format('d/m/Y') }}</div></div>
    </div>
</div>

@if($dossier->diagnostic)
@php $diag = $dossier->diagnostic; @endphp

<div class="section">
    <div class="section-title">Constat Technique</div>
    <div style="border: 1px solid #e2e8f0; padding: 10px; border-radius: 6px; min-height: 50px;">
        {{ $diag->constat ?? $diag->constat_technique ?? 'Non renseigné' }}
    </div>
</div>

<div class="section">
    <div class="section-title">Recommandation</div>
    <div style="border: 1px solid #e2e8f0; padding: 10px; border-radius: 6px; min-height: 40px;">
        {{ $diag->recommandation ?? 'Non renseignée' }}
    </div>
</div>

@if($diag->exclusion_garantie ?? false)
<div class="exclusion-box">
    ⚠️ <strong>Exclusion de garantie :</strong> {{ $diag->motif_exclusion ?? $diag->exclusion_commentaire ?? 'Motif non précisé' }}
</div>
@endif

@if($diag->pieces && $diag->pieces->count())
<div class="section" style="margin-top:16px;">
    <div class="section-title">Pièces Nécessaires</div>
    <table>
        <thead><tr><th>Référence</th><th>Désignation</th><th>Qté</th><th>Prix Unitaire TTC</th></tr></thead>
        <tbody>
            @foreach($diag->pieces as $p)
            <tr>
                <td>{{ $p->reference ?? '—' }}</td>
                <td>{{ $p->nom }}</td>
                <td>{{ $p->pivot->quantite ?? 1 }}</td>
                <td>{{ number_format($p->prix_vente ?? 0, 3, ',', ' ') }} DA</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif

<div style="margin-top: 16px;">
    <div class="section-title" style="display:inline-block;">Décision Technique</div><br>
    @if($diag->is_reparable ?? false)
        <span class="decision-box decision-reparable">✓ Appareil RÉPARABLE</span>
    @else
        <span class="decision-box decision-irreparable">✗ Appareil IRRÉPARABLE</span>
    @endif
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
