@extends('layouts.app')

@section('title', 'Paramètres société')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-1 text-gray-800">Paramètres société</h1>
    <p class="mb-4 text-muted">Configurez les informations utilisées dans les devis, factures et bons de réception.</p>

    <div class="card shadow mb-4 border-0">
        <div class="card-body p-4">
            <h5 class="font-weight-bold mb-4">Informations de l'entreprise</h5>
            
            <form action="{{ route('parametres-societe.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <div class="row g-3">
                    <div class="col-md-6 mb-3">
                        <label class="form-label small fw-bold">Nom société</label>
                        <input type="text" name="nom_societe" class="form-control" value="{{ old('nom_societe', $parametre->nom_societe) }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label small fw-bold">Téléphone</label>
                        <input type="text" name="telephone" class="form-control" value="{{ old('telephone', $parametre->telephone) }}">
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label small fw-bold">Email</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email', $parametre->email) }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label small fw-bold">Site web</label>
                        <input type="text" name="site_web" class="form-control" value="{{ old('site_web', $parametre->site_web) }}">
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label small fw-bold">Ville</label>
                        <input type="text" name="ville" class="form-control" value="{{ old('ville', $parametre->ville) }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label small fw-bold">Pays</label>
                        <input type="text" name="pays" class="form-control" value="{{ old('pays', $parametre->pays) }}">
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label small fw-bold">Numéro fiscal / RC</label>
                        <input type="text" name="numero_fiscal" class="form-control" value="{{ old('numero_fiscal', $parametre->numero_fiscal) }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label small fw-bold">Devise</label>
                        <input type="text" name="devise" class="form-control" value="{{ old('devise', $parametre->devise) }}">
                    </div>
                    
                    <div class="col-12 mb-3">
                        <label class="form-label small fw-bold">Adresse</label>
                        <textarea name="adresse" class="form-control" rows="3">{{ old('adresse', $parametre->adresse) }}</textarea>
                    </div>
                    
                    <div class="col-md-6 mb-4">
                        <label class="form-label small fw-bold">Logo</label>
                        <input type="file" name="logo" class="form-control">
                        @if($parametre->logo)
                            <div class="mt-2">
                                <img src="{{ asset('storage/' . $parametre->logo) }}" alt="Logo" style="height: 50px;">
                            </div>
                        @endif
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary px-4 py-2 fw-bold shadow-sm">Enregistrer</button>
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary px-4 py-2 fw-bold ms-2">Retour</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
