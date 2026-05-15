@extends('layouts.app')

@section('title', 'Rapport d\'Intervention')

@section('content')
<div class="container-fluid" style="max-width: 900px;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Rapport d'Intervention</h1>
            <small class="text-muted">DOSSIER #{{ $intervention->dossier->num_dossier }}</small>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('intervention.pdf', $intervention->id) }}" target="_blank" class="btn btn-outline-danger shadow-sm">
                <i class="fas fa-file-pdf me-1"></i> PDF
            </a>
            <a href="{{ route('dossiers.show', $intervention->dossier_id) }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Retour au dossier
            </a>
        </div>
    </div>

    <div class="row g-4">
        {{-- Détails de l'intervention --}}
        <div class="col-md-8">
            <div class="card shadow border-0 mb-4" style="border-radius: 15px;">
                <div class="card-header bg-white border-0 py-3 px-4">
                    <h6 class="fw-bold mb-0 text-primary">COMPTE-RENDU TECHNIQUE</h6>
                </div>
                <div class="card-body px-4 pb-4">
                    <div class="p-4 bg-light rounded-3 border" style="min-height: 200px; white-space: pre-wrap;">{{ $intervention->rapport_technique ?? $intervention->compte_rendu }}</div>
                </div>
            </div>

            {{-- Pièces consommées --}}
            <div class="card shadow border-0" style="border-radius: 15px;">
                <div class="card-header bg-white border-0 py-3 px-4">
                    <h6 class="fw-bold mb-0 text-primary">PIÈCES DÉTACHÉES UTILISÉES</h6>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light small text-uppercase fw-bold">
                            <tr>
                                <th class="ps-4">Pièce</th>
                                <th class="text-center">Quantité</th>
                                <th class="text-end pe-4">Statut Stock</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($intervention->pieces as $piece)
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-bold text-dark">{{ $piece->nom }}</div>
                                    <small class="text-muted font-monospace">{{ $piece->reference }}</small>
                                </td>
                                <td class="text-center fw-bold">{{ $piece->pivot->quantite }}</td>
                                <td class="text-end pe-4">
                                    <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3">Décompté</span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center py-4 text-muted fst-italic">Aucune pièce utilisée pour cette intervention.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Sidebar infos --}}
        <div class="col-md-4">
            <div class="card shadow border-0 mb-4" style="border-radius: 15px; border-left: 5px solid #10b981 !important;">
                <div class="card-body p-4">
                    <div class="text-muted small text-uppercase fw-bold mb-3">Statut Final</div>
                    <div class="d-flex align-items-center gap-2 mb-4">
                        <i class="fas fa-check-circle text-success fa-2x"></i>
                        <h4 class="fw-bold text-success mb-0">APPAREIL RÉPARÉ</h4>
                    </div>

                    <div class="mb-3">
                        <div class="text-muted small">Technicien</div>
                        <div class="fw-bold">{{ $intervention->technicien->name ?? '—' }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="text-muted small">Date d'intervention</div>
                        <div class="fw-bold">{{ \Carbon\Carbon::parse($intervention->date_intervention)->format('d/m/Y à H:i') }}</div>
                    </div>
                </div>
            </div>

            <div class="card shadow border-0" style="border-radius: 15px;">
                <div class="card-body p-4 text-center">
                    <i class="fas fa-print fa-3x text-muted opacity-25 mb-3"></i>
                    <p class="small text-muted">Ce document sert de justificatif technique pour la facturation et la garantie.</p>
                    <a href="{{ route('intervention.pdf', $intervention->id) }}" target="_blank" class="btn btn-primary w-100">
                        <i class="fas fa-print me-2"></i> Imprimer le Rapport
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
