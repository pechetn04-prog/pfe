@extends('layouts.app')

@section('title', 'Rapport de Diagnostic — #{{ $dossier->num_dossier }}')

@section('content')
<!-- Page de consultation du Rapport de Diagnostic (Vue HTML) -->
<div class="container-fluid" style="max-width: 1000px;">

    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
        <!-- En-tête : Titre et bouton de retour au dossier -->
        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('dossiers.show', $dossier->id) }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="h4 fw-bold mb-0">Rapport de Diagnostic</h1>
                <small class="text-muted">Dossier #{{ $dossier->num_dossier }} — {{ $dossier->client->name ?? '—' }}</small>
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
            <a href="{{ route('diagnostics.create', $dossier->id) }}" class="ms-2 btn btn-sm btn-warning">Saisir maintenant</a>
        @endif
    </div>
    @else
    @php $diag = $dossier->diagnostic; @endphp

    {{-- Décision technique --}}
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 14px; border-left: 5px solid {{ ($diag->is_reparable ?? false) ? '#10b981' : '#ef4444' }} !important;">
        <div class="card-body p-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div>
                <div class="text-muted small mb-1">Décision Technique</div>
                @if($diag->is_reparable ?? false)
                    <span class="badge bg-success fs-6 px-4 py-2 rounded-pill">
                        <i class="fas fa-check-circle me-2"></i>Appareil RÉPARABLE
                    </span>
                @else
                    <span class="badge bg-danger fs-6 px-4 py-2 rounded-pill">
                        <i class="fas fa-times-circle me-2"></i>Appareil IRRÉPARABLE
                    </span>
                @endif
            </div>
            <div class="text-end">
                <div class="text-muted small">Technicien</div>
                <div class="fw-bold">{{ $dossier->technicien->name ?? '—' }}</div>
                <div class="text-muted small">{{ \Carbon\Carbon::parse($diag->created_at)->format('d/m/Y à H:i') }}</div>
            </div>
        </div>
    </div>

    <div class="row g-4">

        {{-- Constat --}}
        <div class="col-md-8">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 14px;">
                <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                    <h6 class="fw-bold"><i class="fas fa-clipboard me-2 text-primary"></i>Constat Technique</h6>
                </div>
                <div class="card-body px-4 pb-4">
                    <div class="p-3 rounded-3 bg-light border" style="min-height: 100px;">
                        {{ $diag->constat ?? $diag->constat_technique ?? 'Non renseigné' }}
                    </div>
                </div>
            </div>
        </div>

        {{-- Infos dossier --}}
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 14px;">
                <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                    <h6 class="fw-bold"><i class="fas fa-info-circle me-2 text-primary"></i>Informations</h6>
                </div>
                <div class="card-body px-4 pb-4">
                    <div class="mb-2">
                        <div class="small text-muted">IMEI</div>
                        <div class="fw-semibold font-monospace">{{ $dossier->imei }}</div>
                    </div>
                    <div class="mb-2">
                        <div class="small text-muted">Garantie</div>
                        <span class="badge {{ $dossier->sous_garantie ? 'bg-success' : 'bg-secondary' }} rounded-pill">
                            {{ $dossier->sous_garantie ? 'Sous garantie' : 'Hors garantie' }}
                        </span>
                    </div>
                    @if($diag->exclusion_garantie ?? false)
                    <div class="mt-2">
                        <div class="small text-muted">Motif exclusion garantie</div>
                        <div class="text-danger small fw-semibold">{{ $diag->motif_exclusion ?? $diag->exclusion_commentaire }}</div>
                    </div>
                    @endif
                    <div class="mb-2">
                        <div class="small text-muted">Type de panne</div>
                        <div class="fw-semibold">{{ $diag->type_panne ?? '—' }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Recommandation --}}
        @if($diag->recommandation ?? false)
        <div class="col-12">
            <div class="card border-0 shadow-sm" style="border-radius: 14px;">
                <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                    <h6 class="fw-bold"><i class="fas fa-lightbulb me-2 text-warning"></i>Recommandation</h6>
                </div>
                <div class="card-body px-4 pb-4">
                    <div class="p-3 rounded-3" style="background: #fffbeb; border: 1px solid #fde68a;">
                        {{ $diag->recommandation }}
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- Pièces nécessaires --}}
        @if($diag->pieces && $diag->pieces->count())
        <div class="col-12">
            <div class="card border-0 shadow-sm" style="border-radius: 14px;">
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
                                <td>{{ number_format($piece->prix_vente ?? 0, 3, ',', ' ') }} {{ $company->devise ?? 'TND' }}</td>
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
