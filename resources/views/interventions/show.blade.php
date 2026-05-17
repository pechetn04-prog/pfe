@extends('layouts.app')

@section('title', 'Rapport d\'Intervention — #' . $intervention->dossier->num_dossier)

@section('content')
    <div class="container-fluid" style="max-width: 1000px;">

        <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
            <!-- En-tête : Titre et bouton de retour au dossier -->
            <div class="d-flex align-items-center gap-3">
                <a href="{{ route('dossiers.show', $intervention->dossier_id) }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div>
                    <h1 class="h4 fw-bold mb-0">Rapport d'Intervention</h1>
                    <small class="text-muted">Dossier #{{ $intervention->dossier->num_dossier }} —
                        {{ $intervention->dossier->client->name ?? '—' }}</small>
                </div>
            </div>
            
            <!-- Actions : Génération PDF -->
            <div class="d-flex gap-2">
                <a href="{{ route('dossiers.intervention.pdf', $intervention->dossier_id) }}" target="_blank"
                    class="btn btn-sm btn-outline-danger px-3 shadow-sm fw-bold">
                    <i class="fas fa-file-pdf me-1"></i> Imprimer / PDF
                </a>
            </div>
        </div>

        @php
            $status = $intervention->dossier->statut;
            $borderColor = '#6b7280'; // default gray
            $badgeBg = 'bg-secondary';
            $statusText = 'Terminé';

            if ($status === 'REPARE' || $status === 'FACTURE' || $status === 'LIVRE' || $status === 'CLOTURE') {
                $borderColor = '#10b981'; // green
                $badgeBg = 'bg-success';
                $statusText = 'APPAREIL RÉPARÉ';
            } elseif ($status === 'IRREPARABLE' || $status === 'ATTENTE_VALIDATION_REMPLACEMENT') {
                $borderColor = '#ef4444'; // red
                $badgeBg = 'bg-danger';
                $statusText = 'APPAREIL IRRÉPARABLE';
            } elseif ($status === 'ATTENTE_PIECE') {
                $borderColor = '#f59e0b'; // orange
                $badgeBg = 'bg-warning text-dark';
                $statusText = 'EN ATTENTE DE PIÈCES';
            }
        @endphp

        {{-- Décision technique / Statut Final --}}
        <div class="card border-0 shadow-sm mb-4"
            style="border-radius: 14px; border-left: 5px solid {{ $borderColor }} !important;">
            <div class="card-body p-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
                <div>
                    <div class="text-muted small mb-1">Verdict de l'Intervention</div>
                    <span class="badge {{ $badgeBg }} fs-6 px-4 py-2 rounded-pill fw-bold">
                        <i class="fas fa-check-circle me-2"></i>{{ $statusText }}
                    </span>
                </div>
                <div class="text-end">
                    <div class="text-muted small">Technicien en charge</div>
                    <div class="fw-bold text-dark">{{ $intervention->technicien->name ?? '—' }}</div>
                    <div class="text-muted small">{{ $intervention->date_fin->format('d/m/Y à H:i') }}</div>
                </div>
            </div>
        </div>

        <div class="row g-4">

            {{-- Compte-rendu --}}
            <div class="col-md-8">
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 14px;">
                    <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                        <h6 class="fw-bold"><i class="fas fa-clipboard-list me-2 text-primary"></i>Rapport d'Atelier / Compte-rendu</h6>
                    </div>
                    <div class="card-body px-4 pb-4">
                        <div class="p-3 rounded-3 bg-light border text-dark" style="min-height: 120px; white-space: pre-wrap;">
                            {{ $intervention->rapport_technique ?? $intervention->compte_rendu ?? 'Aucun détail fourni.' }}
                        </div>
                    </div>
                </div>

                {{-- Photo Jointes --}}
                @if($intervention->photo_intervention)
                    <div class="card border-0 shadow-sm" style="border-radius: 14px;">
                        <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                            <h6 class="fw-bold"><i class="fas fa-camera me-2 text-primary"></i>Preuve Visuelle / Photo</h6>
                        </div>
                        <div class="card-body px-4 pb-4 text-center">
                            <div class="rounded-3 overflow-hidden border d-inline-block shadow-sm" style="max-width: 100%;">
                                <img src="{{ asset('storage/' . $intervention->photo_intervention) }}"
                                    alt="Photo d'Intervention" class="img-fluid" style="max-height: 450px; object-fit: contain;">
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Infos dossier --}}
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100" style="border-radius: 14px;">
                    <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                        <h6 class="fw-bold"><i class="fas fa-info-circle me-2 text-primary"></i>Informations Matériel</h6>
                    </div>
                    <div class="card-body px-4 pb-4">
                        <div class="mb-3">
                            <div class="small text-muted mb-1">Modèle de l'appareil</div>
                            <div class="fw-bold text-dark">{{ $intervention->dossier->appareil->modele ?? '—' }}</div>
                        </div>
                        
                        <div class="mb-3">
                            <div class="small text-muted mb-1">Numéro IMEI</div>
                            <div class="fw-bold font-monospace text-secondary" style="font-size: 0.9rem;">{{ $intervention->dossier->imei }}</div>
                        </div>

                        <div class="mb-3">
                            <div class="small text-muted mb-1">Garantie active</div>
                            <span class="badge {{ $intervention->dossier->sous_garantie ? 'bg-success' : 'bg-secondary' }} rounded-pill px-3 py-1">
                                {{ $intervention->dossier->sous_garantie ? 'Sous Garantie' : 'Hors Garantie' }}
                            </span>
                        </div>

                        @if($intervention->dossier->fin_garantie)
                            <div class="mb-3">
                                <div class="small text-muted mb-1">Fin de garantie</div>
                                <div class="small text-dark fw-bold">{{ \Carbon\Carbon::parse($intervention->dossier->fin_garantie)->format('d/m/Y') }}</div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Pièces utilisées --}}
            @if($intervention->pieces->count() > 0)
                <div class="col-12 mt-4">
                    <div class="card border-0 shadow-sm" style="border-radius: 14px;">
                        <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                            <h6 class="fw-bold text-dark"><i class="fas fa-boxes me-2 text-primary"></i>Pièces Détachées Consommées</h6>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light">
                                    <tr class="small text-muted text-uppercase fw-bold">
                                        <th class="ps-4">Référence</th>
                                        <th>Désignation de la Pièce</th>
                                        <th class="text-center">Quantité</th>
                                        <th class="text-end pe-4">Statut Stock</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($intervention->pieces as $piece)
                                        <tr>
                                            <td class="ps-4 small font-monospace text-muted">{{ $piece->reference ?? '—' }}</td>
                                            <td class="fw-semibold text-dark">{{ $piece->nom }}</td>
                                            <td class="text-center fw-bold">{{ $piece->pivot->quantite ?? 1 }}</td>
                                            <td class="text-end pe-4">
                                                <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 fw-bold">
                                                    <i class="fas fa-check me-1"></i>Décompté
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif

        </div>

    </div>
@endsection