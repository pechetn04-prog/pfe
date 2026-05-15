@extends('layouts.app')

@section('title', 'Détails de l\'Intervention - Dossier #' . $intervention->dossier->num_dossier)

@section('content')
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0 text-gray-800">Rapport d'Intervention</h1>
            <div class="d-flex gap-2">
                <a href="{{ route('dossiers.intervention.pdf', $intervention->dossier_id) }}" target="_blank"
                    class="btn btn-outline-danger shadow-sm">
                    <i class="fas fa-file-pdf me-1"></i> Imprimer
                </a>
                <a href="{{ route('dossiers.show', $intervention->dossier_id) }}" class="btn btn-secondary">Retour au
                    dossier</a>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card shadow border-0 mb-4" style="border-radius: 15px;">
                    <div class="card-header bg-primary text-white py-3" style="border-radius: 15px 15px 0 0;">
                        <h6 class="m-0 font-weight-bold">Informations Générales</h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <label class="small text-muted fw-bold text-uppercase">Technicien</label>
                                <div class="fw-bold">{{ $intervention->technicien->name ?? '—' }}</div>
                            </div>
                            <div class="col-md-4">
                                <label class="small text-muted fw-bold text-uppercase">Date d'Intervention</label>
                                <div class="fw-bold">
                                    {{ \Carbon\Carbon::parse($intervention->date_intervention)->format('d/m/Y H:i') }}</div>
                            </div>
                            <div class="col-md-4">
                                <label class="small text-muted fw-bold text-uppercase">Statut Final</label>
                                <div>
                                    <span class="badge bg-success rounded-pill px-3">APPAREIL RÉPARÉ</span>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="small text-muted fw-bold text-uppercase">Compte-rendu Technique</label>
                            <div class="bg-light p-3 rounded border" style="white-space: pre-wrap;">
                                {{ $intervention->rapport_technique ?? $intervention->compte_rendu }}</div>
                        </div>

                        @if($intervention->photo_intervention)
                            <div class="mb-4">
                                <label class="small text-muted fw-bold text-uppercase mb-2 d-block">Pièce Jointe / Photo</label>
                                <div class="rounded-3 overflow-hidden border" style="max-width: 400px;">
                                    <img src="{{ asset('storage/' . $intervention->photo_intervention) }}"
                                        alt="Photo Intervention" class="img-fluid">
                                </div>
                            </div>
                        @endif

                        @if($intervention->pieces->count() > 0)
                            <div class="mb-0">
                                <label class="small text-muted fw-bold text-uppercase mb-2 d-block">Pièces Détachées
                                    Utilisées</label>
                                <div class="table-responsive border rounded">
                                    <table class="table table-hover align-middle mb-0">
                                        <thead class="bg-light small text-uppercase fw-bold">
                                            <tr>
                                                <th class="ps-3">Pièce</th>
                                                <th class="text-center">Quantité</th>
                                                <th class="text-end pe-3">Statut Stock</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($intervention->pieces as $piece)
                                                <tr>
                                                    <td class="ps-3">
                                                        <div class="fw-bold text-dark">{{ $piece->nom }}</div>
                                                        <small class="text-muted font-monospace">{{ $piece->reference }}</small>
                                                    </td>
                                                    <td class="text-center fw-bold">{{ $piece->pivot->quantite }}</td>
                                                    <td class="text-end pe-3">
                                                        <span
                                                            class="badge bg-success bg-opacity-10 text-success rounded-pill px-3">Décompté</span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection