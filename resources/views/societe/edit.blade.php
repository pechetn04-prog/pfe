@extends('layouts.app')

@section('title', 'Paramètres société')

@section('content')
<div class="container-fluid px-5 py-4">

    <div class="mb-5">
        <h1 class="display-6 fw-bold mb-1 text-premium-navy">Paramètres société</h1>
        <p class="text-muted">Configurez les informations utilisées dans les devis, factures et bons de réception.</p>
    </div>
    <form action="{{ route('parametres-societe.update') }}" method="POST" enctype="multipart/form-data">
        @csrf @method('PUT')

        <div class="card border-0 shadow-sm mb-4 card-societe-box">
            <div class="card-body p-5">
                <h5 class="fw-bold mb-4 text-premium-navy">Informations de l'entreprise</h5>
                
                <div class="row g-4 mb-4">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-dark">Nom société</label>
                        <input type="text" name="nom_societe" class="form-control form-control-lg border-light-subtle bg-light-subtle input-societe-form" 
                               value="{{ old('nom_societe', $parametre->nom_societe) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-dark">Téléphone</label>
                        <input type="text" name="telephone" class="form-control form-control-lg border-light-subtle bg-light-subtle input-societe-form" 
                               value="{{ old('telephone', $parametre->telephone) }}">
                    </div>
                </div>

                <div class="row g-4 mb-4">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-dark">Email</label>
                        <input type="email" name="email" class="form-control form-control-lg border-light-subtle bg-light-subtle input-societe-form" 
                               value="{{ old('email', $parametre->email) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-dark">Site web</label>
                        <input type="text" name="site_web" class="form-control form-control-lg border-light-subtle bg-light-subtle input-societe-form" 
                               value="{{ old('site_web', $parametre->site_web) }}">
                    </div>
                </div>

                <div class="row g-4 mb-4">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-dark">Ville</label>
                        <input type="text" name="ville" class="form-control form-control-lg border-light-subtle bg-light-subtle input-societe-form" 
                               value="{{ old('ville', $parametre->ville) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-dark">Pays</label>
                        <input type="text" name="pays" class="form-control form-control-lg border-light-subtle bg-light-subtle input-societe-form" 
                               value="{{ old('pays', $parametre->pays) }}">
                    </div>
                </div>

                <div class="row g-4 mb-4">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-dark">Matricule Fiscal</label>
                        <input type="text" name="numero_fiscal" class="form-control form-control-lg border-light-subtle bg-light-subtle input-societe-form" 
                               value="{{ old('numero_fiscal', $parametre->numero_fiscal) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-dark">Devise</label>
                        <input type="text" name="devise" class="form-control form-control-lg border-light-subtle bg-light-subtle input-societe-form" 
                               value="{{ old('devise', $parametre->devise) }}">
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label small fw-bold text-dark">Adresse</label>
                    <textarea name="adresse" class="form-control border-light-subtle bg-light-subtle input-societe-form" rows="4">{{ old('adresse', $parametre->adresse) }}</textarea>
                </div>

                <div class="mb-5">
                    <label class="form-label small fw-bold text-dark">Logo</label>
                    <div class="d-flex align-items-center gap-3">
                        <input type="file" name="logo" class="form-control form-control-lg border-light-subtle bg-light-subtle input-societe-form">
                        @if($parametre->logo)
                            <div class="ms-2">
                                <img src="{{ Storage::url($parametre->logo) }}" alt="Logo" class="rounded border img-logo-preview">
                            </div>
                        @endif
                    </div>
                </div>

                <div class="d-flex align-items-center gap-4">
                    <button type="submit" class="btn btn-primary px-4 py-2 fw-bold shadow-sm btn-save-societe">
                        Enregistrer
                    </button>
                    <a href="{{ route('admin.dashboard') }}" class="text-muted text-decoration-none fw-bold small">Retour</a>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
