@extends('layouts.app')

@section('title', 'Préparer le Remplacement — #{{ $dossier->num_dossier }}')

@section('content')
<div class="container" style="max-width: 700px;">
    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('dossiers.show', $dossier->id) }}" class="btn btn-outline-secondary btn-sm me-3">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="h4 fw-bold mb-0">Préparer le Remplacement</h1>
            <small class="text-muted">Dossier #{{ $dossier->num_dossier }} — Appareil irréparable sous garantie</small>
        </div>
    </div>

    <div class="alert alert-info">
        <i class="fas fa-info-circle me-2"></i>
        Le remplacement a été validé par l'administrateur. Enregistrez l'IMEI du nouvel appareil remis au client.
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <form action="{{ route('dossiers.storeRemplacement', $dossier->id) }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-semibold">IMEI de l'appareil de remplacement <span class="text-danger">*</span></label>
                    <input type="text" name="imei_remplacement" class="form-control" required
                        placeholder="Ex: 356938035643809"
                        value="{{ old('imei_remplacement') }}">
                </div>
                <div class="mb-4">
                    <label class="form-label fw-semibold">Modèle de l'appareil de remplacement</label>
                    <input type="text" name="modele_remplacement" class="form-control"
                        placeholder="Ex: Samsung Galaxy A55"
                        value="{{ old('modele_remplacement') }}">
                </div>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-success px-4">
                        <i class="fas fa-check me-2"></i> Confirmer le remplacement
                    </button>
                    <a href="{{ route('dossiers.show', $dossier->id) }}" class="btn btn-outline-secondary px-4">Annuler</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
