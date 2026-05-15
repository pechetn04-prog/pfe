<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Suivi Dossier #{{ $dossier->num_dossier }} — Maison Tel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Outfit', sans-serif; background: #f1f5f9; }
        .navbar-brand { font-weight: 800; font-size: 1.3rem; }
        .status-badge { font-size: 1rem; padding: 0.5rem 1.5rem; border-radius: 30px; }
        .timeline { position: relative; padding-left: 2rem; }
        .timeline::before { content: ''; position: absolute; left: 0.6rem; top: 0; bottom: 0; width: 2px; background: #e2e8f0; }
        .timeline-item { position: relative; padding-bottom: 1.5rem; }
        .timeline-dot { width: 14px; height: 14px; border-radius: 50%; background: #2563eb; position: absolute; left: -1.6rem; top: 4px; border: 2px solid white; box-shadow: 0 0 0 2px #2563eb; }
        .timeline-dot.done { background: #10b981; box-shadow: 0 0 0 2px #10b981; }
        .card { border-radius: 14px; }
        .info-label { font-size: 0.72rem; text-transform: uppercase; color: #94a3b8; font-weight: 600; }
        .info-val { font-weight: 600; color: #0f172a; }
    </style>
</head>
<body>

{{-- Navbar --}}
<nav class="navbar navbar-light bg-white border-bottom px-4 py-3">
    <a class="navbar-brand" href="{{ route('client.suivi') }}">📱 Maison Tel</a>
    <a href="{{ route('client.suivi') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
        <i class="fas fa-search me-1"></i> Nouvelle recherche
    </a>
</nav>

<div class="container py-4" style="max-width: 760px;">

    {{-- Header dossier --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
                <div>
                    <div class="text-muted small mb-1">Dossier SAV</div>
                    <h1 class="h4 fw-bold mb-1">#{{ $dossier->num_dossier }}</h1>
                    <div class="text-muted small">
                        <i class="fas fa-calendar-alt me-1"></i>
                        Reçu le {{ \Carbon\Carbon::parse($dossier->date_reception)->format('d/m/Y') }}
                    </div>
                </div>
                <div class="text-end">
                    @php
                        $badges = [
                            'AFFECTE'          => ['secondary', 'Affecté'],
                            'EN_DIAGNOSTIC'    => ['info',      'Diagnostic en cours'],
                            'EN_ATTENTE_DEVIS' => ['warning',   'Devis en cours'],
                            'EN_REPARATION'    => ['primary',   'En réparation'],
                            'ATTENTE_PIECE'    => ['warning',   'Attente pièce'],
                            'REPARE'           => ['success',   'Réparé ✓'],
                            'FACTURE'          => ['success',   'Facturé ✓'],
                            'LIVRE'            => ['success',   'Livré ✓'],
                            'CLOTURE'          => ['dark',      'Clôturé'],
                            'IRREPARABLE'      => ['danger',    'Irréparable'],
                            'DEVIS_REFUSE'     => ['danger',    'Devis refusé'],
                            'REMPLACEMENT_PRET'=> ['success',   'Remplacement prêt'],
                        ];
                        $b = $badges[$dossier->statut] ?? ['secondary', $dossier->statut];
                    @endphp
                    <span class="badge bg-{{ $b[0] }} status-badge">{{ $b[1] }}</span>
                    <div class="small text-muted mt-1">
                        <span class="badge {{ $dossier->sous_garantie ? 'bg-success' : 'bg-light text-muted' }} rounded-pill">
                            {{ $dossier->sous_garantie ? '🛡️ Sous garantie' : 'Hors garantie' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Infos appareil (masquer données sensibles) --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white border-0 fw-bold pt-4 pb-0 px-4">
            <i class="fas fa-mobile-alt me-2 text-primary"></i> Appareil
        </div>
        <div class="card-body px-4 pb-4">
            <div class="row g-3">
                <div class="col-6 col-md-4">
                    <div class="info-label">IMEI</div>
                    <div class="info-val">{{ substr($dossier->imei, 0, 6) }}*****{{ substr($dossier->imei, -2) }}</div>
                </div>
                <div class="col-6 col-md-4">
                    <div class="info-label">Panne déclarée</div>
                    <div class="info-val">{{ Str::limit($dossier->panne_declaree, 50) }}</div>
                </div>
                <div class="col-6 col-md-4">
                    <div class="info-label">Dernière mise à jour</div>
                    <div class="info-val">{{ $dossier->updated_at->format('d/m/Y H:i') }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Frise chronologique --}}
    @if($dossier->suivi && $dossier->suivi->count())
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white border-0 fw-bold pt-4 pb-0 px-4">
            <i class="fas fa-history me-2 text-primary"></i> Historique de traitement
        </div>
        <div class="card-body px-4 pb-4">
            <div class="timeline mt-2">
                @foreach($dossier->suivi as $s)
                <div class="timeline-item">
                    <div class="timeline-dot {{ $loop->last ? '' : 'done' }}"></div>
                    <div class="small">
                        <span class="text-muted">{{ \Carbon\Carbon::parse($s->created_at)->format('d/m/Y à H:i') }}</span>
                        <span class="ms-2 fw-semibold">{{ $s->commentaire }}</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    {{-- Message bas de page --}}
    <div class="text-center text-muted small">
        <i class="fas fa-lock me-1"></i>
        Les informations personnelles et financières ne sont pas affichées dans ce suivi public.
        <br>Connectez-vous à votre <a href="{{ route('login') }}">espace client</a> pour plus de détails.
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
