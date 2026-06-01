<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Étiquette — #{{ $dossier->num_dossier }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f5f5f5; display: flex; justify-content: center; align-items: center; min-height: 100vh; }
        .etiquette {
            width: 90mm; min-height: 50mm; background: white;
            border: 2px solid #0f172a; border-radius: 8px;
            padding: 12px 14px; position: relative;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        .header { display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #0f172a; padding-bottom: 8px; margin-bottom: 8px; }
        .logo { font-size: 14px; font-weight: 900; color: #0f172a; letter-spacing: -0.5px; }
        .num { font-size: 18px; font-weight: 900; color: #2563eb; }
        .row { display: flex; margin-bottom: 4px; font-size: 10px; }
        .label { color: #64748b; min-width: 80px; text-transform: uppercase; font-size: 9px; }
        .val { font-weight: bold; color: #0f172a; }
        .statut { display: inline-block; background: #2563eb; color: white; font-size: 9px; padding: 2px 8px; border-radius: 10px; font-weight: bold; text-transform: uppercase; margin-top: 6px; }
        .footer { margin-top: 8px; border-top: 1px dashed #cbd5e1; padding-top: 6px; font-size: 8px; color: #94a3b8; text-align: center; }
        @media print {
            body { background: white; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div>
        <div class="etiquette">
            <div class="header">
                <div class="logo">Maison Tel</div>
                <div class="num">#{{ $dossier->num_dossier }}</div>
            </div>
            <div class="row">
                <span class="label">Client</span>
                <span class="val">{{ $dossier->client->name ?? '—' }}</span>
            </div>
            <div class="row">
                <span class="label">Tél.</span>
                <span class="val">{{ $dossier->client->telephone ?? '—' }}</span>
            </div>
            <div class="row">
                <span class="label">IMEI</span>
                <span class="val">{{ $dossier->imei }}</span>
            </div>
            <div class="row">
                <span class="label">Panne</span>
                <span class="val">{{ Str::limit($dossier->panne_declaree, 40) }}</span>
            </div>
            <div class="row">
                <span class="label">Réception</span>
                <span class="val">{{ \Carbon\Carbon::parse($dossier->date_reception)->format('d/m/Y') }}</span>
            </div>
            <span class="statut">{{ $dossier->statut }}</span>
            <div class="footer">Service Après-Vente · Maison Tel</div>
        </div>

        <div class="no-print text-center mt-3">
            <button onclick="window.print()" class="btn btn-primary">Imprimer l'étiquette</button>
            <a href="{{ route('dossiers.show', $dossier->id) }}" class="btn btn-outline-secondary ms-2">Retour</a>
        </div>
    </div>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</body>
</html>
