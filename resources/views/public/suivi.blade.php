<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Suivi Dossier #{{ $dossier->num_dossier }} — Maison Tel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/public_search.css') }}">
    <style>
        body {
            background-color: #f8fafc;
            padding: 40px 20px;
            display: block;
            /* Overriding center from public_search.css if needed */
            height: auto;
        }

        .main-container {
            max-width: 1000px;
            margin: 0 auto;
        }

        .premium-card {
            background: white;
            border-radius: 24px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
            padding: 30px;
            height: 100%;
        }

        .status-hero {
            background: #f8fafc;
            border-radius: 20px;
            padding: 30px;
            text-align: center;
        }

        .timeline-container {
            position: relative;
            padding-left: 30px;
            margin-top: 20px;
        }

        .timeline-container::before {
            content: '';
            position: absolute;
            left: 6px;
            top: 0;
            height: 100%;
            width: 2px;
            background: #e2e8f0;
        }

        .timeline-item {
            position: relative;
            margin-bottom: 25px;
        }

        .timeline-marker {
            position: absolute;
            left: -30px;
            top: 4px;
            width: 14px;
            height: 14px;
            border-radius: 50%;
            background: white;
            border: 3px solid #2563eb;
            z-index: 1;
        }

        .timeline-item:first-child .timeline-marker {
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
        }

        .info-list {
            margin-top: 25px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #f1f5f9;
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-label {
            color: #94a3b8;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
        }

        .info-value {
            color: #1e293b;
            font-weight: 700;
            font-size: 14px;
        }

        .section-title {
            font-weight: 800;
            font-size: 18px;
            margin-bottom: 20px;
            color: #0f172a;
            display: flex;
            align-items: center;
            gap: 10px;
        }
    </style>
</head>

<body>

    <div class="main-container">

        <div class="d-flex align-items-center justify-content-between mb-4">
            <a href="{{ route('client.suivi') }}"
                class="btn btn-white shadow-sm border rounded-pill px-4 py-2 fw-bold text-muted text-decoration-none bg-white">
                <i class="fas fa-arrow-left me-2"></i> Retour
            </a>
            <div class="text-end">
                <span class="small text-muted d-block mb-1">Dossier de réparation</span>
                <span
                    class="badge bg-soft-primary text-primary px-3 py-2 rounded-pill fw-bold border">#{{ $dossier->num_dossier }}</span>
            </div>
        </div>

        <div class="row g-4">
            {{-- GAUCHE : Statut Actuel et Infos --}}
            <div class="col-lg-5">
                <div class="premium-card">
                    @php
                        $statusConfig = [
                            'RECU' => ['icon' => 'fa-box-open', 'color' => '#64748b', 'label' => 'Reçu', 'desc' => 'Appareil bien réceptionné.'],
                            'EN_DIAGNOSTIC' => ['icon' => 'fa-microscope', 'color' => '#f59e0b', 'label' => 'Diagnostic', 'desc' => 'Analyse technique en cours.'],
                            'EN_ATTENTE_DEVIS' => ['icon' => 'fa-file-invoice-dollar', 'color' => '#ea580c', 'label' => 'Devis Prêt', 'desc' => 'En attente de votre validation.'],
                            'EN_REPARATION' => ['icon' => 'fa-wrench', 'color' => '#2563eb', 'label' => 'Réparation', 'desc' => 'Intervention technique en cours.'],
                            'REPARE' => ['icon' => 'fa-check-double', 'color' => '#10b981', 'label' => 'Réparé !', 'desc' => 'Prêt pour le retrait.'],
                            'LIVRE' => ['icon' => 'fa-hand-holding-heart', 'color' => '#059669', 'label' => 'Livré', 'desc' => 'Appareil restitué au client.'],
                            'ATTENTE_PIECE' => ['icon' => 'fa-hourglass-start', 'color' => '#ef4444', 'label' => 'Attente Pièces', 'desc' => 'En attente de composants.'],
                            'IRREPARABLE' => ['icon' => 'fa-exclamation-triangle', 'color' => '#b91c1c', 'label' => 'Irréparable', 'desc' => 'Dossier classé non réparable.'],
                            'REMPLACEMENT_PRET' => ['icon' => 'fa-sync-alt', 'color' => '#10b981', 'label' => 'Échange Prêt', 'desc' => 'Nouvel appareil disponible.'],
                        ];
                        $conf = $statusConfig[$dossier->statut] ?? ['icon' => 'fa-info-circle', 'color' => '#64748b', 'label' => $dossier->statut, 'desc' => 'Suivi en cours...'];
                    @endphp

                    <div class="status-hero">
                        <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3"
                            style="width: 70px; height: 70px; background-color: {{ $conf['color'] }}15;">
                            <i class="fas {{ $conf['icon'] }} fs-3" style="color: {{ $conf['color'] }};"></i>
                        </div>
                        <h2 class="fw-800 mb-1" style="color: {{ $conf['color'] }}; font-size: 22px;">
                            {{ $conf['label'] }}</h2>
                        <p class="text-muted small mb-0">{{ $conf['desc'] }}</p>
                    </div>

                    <div class="info-list">
                        <h6 class="section-title mt-4" style="font-size: 15px;">
                            <i class="fas fa-info-circle text-muted"></i>
                            Détails de l'appareil
                        </h6>
                        <div class="info-row">
                            <span class="info-label">Modèle</span>
                            <span class="info-value">{{ $dossier->appareil->modele ?? '—' }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">IMEI (Masqué)</span>
                            <span class="info-value">{{ substr($dossier->imei, 0, 6) }}***</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Garantie</span>
                            @if($dossier->garantie_annulee)
                                <span class="info-value text-warning">Garantie Exclue</span>
                            @elseif($dossier->sous_garantie)
                                <span class="info-value text-success">Sous Garantie</span>
                            @else
                                <span class="info-value text-danger">Hors Garantie</span>
                            @endif
                        </div>
                        <div class="info-row">
                            <span class="info-label">Date Dépôt</span>
                            <span class="info-value">{{ $dossier->date_reception->format('d/m/Y') }}</span>
                        </div>
                    </div>

                    @if($dossier->statut === 'EN_ATTENTE_DEVIS')
                        <div class="mt-4 p-3 bg-soft-primary rounded-4 border-start border-primary border-4">
                            <p class="small fw-bold text-primary mb-1">Un devis vous attend !</p>
                            <p class="extra-small text-muted mb-3" style="font-size: 11px;">Connectez-vous pour valider le
                                devis.</p>
                            <a href="{{ route('login') }}" class="btn btn-primary w-100 rounded-pill fw-bold">Espace
                                Client</a>
                        </div>
                    @endif
                </div>
            </div>

            {{-- DROITE : Historique de suivi --}}
            <div class="col-lg-7">
                <div class="premium-card">
                    <h5 class="section-title">
                        <i class="fas fa-history text-primary"></i>
                        Historique de suivi
                    </h5>
                    <div class="timeline-container">
                        @forelse($dossier->suivi->sortByDesc('created_at') as $suivi)
                            <div class="timeline-item">
                                <div class="timeline-marker"></div>
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="text-muted extra-small"
                                        style="font-size: 11px;">{{ $suivi->created_at->format('d/m/Y H:i') }}</span>
                                </div>
                                <div class="text-dark small lh-base">{{ $suivi->commentaire }}</div>
                            </div>
                        @empty
                            <div class="text-center py-5 text-muted">Aucun historique disponible.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>

</html>