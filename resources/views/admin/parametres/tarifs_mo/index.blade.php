@extends('layouts.app')

@section('title', 'Tarifs Main d\'œuvre')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/tarifs_mo.css') }}">
@endpush

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800 fw-bold">Tarifs Main d'œuvre</h1>
            <small class="text-muted">Configuration des coûts de main d'œuvre par type d'intervention</small>
        </div>
        <button type="button" class="btn btn-primary shadow-sm px-4" data-bs-toggle="modal" data-bs-target="#addTarifModal">
            <i class="fas fa-plus me-1"></i> NOUVEAU TARIF
        </button>
    </div>

    {{-- Filtre de recherche --}}
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px;">
        <div class="card-body p-3">
            <form action="{{ route('admin.tarifs_mo.index') }}" method="GET" class="row g-2 align-items-center">
                <div class="col-md-8">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-0"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" name="search" class="form-control bg-light border-0" placeholder="Rechercher par type d'intervention..." value="{{ $search ?? '' }}">
                    </div>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100 rounded-3 fw-bold shadow-sm">FILTRER</button>
                </div>
                <div class="col-md-2">
                    <a href="{{ route('admin.tarifs_mo.index') }}" class="btn btn-light border-0 w-100 rounded-3 fw-bold" style="background: #f1f5f9; color: #64748b;">RAZ</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm border-0" style="border-radius: 15px;">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr class="small text-muted text-uppercase">
                        <th class="ps-4">Type d'intervention</th>
                        <th>Montant</th>
                        <th class="text-center">Statut</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($tarifs as $tarif)
                    <tr>
                        <td class="ps-4 fw-bold">{{ $tarif->type_intervention }}</td>
                        <td class="fw-bold text-primary">{{ number_format($tarif->montant, 3, ',', ' ') }} DT</td>
                        <td class="text-center">
                            @if($tarif->actif)
                                <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-1 small fw-bold">
                                    <i class="fas fa-check-circle me-1"></i> ACTIF
                                </span>
                            @else
                                <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill px-3 py-1 small fw-bold">
                                    <i class="fas fa-times-circle me-1"></i> INACTIF
                                </span>
                            @endif
                        </td>
                        <td class="text-end pe-4">
                            <div class="d-flex justify-content-end align-items-center gap-3">


                                <button class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-bold" data-bs-toggle="modal" data-bs-target="#editModal{{ $tarif->id }}">
                                    <i class="fas fa-edit me-1"></i> Modifier
                                </button>
                            </div>

                            <!-- Modal Edit -->
                            <div class="modal fade text-start" id="editModal{{ $tarif->id }}" tabindex="-1">
                                <div class="modal-dialog modal-dialog-centered">
                                    <form action="{{ route('admin.tarifs_mo.update', $tarif->id) }}" method="POST" class="modal-content border-0 shadow" style="border-radius: 15px;">
                                        @csrf @method('PUT')
                                        <div class="modal-header border-0 pb-0">
                                            <h5 class="fw-bold">Modifier le Tarif</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body p-4">
                                            <div class="mb-3">
                                                <label class="form-label small fw-bold text-muted text-uppercase">Type d'intervention</label>
                                                <input type="text" name="type_intervention" class="form-control bg-light border-0" value="{{ $tarif->type_intervention }}" required>
                                            </div>
                                            <div class="mb-0">
                                                <label class="form-label small fw-bold text-muted text-uppercase">Montant (DT)</label>
                                                <input type="number" step="0.001" name="montant" class="form-control bg-light border-0" value="{{ $tarif->montant }}" required>
                                            </div>
                                        </div>
                                        <div class="modal-footer border-0 pt-0">
                                            <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Annuler</button>
                                            <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">METTRE À JOUR</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Ajout (Simplifié pour l'exemple) -->
<div class="modal fade" id="addTarifModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('admin.tarifs_mo.store') }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header"><h5>Nouveau Tarif MO</h5></div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Type d'intervention</label>
                    <input type="text" name="type_intervention" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Montant (DT)</label>
                    <input type="number" step="0.001" name="montant" class="form-control" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                <button type="submit" class="btn btn-primary">Enregistrer</button>
            </div>
        </form>
    </div>
</div>
@endsection
