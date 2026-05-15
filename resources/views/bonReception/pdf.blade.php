{{--
    bonReception/pdf.blade.php
    Alias vers le template principal du bon de réception.
    Ce fichier est utilisé si le FactureController ou un autre contrôleur pointe vers ce chemin.
--}}
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Bon de Réception — #{{ $dossier->num_dossier ?? 'SAV' }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 12px; color: #1e293b; padding: 24px; }

        /* En-tête */
        .header { display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 3px solid #2563eb; padding-bottom: 16px; margin-bottom: 24px; }
        .logo-area h1 { font-size: 22px; color: #2563eb; font-weight: 900; margin-bottom: 4px; }
        .logo-area p { font-size: 10px; color: #64748b; }
        .doc-info { text-align: right; }
        .doc-info h2 { font-size: 16px; font-weight: 700; color: #0f172a; text-transform: uppercase; letter-spacing: 1px; }
        .doc-info .num { font-size: 26px; font-weight: 900; color: #2563eb; }
        .doc-info .date { font-size: 10px; color: #64748b; margin-top: 4px; }

        /* Sections */
        .section { margin-bottom: 20px; }
        .section-title { background: #2563eb; color: white; padding: 6px 14px; font-weight: 700; font-size: 10px; text-transform: uppercase; letter-spacing: 0.5px; border-radius: 4px; margin-bottom: 12px; }

        /* Grille */
        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 10px 20px; }
        .grid-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 10px; }
        .field { margin-bottom: 6px; }
        .field .lbl { font-size: 9px; text-transform: uppercase; color: #94a3b8; font-weight: 700; margin-bottom: 2px; }
        .field .val { font-weight: 600; border-bottom: 1px solid #e2e8f0; padding-bottom: 3px; min-height: 18px; color: #0f172a; font-size: 12px; }

        /* Badges */
        .badge-garantie { display: inline-block; padding: 3px 12px; border-radius: 12px; font-size: 10px; font-weight: 700; }
        .badge-ok  { background: #dcfce7; color: #166534; }
        .badge-non { background: #fee2e2; color: #991b1b; }

        /* Panne / observations */
        .box { border: 1px solid #e2e8f0; border-radius: 6px; padding: 10px 14px; min-height: 50px; background: #f8fafc; font-size: 11px; color: #334155; }

        /* Accessoires */
        .accessories { display: flex; flex-wrap: wrap; gap: 6px; margin-top: 6px; }
        .acc-item { background: #eff6ff; color: #1d4ed8; padding: 3px 10px; border-radius: 6px; font-size: 10px; font-weight: 600; }

        /* Avertissement */
        .notice { background: #fef9c3; border: 1px solid #fde047; border-radius: 6px; padding: 10px 14px; font-size: 10px; color: #713f12; margin-bottom: 16px; }

        /* Signatures */
        .signatures { display: grid; grid-template-columns: 1fr 1fr; gap: 40px; margin-top: 40px; }
        .sign-box { border-top: 1px solid #94a3b8; padding-top: 8px; font-size: 10px; color: #64748b; }

        /* Pied de page */
        .footer { margin-top: 30px; text-align: center; font-size: 9px; color: #94a3b8; border-top: 1px solid #e2e8f0; padding-top: 10px; }
    </style>
</head>
<body>

{{-- ══════════════ EN-TÊTE ══════════════ --}}
<div class="header">
    <div class="logo-area">
        @if(!empty($company) && $company->logo)
            <img src="{{ public_path('storage/' . $company->logo) }}" alt="Logo" style="max-height:50px; margin-bottom:6px; display:block;">
        @endif
        <h1>📱 {{ $company->nom_societe ?? 'Maison Tel' }}</h1>
        <p>{{ $company->adresse ?? '' }}</p>
        <p>Tél : {{ $company->telephone ?? '' }} | {{ $company->email ?? '' }}</p>
        @if($company->numero_fiscal ?? null)
            <p>N° Fiscal : {{ $company->numero_fiscal }}</p>
        @endif
    </div>
    <div class="doc-info">
        <h2>Bon de Réception</h2>
        <div class="num">#{{ $dossier->num_dossier }}</div>
        <div class="date">Date : {{ \Carbon\Carbon::parse($dossier->date_reception ?? now())->format('d/m/Y') }}</div>
        <div class="date" style="margin-top:6px;">
            <span class="badge-garantie {{ $dossier->sous_garantie ? 'badge-ok' : 'badge-non' }}">
                {{ $dossier->sous_garantie ? '🛡️ Sous Garantie' : 'Hors Garantie' }}
            </span>
        </div>
    </div>
</div>

{{-- ══════════════ CLIENT ══════════════ --}}
<div class="section">
    <div class="section-title">Informations Client</div>
    <div class="grid-3">
        <div class="field">
            <div class="lbl">Nom complet</div>
            <div class="val">{{ $dossier->client->name ?? ($dossier->client_nom ?? '—') }}</div>
        </div>
        <div class="field">
            <div class="lbl">Téléphone</div>
            <div class="val">{{ $dossier->client->telephone ?? ($dossier->client_telephone ?? '—') }}</div>
        </div>
        <div class="field">
            <div class="lbl">Email</div>
            <div class="val">{{ $dossier->client->email ?? ($dossier->client_email ?? '—') }}</div>
        </div>
    </div>
</div>

{{-- ══════════════ APPAREIL ══════════════ --}}
<div class="section">
    <div class="section-title">Détails de l'Appareil</div>
    <div class="grid-3">
        <div class="field">
            <div class="lbl">IMEI / N° de série</div>
            <div class="val">{{ $dossier->imei }}</div>
        </div>
        <div class="field">
            <div class="lbl">Modèle / Article</div>
            <div class="val">{{ $dossier->modele ?? ($dossier->appareil->modele ?? '—') }}</div>
        </div>
        <div class="field">
            <div class="lbl">Référence produit</div>
            <div class="val">{{ $dossier->reference_produit ?? '—' }}</div>
        </div>
        <div class="field">
            <div class="lbl">État extérieur</div>
            <div class="val">{{ $dossier->etat_appareil ?? '—' }}</div>
        </div>
        <div class="field">
            <div class="lbl">Technicien assigné</div>
            <div class="val">{{ $dossier->technicien->name ?? 'En attente d\'affectation' }}</div>
        </div>
    </div>
</div>

{{-- ══════════════ PANNE ══════════════ --}}
<div class="section">
    <div class="section-title">Panne Déclarée</div>
    <div class="box">{{ $dossier->panne_declaree ?? '—' }}</div>
</div>

{{-- ══════════════ ACCESSOIRES ══════════════ --}}
<div class="section">
    <div class="section-title">Accessoires Remis</div>
    @php
        $acc = $dossier->accessoires_remis ?? null;
    @endphp
    @if($acc)
        <div class="accessories">
            @foreach(explode(',', $acc) as $item)
                @if(trim($item))
                    <span class="acc-item">{{ trim($item) }}</span>
                @endif
            @endforeach
        </div>
    @else
        <div class="box" style="min-height: 30px; color: #94a3b8;">Aucun accessoire remis.</div>
    @endif
</div>

{{-- ══════════════ AVERTISSEMENT ══════════════ --}}
<div class="notice">
    <strong>⚠️ Conditions de prise en charge :</strong>
    Ce bon de réception atteste uniquement de la prise en charge de l'appareil. Un devis sera établi après diagnostic.
    La société décline toute responsabilité pour les données contenues dans l'appareil.
    Durée maximale de dépôt : <strong>30 jours</strong> après notification de fin de réparation.
</div>

{{-- ══════════════ SIGNATURES ══════════════ --}}
<div class="signatures">
    <div class="sign-box">
        <strong>Signature de l'Agent SAV</strong><br><br><br><br>
        Nom : ____________________________
    </div>
    <div class="sign-box">
        <strong>Signature du Client</strong><br>
        <span style="font-size:9px;">(Lu et approuvé — J'accepte les conditions ci-dessus)</span><br><br>
        Nom : ____________________________
    </div>
</div>

{{-- ══════════════ PIED DE PAGE ══════════════ --}}
<div class="footer">
    {{ $company->nom_societe ?? 'Maison Tel' }} — {{ $company->adresse ?? '' }} —
    Tél : {{ $company->telephone ?? '' }} | {{ $company->email ?? '' }}<br>
    Document généré le {{ now()->format('d/m/Y à H:i') }} — Réf : #{{ $dossier->num_dossier }}
</div>

</body>
</html>
