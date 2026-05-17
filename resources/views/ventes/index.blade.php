@extends('layouts.app')

@section('title', 'Registre des Ventes')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/ventes.css') }}">
@endpush

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 ventes-title mb-0">Registre des Ventes</h1>
            <small class="text-muted fw-bold">Historique des ventes pour vérification de garantie</small>
        </div>
        <div></div>
    </div>

    <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px;">
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
                    <a href="{{ route('ventes.index') }}" class="btn btn-light border-0 w-100 rounded-3 fw-bold" style="background: #f1f5f9; color: #64748b;">RAZ</a>
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
@endsection
