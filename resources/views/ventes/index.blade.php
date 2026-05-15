@extends('layouts.app')

@section('title', 'Registre des Ventes')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Registre des Ventes</h1>
            <small class="text-muted">Historique des ventes pour vérification de garantie</small>
        </div>
        <a href="{{ route('ventes.create') }}" class="btn btn-primary shadow-sm">
            <i class="fas fa-plus me-1"></i> Nouvelle Vente
        </a>
    </div>

    <div class="card shadow border-0 mb-4" style="border-radius: 15px;">
        <div class="card-body">
            <form action="{{ route('ventes.index') }}" method="GET" class="row g-3">
                <div class="col-md-10">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0 text-muted"><i class="fas fa-search"></i></span>
                        <input type="text" name="search" class="form-control border-start-0 ps-0" placeholder="Rechercher par IMEI ou Nom Client..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-dark w-100 shadow-sm">Filtrer</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow border-0 overflow-hidden" style="border-radius: 15px;">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr class="small text-muted text-uppercase">
                        <th class="ps-4">IMEI</th>
                        <th>Type</th>
                        <th>Modèle / Article</th>
                        <th>N° Facture</th>
                        <th>Client</th>
                        <th>Date Vente</th>
                        <th>Garantie</th>
                        <th>Fin Garantie</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($ventes as $vente)
                    @php
                        $finGarantie = \Carbon\Carbon::parse($vente->date_vente)->addMonths($vente->duree_garantie_mois);
                        $sousGarantie = now()->lessThanOrEqualTo($finGarantie);
                    @endphp
                    <tr>
                        <td class="ps-4 fw-bold text-primary font-monospace">{{ $vente->imei }}</td>
                        <td>
                            <span class="badge bg-light text-dark border px-2 py-1" style="font-size: 0.65rem;">{{ $vente->type ?? 'MATÉRIEL' }}</span>
                        </td>
                        <td>
                            <div class="fw-bold">{{ $vente->modele }}</div>
                            <small class="text-muted">{{ $vente->reference_produit ?? '—' }}</small>
                        </td>
                        <td class="small fw-bold text-secondary">{{ $vente->numero_facture_vente ?? '—' }}</td>
                        <td>
                            <div class="small fw-bold text-dark">{{ $vente->client_nom }}</div>
                            <div class="text-muted" style="font-size: 0.75rem;">{{ $vente->client_email }}</div>
                        </td>
                        <td class="small">{{ \Carbon\Carbon::parse($vente->date_vente)->format('d/m/Y') }}</td>
                        <td class="small">{{ $vente->duree_garantie_mois }} mois</td>
                        <td>
                            <span class="badge {{ $sousGarantie ? 'bg-success bg-opacity-10 text-success' : 'bg-danger bg-opacity-10 text-danger' }} px-3 rounded-pill" style="font-size: 0.7rem;">
                                <i class="fas {{ $sousGarantie ? 'fa-check' : 'fa-times' }} me-1"></i>
                                {{ $finGarantie->format('d/m/Y') }}
                            </span>
                        </td>
                        <td class="text-end pe-4">
                            <a href="{{ route('dossiers.create', ['imei' => $vente->imei]) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-bold">
                                <i class="fas fa-plus-circle me-1"></i> Ticket
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">Aucune vente enregistrée.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($ventes->hasPages())
        <div class="card-footer bg-white border-0 py-3">
            {{ $ventes->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
