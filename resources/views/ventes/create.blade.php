@extends('layouts.app')

@section('title', 'Enregistrer une Vente')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Nouvelle Vente</h1>
            <small class="text-muted text-uppercase fw-bold">ENREGISTREMENT GARANTIE</small>
        </div>
        <a href="{{ route('ventes.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Retour
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow border-0" style="border-radius: 15px;">
                <div class="card-body p-4">
                    <form action="{{ route('ventes.store') }}" method="POST">
                        @csrf
                        
                        <h6 class="text-primary fw-bold mb-4 text-uppercase small"><i class="fas fa-mobile-alt me-2"></i> Détails Appareil</h6>
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">IMEI <span class="text-danger">*</span></label>
                                <input type="text" name="imei" class="form-control @error('imei') is-invalid @enderror" required value="{{ old('imei') }}">
                                @error('imei') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">DURÉE GARANTIE (MOIS) <span class="text-danger">*</span></label>
                                <input type="number" name="duree_garantie_mois" class="form-control" required value="{{ old('duree_garantie_mois', 12) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">MODÈLE <span class="text-danger">*</span></label>
                                <input type="text" name="modele" class="form-control" required value="{{ old('modele') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">RÉFÉRENCE</label>
                                <input type="text" name="reference_produit" class="form-control" value="{{ old('reference_produit') }}">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label small fw-bold">DATE DE VENTE <span class="text-danger">*</span></label>
                                <input type="date" name="date_vente" class="form-control" required value="{{ old('date_vente', date('Y-m-d')) }}">
                            </div>
                        </div>

                        <h6 class="text-primary fw-bold mb-4 text-uppercase small"><i class="fas fa-user me-2"></i> Information Client</h6>
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">NOM COMPLET <span class="text-danger">*</span></label>
                                <input type="text" name="client_nom" class="form-control" required value="{{ old('client_nom') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">EMAIL</label>
                                <input type="email" name="client_email" class="form-control" value="{{ old('client_email') }}">
                            </div>
                        </div>

                        <div class="d-grid mt-4">
                            <button type="submit" class="btn btn-primary btn-lg shadow-sm fw-bold">
                                <i class="fas fa-save me-2"></i> ENREGISTRER LA VENTE
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
