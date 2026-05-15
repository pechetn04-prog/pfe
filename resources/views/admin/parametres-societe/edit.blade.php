@extends('layouts.app')

@section('title', 'Paramètres Société')

@section('content')
<div class="container-fluid" style="max-width: 900px;">

    <div class="d-flex align-items-center mb-4">
        <div>
            <h1 class="h3 fw-bold mb-0">Paramètres Société</h1>
            <small class="text-muted">Configurez les informations utilisées dans les devis, factures et bons de réception.</small>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger border-0 shadow-sm">
            <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <form action="{{ route('parametres-societe.update') }}" method="POST" enctype="multipart/form-data">
        @csrf @method('PUT')

        {{-- Informations principales --}}
        <div class="card border-0 shadow-sm mb-4" style="border-radius: 14px;">
            <div class="card-header bg-white border-0 py-3 px-4">
                <h6 class="fw-bold mb-0"><i class="fas fa-building me-2 text-primary"></i>Informations de l'entreprise</h6>
            </div>
            <div class="card-body px-4 pb-4">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Nom de la société <span class="text-danger">*</span></label>
                        <input type="text" name="nom_societe" class="form-control"
                            value="{{ old('nom_societe', $parametre->nom_societe) }}"
                            placeholder="Ex: Maison Tel SARL">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Téléphone</label>
                        <input type="text" name="telephone" class="form-control"
                            value="{{ old('telephone', $parametre->telephone) }}"
                            placeholder="Ex: +213 XX XX XX XX">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Email</label>
                        <input type="email" name="email" class="form-control"
                            value="{{ old('email', $parametre->email) }}"
                            placeholder="contact@maisontel.dz">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Site web</label>
                        <input type="text" name="site_web" class="form-control"
                            value="{{ old('site_web', $parametre->site_web) }}"
                            placeholder="www.maisontel.dz">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Ville</label>
                        <input type="text" name="ville" class="form-control"
                            value="{{ old('ville', $parametre->ville) }}"
                            placeholder="Ex: Alger">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Pays</label>
                        <input type="text" name="pays" class="form-control"
                            value="{{ old('pays', $parametre->pays ?? 'Algérie') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Numéro fiscal / RC</label>
                        <input type="text" name="numero_fiscal" class="form-control"
                            value="{{ old('numero_fiscal', $parametre->numero_fiscal) }}"
                            placeholder="Ex: RC 123456">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Devise</label>
                        <select name="devise" class="form-select">
                            <option value="TND" {{ ($parametre->devise ?? 'TND') === 'TND' ? 'selected' : '' }}>TND (Dinar Tunisien)</option>
                            <option value="DZD" {{ ($parametre->devise ?? '') === 'DZD' ? 'selected' : '' }}>DZD (Dinar Algérien)</option>
                            <option value="MAD" {{ ($parametre->devise ?? '') === 'MAD' ? 'selected' : '' }}>MAD (Dirham Marocain)</option>
                            <option value="EUR" {{ ($parametre->devise ?? '') === 'EUR' ? 'selected' : '' }}>EUR (Euro)</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Adresse complète</label>
                        <textarea name="adresse" class="form-control" rows="3"
                            placeholder="Ex: 123 Rue de la Paix, Zone Industrielle, Alger">{{ old('adresse', $parametre->adresse) }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        {{-- Logo --}}
        <div class="card border-0 shadow-sm mb-4" style="border-radius: 14px;">
            <div class="card-header bg-white border-0 py-3 px-4">
                <h6 class="fw-bold mb-0"><i class="fas fa-image me-2 text-primary"></i>Logo de la société</h6>
            </div>
            <div class="card-body px-4 pb-4">
                <div class="d-flex align-items-center gap-4 flex-wrap">
                    @if($parametre->logo)
                        <div class="border rounded-3 p-2" style="background: #f8fafc;">
                            <img src="{{ Storage::url($parametre->logo) }}" alt="Logo actuel"
                                style="max-height: 80px; max-width: 200px; object-fit: contain;">
                        </div>
                    @else
                        <div class="border rounded-3 p-4 text-muted text-center" style="min-width: 160px; background: #f8fafc;">
                            <i class="fas fa-image fa-2x mb-2 d-block"></i>
                            <small>Aucun logo</small>
                        </div>
                    @endif
                    <div class="flex-grow-1">
                        <label class="form-label fw-semibold">{{ $parametre->logo ? 'Remplacer le logo' : 'Ajouter un logo' }}</label>
                        <input type="file" name="logo" class="form-control" accept="image/png,image/jpeg,image/svg+xml">
                        <small class="text-muted">Formats acceptés : PNG, JPG, SVG. Taille max : 2 Mo.</small>
                    </div>
                </div>
            </div>
        </div>

        {{-- Actions --}}
        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary px-5 py-2">
                <i class="fas fa-save me-2"></i> Enregistrer les paramètres
            </button>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary px-4 py-2">Retour</a>
        </div>
    </form>
</div>
@endsection
