<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Suivi Dossier #{{ $dossier->num_dossier }} — Maison Tel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/public_suivi.css') }}">

</head>
<body>

<div class="container-fluid px-4 py-5">
    <div class="row">
        <div class="col-12">

            {{-- En-tête avec navigation --}}
            <div class="d-flex align-items-center justify-content-between mb-4">
                <div class="d-flex align-items-center">
                    <a href="{{ route('client.suivi') }}" class="btn btn-white border shadow-sm rounded-pill p-2 d-flex align-items-center justify-content-center me-3" style="width: 45px; height: 45px;">
                        <i class="fas fa-arrow-left text-primary"></i>
                    </a>
                    <div>
                        <h1 class="h3 fw-bold mb-0 text-dark">Suivi de réparation</h1>
                        <span class="badge bg-soft-primary text-primary px-3 py-1 rounded-pill small">#{{ $dossier->num_dossier }}</span>
                    </div>
                </div>
                <div class="text-end d-none d-md-block">
                    <div class="text-muted small mb-1">Reçu le</div>
                    <div class="fw-bold text-dark">{{ \Carbon\Carbon::parse($dossier->date_reception)->format('d/m/Y') }}</div>
                </div>
            </div>

            {{-- Action Suivante Recommandée (Public) --}}
            @if($dossier->statut === 'EN_ATTENTE_DEVIS')
            <div class="card border-0 shadow-lg mb-5 overflow-hidden" style="border-radius: 20px; background: #2563eb; color: white;">
                <div class="card-body p-4 text-center py-5">
                    <div class="bg-white bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 60px; height: 60px;">
                        <i class="fas fa-lock fs-4"></i>
                    </div>
                    <h4 class="fw-bold mb-2">Un devis est prêt pour validation</h4>
                    <p class="mb-4 opacity-75">Pour accepter ou refuser ce devis, veuillez vous connecter à votre espace client.</p>
                    <a href="{{ route('login') }}" class="btn btn-white rounded-pill px-5 py-2 fw-bold shadow-sm">
                        Se connecter au portail
                    </a>
                </div>
            </div>
            @endif

            <div class="row g-4">
                
                {{-- Panneau GAUCHE (7/12) : Détails et Journal --}}
                <div class="col-lg-7 order-2 order-lg-1">
                    
                    {{-- Carte Infos Appareil --}}
                    <div class="card border-0 shadow-sm mb-4 overflow-hidden" style="border-radius: 24px; background: white;">
                        <div class="card-header bg-white border-0 py-4 px-4 d-flex justify-content-between align-items-center">
                            <h5 class="fw-bold mb-0 text-dark">Informations Appareil</h5>
                            <i class="fas fa-mobile-alt text-muted fs-4"></i>
                        </div>
                        <div class="card-body p-4 pt-0">
                            <div class="row g-4">
                                <div class="col-sm-4">
                                    <label class="small text-muted fw-bold text-uppercase mb-1 d-block">Appareil</label>
                                    <div class="fw-bold text-dark fs-5 text-truncate">{{ $dossier->appareil->modele ?? '—' }}</div>
                                </div>
                                <div class="col-sm-4">
                                    <label class="small text-muted fw-bold text-uppercase mb-1 d-block">IMEI (Masqué)</label>
                                    <div class="fw-bold text-dark fs-5 text-truncate">{{ substr($dossier->imei, 0, 6) }}*****{{ substr($dossier->imei, -2) }}</div>
                                </div>
                                <div class="col-sm-4">
                                    <label class="small text-muted fw-bold text-uppercase mb-1 d-block">Garantie</label>
                                    <div class="mt-1">
                                        @if($dossier->sous_garantie)
                                            <span class="badge bg-soft-success text-success px-3 py-1 rounded-pill fw-bold">
                                                <i class="fas fa-shield-alt me-1"></i> Sous Garantie
                                            </span>
                                        @else
                                            <span class="badge bg-soft-danger text-danger px-3 py-1 rounded-pill fw-bold">
                                                <i class="fas fa-exclamation-circle me-1"></i> Hors Garantie
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-12">
                                    <label class="small text-muted fw-bold text-uppercase mb-1 d-block">Panne déclarée</label>
                                    <div class="text-dark bg-light p-3 rounded-4" style="font-size: 0.95rem;">
                                        {{ $dossier->panne_declaree }}
                                    </div>
                                </div>
                                @if($dossier->imei_remplacement)
                                <div class="col-12 mt-2 pt-3 border-top">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-soft-success text-success p-2 rounded-3 me-3" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                                            <i class="fas fa-sync-alt"></i>
                                        </div>
                                        <div>
                                            <label class="small text-muted fw-bold text-uppercase mb-0 d-block" style="font-size: 0.65rem;">Nouvel Appareil (Échange à neuf)</label>
                                            <div class="fw-bold text-success" style="font-size: 1rem;">
                                                {{ $dossier->modele_remplacement }} 
                                                <span class="ms-2 text-muted fw-normal" style="font-size: 0.8rem;">IMEI : {{ substr($dossier->imei_remplacement, 0, 6) }}*****</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Journal de Suivi (Timeline stylée) --}}
                    <div class="card border-0 shadow-sm" style="border-radius: 24px; background: white;">
                        <div class="card-header bg-white border-0 py-4 px-4">
                            <h5 class="fw-bold mb-0 text-dark">Historique des étapes</h5>
                        </div>
                        <div class="card-body p-4 pt-0">
                            <div class="custom-timeline">
                                @forelse($dossier->suivi->sortByDesc('created_at') as $suivi)
                                <div class="timeline-item">
                                    <div class="timeline-marker"></div>
                                    <div class="timeline-content pb-4">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <span class="small fw-bold text-primary">{{ \Carbon\Carbon::parse($suivi->created_at)->diffForHumans() }}</span>
                                            <span class="text-muted" style="font-size: 0.75rem;">{{ \Carbon\Carbon::parse($suivi->created_at)->format('H:i') }}</span>
                                        </div>
                                        <div class="text-dark small">{{ $suivi->commentaire }}</div>
                                    </div>
                                </div>
                                @empty
                                <div class="text-center py-4 text-muted italic">Aucun historique disponible pour le moment.</div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Panneau DROIT (5/12) : État --}}
                <div class="col-lg-5 order-1 order-lg-2">
                    
                    {{-- Carte d'État Héro --}}
                    <div class="card border-0 shadow-lg p-5 mb-4 overflow-hidden" style="border-radius: 30px; background: white;">
                        @php
                            $statusConfig = [
                                'RECU' => ['icon' => 'fa-box-open', 'color' => '#64748b', 'label' => 'Dossier Reçu', 'desc' => 'Votre appareil a bien été réceptionné.'],
                                'EN_DIAGNOSTIC' => ['icon' => 'fa-microscope', 'color' => '#f59e0b', 'label' => 'En Diagnostic', 'desc' => 'Nos techniciens analysent la panne.'],
                                'EN_ATTENTE_DEVIS' => ['icon' => 'fa-file-invoice-dollar', 'color' => '#ea580c', 'label' => 'Attente Devis', 'desc' => 'Un devis est prêt pour validation.'],
                                'EN_REPARATION' => ['icon' => 'fa-wrench', 'color' => '#2563eb', 'label' => 'En Réparation', 'desc' => 'L\'intervention technique est en cours.'],
                                'REPARE' => ['icon' => 'fa-check-double', 'color' => '#10b981', 'label' => 'Réparé !', 'desc' => 'Votre appareil est prêt pour le retrait.'],
                                'LIVRE' => ['icon' => 'fa-hand-holding-heart', 'color' => '#059669', 'label' => 'Remis / Livré', 'desc' => 'Merci de votre confiance !'],
                                'ATTENTE_PIECE' => ['icon' => 'fa-hourglass-start', 'color' => '#ef4444', 'label' => 'Attente Pièces', 'desc' => 'Nous attendons les pièces détachées.'],
                                'IRREPARABLE' => ['icon' => 'fa-exclamation-triangle', 'color' => '#b91c1c', 'label' => 'Irréparable', 'desc' => 'Malheureusement, l\'appareil n\'est pas réparable.'],
                                'ATTENTE_VALIDATION_REMPLACEMENT' => ['icon' => 'fa-shield-alt', 'color' => '#1e69ff', 'label' => 'Attente Validation', 'desc' => 'L\'échange est en attente de validation administrative.'],
                                'REMPLACEMENT_VALIDE' => ['icon' => 'fa-check-circle', 'color' => '#10b981', 'label' => 'Échange Validé', 'desc' => 'L\'échange a été validé. Nous préparons votre nouvel appareil.'],
                                'REMPLACEMENT_PRET' => ['icon' => 'fa-box-open', 'color' => '#10b981', 'label' => 'Échange Prêt', 'desc' => 'Votre nouvel appareil est prêt pour le retrait.'],
                                'REMPLACEMENT_REFUSE' => ['icon' => 'fa-times-circle', 'color' => '#ef4444', 'label' => 'Échange Refusé', 'desc' => 'L\'échange a été refusé. Votre appareil initial est disponible pour retrait.'],
                            ];
                            $conf = $statusConfig[$dossier->statut] ?? ['icon' => 'fa-info-circle', 'color' => '#64748b', 'label' => $dossier->statut, 'desc' => 'Suivi en cours...'];
                        @endphp
                        
                        <div class="mb-4 position-relative">
                            <div class="rounded-circle d-flex align-items-center justify-content-center mb-3 shadow-sm" style="width: 80px; height: 80px; background-color: {{ $conf['color'] }}15;">
                                <i class="fas {{ $conf['icon'] }} fs-2" style="color: {{ $conf['color'] }};"></i>
                            </div>
                        </div>
                        
                        <div class="small text-muted text-uppercase fw-bold mb-1">Statut actuel</div>
                        <h2 class="fw-bold mb-2" style="color: {{ $conf['color'] }};">{{ $conf['label'] }}</h2>
                        <p class="text-muted mb-0">{{ $conf['desc'] }}</p>
                    </div>

                    {{-- Rappel connexion --}}
                    <div class="card border-0 shadow-sm p-4 text-center bg-soft-primary" style="border-radius: 24px;">
                        <p class="text-primary small mb-3 fw-bold">Vous voulez voir plus de détails ?</p>
                        <p class="text-muted small mb-3">Connectez-vous pour voir vos documents PDF, devis détaillés et factures.</p>
                        <a href="{{ route('login') }}" class="btn btn-primary w-100 rounded-pill fw-bold">Espace Client</a>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>