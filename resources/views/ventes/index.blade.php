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
        <div>
            <button type="button" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm text-uppercase" style="font-size: 0.8rem; letter-spacing: 0.5px;" data-bs-toggle="modal" data-bs-target="#importExcelModal">
                <i class="fas fa-file-import me-2"></i> Importer Excel
            </button>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success border-0 shadow-sm mb-4 px-4 py-3 d-flex align-items-center" style="border-radius: 12px; background-color: #ecfdf5; color: #065f46;">
        <i class="fas fa-check-circle me-3 fs-4 text-success"></i>
        <div class="fw-bold">{{ session('success') }}</div>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger border-0 shadow-sm mb-4 px-4 py-3 d-flex align-items-center" style="border-radius: 12px; background-color: #fef2f2; color: #991b1b;">
        <i class="fas fa-exclamation-circle me-3 fs-4 text-danger"></i>
        <div class="fw-bold">{{ session('error') }}</div>
    </div>
    @endif

    @if($errors->any())
    <div class="alert alert-danger border-0 shadow-sm mb-4 px-4 py-3" style="border-radius: 12px; background-color: #fef2f2; color: #991b1b;">
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

{{-- Modal Import Excel --}}
<div class="modal fade" id="importExcelModal" tabindex="-1" aria-labelledby="importExcelModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-dark" id="importExcelModalLabel">📊 Importer des ventes (Excel)</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('ventes.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body py-4">
                    <div class="mb-4">
                        <label class="form-label fw-bold text-muted small text-uppercase">Fichier Excel (.xlsx, .xls) ou CSV</label>
                        <div class="input-group">
                            <input type="file" name="excel_file" class="form-control" accept=".xlsx,.xls,.csv,.txt" required>
                        </div>
                        <small class="text-muted mt-2 d-block">
                            La taille maximale du fichier est de 4 Mo.
                        </small>
                    </div>

                    <div class="bg-light p-3 rounded-4 border">
                        <label class="fw-bold small text-primary mb-2 d-block"><i class="fas fa-info-circle me-1"></i> Structure attendue des colonnes :</label>
                        <div class="bg-white p-2 rounded-3 border mb-2 text-center" style="font-family: monospace; font-size: 0.7rem; overflow-x: auto; white-space: nowrap; border-style: dashed !important;">
                            type | imei | modele | client_nom | date_vente | duree_garantie_mois | reference_produit | numero_facture_vente
                        </div>
                        <div class="text-muted" style="font-size: 0.7rem; line-height: 1.4;">
                            * **Ligne d'en-tête obligatoire** : La première ligne doit contenir les noms exacts des colonnes (ex: `imei`, `modele`, `date_vente`, etc.)<br>
                            * **Format de date** : Format standard de date Excel ou `AAAA-MM-JJ`<br>
                            * **Unicité** : L'IMEI est la clé d'identification. Si l'IMEI existe déjà, sa fiche de vente sera mise à jour avec les nouvelles valeurs.
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0 d-flex gap-2">
                    <button type="submit" class="btn btn-primary px-4 rounded-pill fw-bold shadow-sm text-uppercase" style="font-size: 0.8rem;">
                        <i class="fas fa-upload me-2"></i> Importer Excel
                    </button>
                    <button type="button" class="btn btn-light px-3 rounded-pill fw-bold" data-bs-dismiss="modal">Annuler</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
