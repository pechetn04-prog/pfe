@extends('layouts.app')

@section('title', 'Tarifs Main d\'œuvre')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/tarifs_mo.css') }}">
@endpush

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800 fw-bold">Tarifs Main d'œuvre</h1>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addTarifModal">
            <i class="fas fa-plus me-1"></i> Nouveau Tarif
        </button>
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
                            <form action="{{ route('admin.tarifs_mo.toggle', $tarif->id) }}" method="POST">
                                @csrf @method('PATCH')
                                <button type="submit" class="btn btn-sm {{ $tarif->actif ? 'btn-success' : 'btn-secondary' }} rounded-pill px-3">
                                    {{ $tarif->actif ? 'Actif' : 'Inactif' }}
                                </button>
                            </form>
                        </td>
                        <td class="text-end pe-4">
                            <button class="btn btn-link text-primary p-1" data-bs-toggle="modal" data-bs-target="#editModal{{ $tarif->id }}"><i class="fas fa-edit"></i></button>
                            <form action="{{ route('admin.tarifs_mo.destroy', $tarif->id) }}" method="POST" class="d-inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-link text-danger p-1" onclick="return confirm('Supprimer ce tarif ?')"><i class="fas fa-trash"></i></button>
                            </form>
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
