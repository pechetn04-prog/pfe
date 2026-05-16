@extends('layouts.app')

@section('title', 'Détails du Diagnostic - Dossier #' . $dossier->num_dossier)

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Rapport de Diagnostic</h1>
        <!-- Actions : Impression PDF et retour -->
        <div class="d-flex gap-2">
            <a href="{{ route('dossiers.diagnostic.pdf', $dossier->id) }}" target="_blank" class="btn btn-outline-danger shadow-sm">
                <i class="fas fa-file-pdf me-1"></i> Imprimer
            </a>
            <a href="{{ route('dossiers.show', $dossier->id) }}" class="btn btn-secondary">Retour au dossier</a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow border-0 mb-4" style="border-radius: 15px;">
                <!-- Bloc : Informations Générales de l'expertise -->
                <div class="card-header bg-primary text-white py-3" style="border-radius: 15px 15px 0 0;">
                    <h6 class="m-0 font-weight-bold">Informations Générales</h6>
                </div>
                <div class="card-body p-4">
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <label class="small text-muted fw-bold text-uppercase">Technicien</label>
                            <div class="fw-bold">{{ $dossier->diagnostic->technicien->name ?? '—' }}</div>
                        </div>
                        <div class="col-md-4">
                            <label class="small text-muted fw-bold text-uppercase">Date du Diagnostic</label>
                            <div class="fw-bold">{{ \Carbon\Carbon::parse($dossier->diagnostic->date_diagnostic)->format('d/m/Y H:i') }}</div>
                        </div>
                        <!-- Statut de réparabilité -->
                        <div class="col-md-4">
                            <label class="small text-muted fw-bold text-uppercase">Décision</label>
                            <div>
                                @if($dossier->diagnostic->is_reparable)
                                    <span class="badge bg-success rounded-pill px-3">RÉPARABLE</span>
                                @else
                                    <span class="badge bg-danger rounded-pill px-3">IRRÉPARABLE</span>
                                @endif
                                
                                @if($dossier->diagnostic->exclusion_garantie)
                                    <span class="badge bg-warning text-dark rounded-pill px-3 ms-2">EXCLUSION GARANTIE</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="small text-muted fw-bold text-uppercase">Constat Technique</label>
                        <div class="bg-light p-3 rounded border">
                            {{ $dossier->diagnostic->constat_technique }}
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="small text-muted fw-bold text-uppercase">Recommandation</label>
                        <div class="bg-light p-3 rounded border italic">
                            {{ $dossier->diagnostic->recommandation ?? 'Aucune recommandation particulière.' }}
                        </div>
                    </div>

                    @if($dossier->diagnostic->photo_panne)
                    <div class="mb-0">
                        <label class="small text-muted fw-bold text-uppercase mb-2 d-block">Pièce Jointe / Photo</label>
                        <div class="rounded-3 overflow-hidden border" style="max-width: 400px;">
                            <img src="{{ asset('storage/' . $dossier->diagnostic->photo_panne) }}" alt="Photo Diagnostic" class="img-fluid">
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
