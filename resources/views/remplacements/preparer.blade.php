@extends('layouts.app')

@section('title', 'Préparer le Remplacement — #' . $dossier->num_dossier)

@section('content')
<div class="container-fluid px-4 py-4">
    {{-- Header --}}
    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('dossiers.show', $dossier->id) }}" class="btn btn-sm bg-white shadow-sm me-3 border-0 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; border-radius: 10px; color: #1e69ff;">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="h4 fw-bold mb-0" style="color: #1a2332; letter-spacing: -0.5px;">Préparation du Remplacement</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0" style="font-size: 0.75rem; font-weight: 500;">
                    <li class="breadcrumb-item"><a href="{{ route('dossiers.index') }}" class="text-primary text-decoration-none">Dossiers</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('dossiers.show', $dossier->id) }}" class="text-primary text-decoration-none">#{{ $dossier->num_dossier }}</a></li>
                    <li class="breadcrumb-item active text-muted" aria-current="page">Remplacement</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-6">
            {{-- Info Card --}}
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 20px; background: linear-gradient(135deg, #1e69ff 0%, #0046d5 100%); color: white;">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-white bg-opacity-20 p-2 rounded-3 me-3">
                            <i class="fas fa-info-circle fs-4"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-1">Remplacement Validé</h6>
                            <p class="mb-0 small opacity-75">L'administration a autorisé l'échange à neuf pour ce dossier.</p>
                        </div>
                    </div>
                    <div class="bg-white bg-opacity-10 p-3 rounded-3 border border-white border-opacity-10">
                        <div class="row g-2">
                            <div class="col-6">
                                <label class="small opacity-50 text-uppercase fw-bold" style="font-size: 0.6rem;">Ancien IMEI</label>
                                <div class="fw-bold">{{ $dossier->appareil->imei }}</div>
                            </div>
                            <div class="col-6">
                                <label class="small opacity-50 text-uppercase fw-bold" style="font-size: 0.6rem;">Modèle Initial</label>
                                <div class="fw-bold">{{ $dossier->appareil->modele }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Form Card --}}
            <div class="card border-0 shadow-sm overflow-hidden" style="border-radius: 20px;">
                <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                    <h5 class="fw-bold text-dark mb-0"><i class="fas fa-box-open text-primary me-2"></i> Nouvel Appareil</h5>
                    <p class="text-muted small mb-0">Saisissez les informations de l'unité remise au client.</p>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('dossiers.storeRemplacement', $dossier->id) }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label class="form-label small fw-bold text-uppercase text-muted" style="letter-spacing: 0.5px;">IMEI du nouvel appareil <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-0" style="border-radius: 12px 0 0 12px;"><i class="fas fa-barcode text-muted"></i></span>
                                <input type="text" name="imei_remplacement" class="form-control bg-light border-0 p-3" required
                                    placeholder="Ex: 356938035643809"
                                    value="{{ old('imei_remplacement') }}"
                                    style="border-radius: 0 12px 12px 0; font-weight: 600;">
                            </div>
                            @error('imei_remplacement')
                                <small class="text-danger mt-1 d-block">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label small fw-bold text-uppercase text-muted" style="letter-spacing: 0.5px;">Modèle (si différent)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-0" style="border-radius: 12px 0 0 12px;"><i class="fas fa-mobile-alt text-muted"></i></span>
                                <input type="text" name="modele_remplacement" class="form-control bg-light border-0 p-3"
                                    placeholder="Ex: Samsung Galaxy A55"
                                    value="{{ old('modele_remplacement', $dossier->appareil->modele) }}"
                                    style="border-radius: 0 12px 12px 0; font-weight: 600;">
                            </div>
                        </div>

                        <div class="d-flex align-items-center gap-2 mt-5">
                            <button type="submit" class="btn btn-primary btn-lg flex-grow-1 rounded-pill fw-bold shadow-sm py-3" style="font-size: 0.9rem;">
                                <i class="fas fa-check-circle me-2"></i> CONFIRMER LE REMPLACEMENT
                            </button>
                            <a href="{{ route('dossiers.show', $dossier->id) }}" class="btn btn-light btn-lg px-4 rounded-pill fw-bold text-muted" style="font-size: 0.9rem;">Annuler</a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="text-center mt-4">
                <p class="text-muted small italic"><i class="fas fa-shield-alt me-1"></i> Cette action clôturera la phase technique et préparera le dossier pour la livraison.</p>
            </div>
        </div>
    </div>
</div>

<style>
    .form-control:focus {
        background-color: #fff !important;
        box-shadow: 0 0 0 4px rgba(30, 105, 255, 0.1) !important;
        border: 1px solid #1e69ff !important;
    }
    .breadcrumb-item + .breadcrumb-item::before { content: "/"; color: #cbd5e1; }
    .italic { font-style: italic; }
</style>
@endsection
