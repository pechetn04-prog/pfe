@extends('layouts.app')

@section('title', 'Enregistrer une Vente')

@section('content')
<div class="container-fluid" style="max-width: 800px;">
    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('ventes.index') }}" class="btn btn-light rounded-pill me-3 shadow-sm border">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h1 class="h3 fw-bold mb-0" style="font-weight: 800;">Enregistrer une Vente</h1>
    </div>

    @if($errors->any())
        <div class="alert alert-danger border-0 shadow-sm" style="border-radius: 12px;">
            <ul class="mb-0 fw-bold">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('ventes.store') }}" method="POST">
        @csrf
        <div class="card border-0 shadow-sm" style="border-radius: 15px;">
            <div class="card-body p-4">
                <div class="row g-4">
                    <div class="col-md-12">
                        <label class="form-label small fw-bolder text-uppercase" style="color: #64748b; font-weight: 800;">IMEI de l'appareil</label>
                        <input type="text" name="imei" class="form-control form-control-lg bg-light border-0 fw-bold" 
                               placeholder="Saisissez le numéro IMEI..." required value="{{ old('imei') }}" style="border-radius: 10px;">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label small fw-bolder text-uppercase" style="color: #64748b; font-weight: 800;">Modèle / Article</label>
                        <input type="text" name="modele" class="form-control form-control-lg bg-light border-0 fw-bold" 
                               placeholder="Ex: iPhone 13 Pro" required value="{{ old('modele') }}" style="border-radius: 10px;">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label small fw-bolder text-uppercase" style="color: #64748b; font-weight: 800;">Référence Produit</label>
                        <input type="text" name="reference_produit" class="form-control form-control-lg bg-light border-0 fw-bold" 
                               placeholder="Ex: MLP13-256-BLK" value="{{ old('reference_produit') }}" style="border-radius: 10px;">
                    </div>

                    <div class="col-md-12">
                        <hr class="my-2 opacity-50">
                        <h6 class="fw-bold mb-3" style="color: #1a2332;">Informations Client & Garantie</h6>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label small fw-bolder text-uppercase" style="color: #64748b; font-weight: 800;">Nom du Client</label>
                        <input type="text" name="client_nom" class="form-control form-control-lg bg-light border-0 fw-bold" 
                               placeholder="Nom complet du client" required value="{{ old('client_nom') }}" style="border-radius: 10px;">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label small fw-bolder text-uppercase" style="color: #64748b; font-weight: 800;">Email du Client</label>
                        <input type="email" name="client_email" class="form-control form-control-lg bg-light border-0 fw-bold" 
                               placeholder="client@exemple.com" value="{{ old('client_email') }}" style="border-radius: 10px;">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label small fw-bolder text-uppercase" style="color: #64748b; font-weight: 800;">Date de Vente</label>
                        <input type="date" name="date_vente" class="form-control form-control-lg bg-light border-0 fw-bold" 
                               required value="{{ old('date_vente', date('Y-m-d')) }}" style="border-radius: 10px;">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label small fw-bolder text-uppercase" style="color: #64748b; font-weight: 800;">Durée Garantie (Mois)</label>
                        <input type="number" name="duree_garantie_mois" class="form-control form-control-lg bg-light border-0 fw-bold" 
                               required value="{{ old('duree_garantie_mois', 12) }}" min="0" style="border-radius: 10px;">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label small fw-bolder text-uppercase" style="color: #64748b; font-weight: 800;">N° Facture Vente</label>
                        <input type="text" name="numero_facture_vente" class="form-control form-control-lg bg-light border-0 fw-bold" 
                               placeholder="Ex: FAC-00123" value="{{ old('numero_facture_vente') }}" style="border-radius: 10px;">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label small fw-bolder text-uppercase" style="color: #64748b; font-weight: 800;">Type</label>
                        <select name="type" class="form-select form-select-lg bg-light border-0 fw-bold" style="border-radius: 10px;">
                            <option value="MATÉRIEL">MATÉRIEL</option>
                            <option value="ACCESSOIRE">ACCESSOIRE</option>
                        </select>
                    </div>

                    <div class="col-12 mt-4 pt-2">
                        <button type="submit" class="btn btn-primary btn-lg w-100 rounded-pill fw-bold shadow-sm py-3" style="font-weight: 800;">
                            <i class="fas fa-save me-2"></i> ENREGISTRER LA VENTE
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
