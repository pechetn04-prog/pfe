@extends('layouts.app')

@section('title', 'Registre des Ventes')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 fw-bold mb-0" style="color: #1a2332; font-weight: 800;">Registre des Ventes</h1>
            <small class="text-muted fw-bold">Historique des ventes pour vérification de garantie</small>
        </div>
        <a href="{{ route('ventes.create') }}" class="btn btn-primary rounded-pill px-4 shadow-sm fw-bold" style="font-weight: 800;">
            <i class="fas fa-plus me-2"></i> Nouvelle Vente
        </a>
    </div>

    <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px;">
        <div class="card-body p-3">
            <form action="{{ route('ventes.index') }}" method="GET" class="row g-2">
                <div class="col-md-10">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-0 text-muted ps-3"><i class="fas fa-search"></i></span>
                        <input type="text" name="search" class="form-control bg-light border-0 ps-2" placeholder="Rechercher par IMEI ou Nom Client..." value="{{ request('search') }}" style="border-radius: 0 10px 10px 0;">
                    </div>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-dark w-100 rounded-pill fw-bold shadow-sm">Filtrer</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm overflow-hidden" style="border-radius: 15px;">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr class="small text-uppercase fw-bold" style="letter-spacing: 0.5px; color: #64748b;">
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
                <tbody class="bg-white">
                    @forelse($ventes as $vente)
                    @php
                        $finGarantie = \Carbon\Carbon::parse($vente->date_vente)->addMonths($vente->duree_garantie_mois);
                        $sousGarantie = now()->lessThanOrEqualTo($finGarantie);
                    @endphp
                    <tr>
                        <td class="ps-4 fw-bolder text-primary" style="font-weight: 800; font-family: 'Outfit', sans-serif;">{{ $vente->imei }}</td>
                        <td>
                            @php
                                $badgeColor = $vente->type === 'REMPLACEMENT' ? 'bg-soft-info text-info border-info' : 'bg-light text-dark';
                            @endphp
                            <span class="badge rounded-pill {{ $badgeColor }} border px-2 py-1 fw-bold" style="font-size: 0.65rem;">{{ $vente->type ?? 'MATÉRIEL' }}</span>
                        </td>
                        <td>
                            <div class="fw-bolder text-dark" style="font-weight: 800;">{{ $vente->modele }}</div>
                            <small class="text-muted fw-bold">{{ $vente->reference_produit ?? '—' }}</small>
                        </td>
                        <td class="small fw-bolder text-secondary" style="font-weight: 800;">{{ $vente->numero_facture_vente ?? '—' }}</td>
                        <td>
                            <div class="small fw-bolder text-dark" style="font-weight: 800;">{{ $vente->client_nom }}</div>
                            <div class="text-muted fw-bold" style="font-size: 0.75rem;">{{ $vente->client_email }}</div>
                        </td>
                        <td class="small fw-bold">{{ \Carbon\Carbon::parse($vente->date_vente)->format('d/m/Y') }}</td>
                        <td class="small fw-bold">{{ $vente->duree_garantie_mois }} mois</td>
                        <td class="small fw-bold">
                            {{ $finGarantie->format('d/m/Y') }}
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
