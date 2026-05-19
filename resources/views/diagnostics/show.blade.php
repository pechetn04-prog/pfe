@extends('layouts.app')

@section('title', 'Rapport de Diagnostic — #{{ $dossier->num_dossier }}')

@section('content')
    <!-- Page de consultation du Rapport de Diagnostic (Vue HTML) -->
    <div class="container-fluid container-max-1000">

        <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
            <!-- En-tête : Titre et bouton de retour au dossier -->
            <div class="d-flex align-items-center gap-3">
                @if(auth()->user()->role === 'Technicien')
                    <a href="{{ route('technicien.dashboard') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                @else
                    <a href="{{ route('dossiers.show', $dossier->id) }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                @endif
                <div>
                    <h1 class="h4 fw-bold mb-0">Rapport de Diagnostic</h1>
                    <small class="text-muted">Dossier #{{ $dossier->num_dossier }} —
                        {{ $dossier->client->name ?? '—' }}</small>
                </div>
            </div>
            <!-- Actions : Génération PDF et saisie (si non fait) -->
            <div class="d-flex gap-2">
                <a href="{{ route('dossiers.diagnostic.pdf', $dossier->id) }}" target="_blank"
                    class="btn btn-sm btn-outline-danger px-3">
                    <i class="fas fa-file-pdf me-1"></i> PDF
                </a>
                @if(!$dossier->diagnostic && in_array($dossier->statut, ['EN_DIAGNOSTIC', 'AFFECTE']))
                    <a href="{{ route('diagnostics.create', $dossier->id) }}" class="btn btn-sm btn-primary px-3">
                        <i class="fas fa-plus me-1"></i> Saisir diagnostic
                    </a>
                @endif
            </div>
        </div>

        @if(!$dossier->diagnostic)
            <div class="alert alert-warning border-0 shadow-sm">
                <i class="fas fa-exclamation-triangle me-2"></i>
                Aucun diagnostic n'a encore été saisi pour ce dossier.
                @if(auth()->user()->role === 'Technicien' && $dossier->technicien_id === auth()->id())
                    <a href="{{ route('diagnostics.create', $dossier->id) }}" class="ms-2 btn btn-sm btn-warning">Saisir
                        maintenant</a>
                @endif
            </div>
        @else


            <div class="row g-4">

                {{-- Constat --}}
                <div class="col-md-8">
                    <div class="card border-0 shadow-sm h-100 card-diagnostic-show-box">
                        <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                            <h6 class="fw-bold"><i class="fas fa-clipboard me-2 text-primary"></i>Constat Technique</h6>
                        </div>
                        <div class="card-body px-4 pb-4">
                            <div class="p-3 rounded-3 bg-light border constat-body">
                                {{ $diag->constat ?? $diag->constat_technique ?? 'Non renseigné' }}
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Infos dossier --}}
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm h-100 card-diagnostic-show-box">
                        <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                            <h6 class="fw-bold"><i class="fas fa-info-circle me-2 text-primary"></i>Informations</h6>
                        </div>
                        <div class="card-body px-4 pb-4">
                            <div class="mb-3">
                                <div class="small text-muted">Technicien Expert</div>
                                <div class="fw-bold text-dark">{{ $dossier->technicien->name ?? '—' }}</div>
                            </div>
                            <div class="mb-3">
                                <div class="small text-muted">Date du Diagnostic</div>
                                <div class="fw-semibold text-secondary">{{ $diag->created_at->format('d/m/Y à H:i') }}</div>
                            </div>
                            <div class="mb-3">
                                <div class="small text-muted">IMEI / SN</div>
                                <div class="fw-semibold font-monospace text-dark">{{ $dossier->imei }}</div>
                            </div>
                            <div class="mb-3">
                                <div class="small text-muted">Garantie</div>
                                @if($dossier->garantie_annulee || !empty($diag->motif_exclusion))
                                    <span class="badge bg-warning text-dark rounded-pill fw-bold">
                                        GARANTIE EXCLUE
                                    </span>
                                @elseif($dossier->sous_garantie)
                                    <span class="badge bg-success rounded-pill">
                                        Sous garantie
                                    </span>
                                @else
                                    <span class="badge bg-secondary rounded-pill">
                                        Hors garantie
                                    </span>
                                @endif
                            </div>
                            <div class="mb-3">
                                <div class="small text-muted">Décision d'Expertise</div>
                                @if($isReparable)
                                    @if($isGarantieValide)
                                        <span class="badge bg-success text-white rounded-pill fw-bold d-block text-wrap text-start p-2">
                                            <i class="fas fa-tools me-1"></i> APPAREIL RÉPARABLE <br>
                                            <small class="fw-normal opacity-75">Réparation gratuite (sous garantie)</small>
                                        </span>
                                    @else
                                        <span class="badge bg-primary text-white rounded-pill fw-bold d-block text-wrap text-start p-2">
                                            <i class="fas fa-file-invoice-dollar me-1"></i> APPAREIL RÉPARABLE <br>
                                            <small class="fw-normal opacity-75">Hors garantie (Attente Devis)</small>
                                        </span>
                                    @endif
                                @else
                                    @if($isGarantieValide)
                                        <span class="badge bg-warning text-dark rounded-pill fw-bold d-block text-wrap text-start p-2">
                                            <i class="fas fa-exchange-alt me-1"></i> ON NE PEUT PAS RÉPARER <br>
                                            <small class="fw-normal opacity-85 text-dark">En attente validation remplacement</small>
                                        </span>
                                    @else
                                        <span class="badge bg-danger text-white rounded-pill fw-bold d-block text-wrap text-start p-2">
                                            <i class="fas fa-times-circle me-1"></i> ON NE PEUT PAS RÉPARER <br>
                                            <small class="fw-normal opacity-75">Hors garantie (Restitution)</small>
                                        </span>
                                    @endif
                                @endif
                            </div>
                            @if(!empty($diag->motif_exclusion))
                                <div class="mb-3">
                                    <div class="small text-muted">Motif exclusion garantie</div>
                                    <div class="text-danger small fw-semibold">
                                        <i class="fas fa-exclamation-triangle me-1"></i> {{ $diag->motif_exclusion }}
                                    </div>
                                </div>
                            @endif
                            @if(!empty($diag->exclusion_commentaire))
                                <div class="mb-3">
                                    <div class="small text-muted">Commentaire d'exclusion</div>
                                    <div class="text-muted small">
                                        {{ $diag->exclusion_commentaire }}
                                    </div>
                                </div>
                            @endif
                           
                        </div>
                    </div>
                </div>

                {{-- Recommandation --}}
                @if($diag->recommandation ?? false)
                    <div class="col-12">
                        <div class="card border-0 shadow-sm card-diagnostic-show-box">
                            <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                                <h6 class="fw-bold"><i class="fas fa-lightbulb me-2 text-warning"></i>Recommandation</h6>
                            </div>
                            <div class="card-body px-4 pb-4">
                                <div class="p-3 rounded-3 recommandation-body">
                                    {{ $diag->recommandation }}
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Pièces nécessaires --}}
                @if($diag->pieces && $diag->pieces->count())
                    <div class="col-12">
                        <div class="card border-0 shadow-sm card-diagnostic-show-box">
                            <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                                <h6 class="fw-bold"><i class="fas fa-boxes me-2 text-primary"></i>Pièces Nécessaires</h6>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="bg-light">
                                        <tr class="small text-muted text-uppercase">
                                            <th class="ps-4">Référence</th>
                                            <th>Désignation</th>
                                            <th>Qté</th>
                                            <th>Prix unitaire TTC</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($diag->pieces as $piece)
                                            <tr>
                                                <td class="ps-4 small font-monospace text-muted">{{ $piece->reference ?? '—' }}</td>
                                                <td class="fw-semibold">{{ $piece->nom }}</td>
                                                <td>{{ $piece->pivot->quantite ?? 1 }}</td>
                                                <td>{{ number_format($piece->pivot->prix_unitaire ?? $piece->prix_unitaire) }}
                                                    {{ $company->devise ?? 'TND' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Prestations / Main d'œuvre --}}
                @if($diag->tarifsMo && $diag->tarifsMo->count())
                    <div class="col-12">
                        <div class="card border-0 shadow-sm card-diagnostic-show-box">
                            <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                                <h6 class="fw-bold"><i class="fas fa-tools me-2 text-info"></i>Prestations & Main d'œuvre</h6>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="bg-light">
                                        <tr class="small text-muted text-uppercase">
                                            <th class="ps-4">Type d'intervention</th>
                                            <th>Montant TTC</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($diag->tarifsMo as $mo)
                                            <tr>
                                                <td class="ps-4 fw-semibold">{{ $mo->type_intervention }}</td>
                                                <td>{{ number_format($mo->pivot->montant ?? $mo->montant) }}
                                                    {{ $company->devise ?? 'TND' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif

            </div>
        @endif

    </div>
@endsection