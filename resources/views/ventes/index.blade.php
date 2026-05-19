@extends('layouts.app')

@section('title', 'Registre des Ventes')



@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 ventes-title mb-0">Registre des Ventes</h1>
            <small class="text-muted fw-bold">Historique des ventes pour vérification de garantie</small>
        </div>
        <div>
            <button type="button" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm text-uppercase btn-import-excel" data-bs-toggle="modal" data-bs-target="#importExcelModal">
                <i class="fas fa-file-import me-2"></i> Importer Excel
            </button>
        </div>
    </div>


    @if($errors->any())
    <div class="alert alert-danger border-0 shadow-sm mb-4 px-4 py-3 alert-ventes-danger">
        <div class="d-flex align-items-center mb-2">
            <i class="fas fa-times-circle me-3 fs-4 text-danger"></i>
            <div class="fw-bold">Erreur de validation :</div>
        </div>
        <ul class="mb-0 ps-4 small">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="card border-0 shadow-sm mb-4 card-ventes-filter">
        <div class="card-body p-3">
            <form action="{{ route('ventes.index') }}" method="GET" class="row g-2 align-items-center">
                <div class="col-md-8">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-0"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" name="search" class="form-control bg-light border-0" placeholder="Rechercher par IMEI ou Nom Client..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100 rounded-3 fw-bold shadow-sm">FILTRER</button>
                </div>
                <div class="col-md-2">
                    <a href="{{ route('ventes.index') }}" class="btn btn-light border-0 w-100 rounded-3 fw-bold btn-raz-ventes">RAZ</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card ventes-card overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 table-ventes">
                <thead>
                    <tr>
                        <th class="ps-4">IMEI</th>
                        <th>Type</th>
                        <th>Modèle / Article</th>
                        <th>N° Facture</th>
                        <th>Client</th>
                        <th>Date Vente</th>
                        <th>Garantie</th>
                        <th>Fin Garantie</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($ventes as $vente)
                    <tr>
                        <td class="ps-4 imei-cell">{{ $vente->imei }}</td>
                        <td>
                            <span class="badge rounded-pill {{ $vente->badge_class }} vente-type-badge">{{ $vente->type ?? 'MATÉRIEL' }}</span>
                        </td>
                        <td>
                            <div class="modele-text">{{ $vente->modele }}</div>
                            <small class="text-muted fw-bold">{{ $vente->reference_produit ?? '—' }}</small>
                        </td>
                        <td class="small fw-bold text-secondary">{{ $vente->numero_facture_vente ?? '—' }}</td>
                        <td>
                            <div class="client-name">{{ $vente->client_nom }}</div>
                            <div class="client-email">{{ $vente->client_email }}</div>
                        </td>
                        <td class="small fw-bold text-muted">{{ $vente->date_vente->format('d/m/Y') }}</td>
                        <td class="small fw-bold text-muted">{{ $vente->duree_garantie_mois }} mois</td>
                        <td class="small fw-bold text-muted">
                            {{ $vente->fin_garantie_formatted }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted fw-bold italic">Aucune vente enregistrée.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($ventes->hasPages())
        <div class="p-3 bg-white border-top">
            {{ $ventes->links() }}
        </div>
        @endif
    </div>
</div>

@include('ventes.partials.import_modal')
@endsection
