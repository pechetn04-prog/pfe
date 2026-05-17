@extends('layouts.app')

@section('title', 'Détails de la Facture')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/facture-premium.css') }}">
@endpush

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Facture #{{ $facture->numero }}</h1>
            <small class="text-muted">Émise le {{ \Carbon\Carbon::parse($facture->date_facture)->format('d/m/Y') }}</small>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('facture.pdf', $facture->id) }}" target="_blank" class="btn btn-danger shadow-sm">
                <i class="fas fa-file-pdf me-2"></i> Imprimer / PDF
            </a>
            <a href="{{ route('dossiers.show', $facture->dossier_id) }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Retour au dossier
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4 border-0" style="border-radius: 15px;">
                <div class="card-body p-4">
                    <div class="row mb-4">
                        <div class="col-sm-6">
                            <h6 class="text-muted text-uppercase small fw-bold">Client</h6>
                            <div class="fs-5 fw-bold">{{ $facture->dossier->client->name ?? '—' }}</div>
                            <div class="text-muted">{{ $facture->dossier->client->telephone ?? '—' }}</div>
                            <div class="text-muted">{{ $facture->dossier->client->email ?? '—' }}</div>
                        </div>
                        <div class="col-sm-6 text-sm-end">
                            <h6 class="text-muted text-uppercase small fw-bold">Appareil</h6>
                            <div class="fw-bold">{{ $facture->dossier->appareil->modele ?? '—' }}</div>
                            <div class="text-muted small">IMEI : {{ $facture->dossier->imei }}</div>
                            <div class="mt-1">
                                <span class="badge {{ $facture->dossier->sous_garantie ? 'bg-success' : 'bg-secondary' }} rounded-pill">
                                    {{ $facture->dossier->sous_garantie ? 'Garantie' : 'Hors garantie' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light small text-uppercase">
                                <tr>
                                    <th>Désignation</th>
                                    <th class="text-center">Qté</th>
                                    <th class="text-end">Total TTC</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- Pièces --}}
                                @foreach($facture->pieces as $piece)
                                <tr>
                                    <td>
                                        <div class="fw-bold text-dark">{{ $piece->nom }}</div>
                                        <small class="text-muted">Pièce détachée</small>
                                    </td>
                                    <td class="text-center">{{ $piece->qty ?? $piece->pivot->quantite }}</td>
                                    <td class="text-end fw-bold">{{ number_format($piece->total_ligne ?? ($piece->pivot->quantite * $piece->pivot->prix_unitaire), 3, ',', ' ') }} DT</td>
                                </tr>
                                @endforeach

                                {{-- Main d'oeuvre --}}
                                @foreach($facture->tarifsMo as $mo)
                                <tr>
                                    <td>
                                        <div class="fw-bold text-dark">{{ $mo->type_intervention }}</div>
                                        <small class="text-muted">Main d'œuvre technique</small>
                                    </td>
                                    <td class="text-center">1</td>
                                    <td class="text-end fw-bold">{{ number_format($mo->pivot->montant, 3, ',', ' ') }} DT</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow border-0" style="border-radius: 15px; background: #2563eb; color: white;">
                <div class="card-body p-4">
                    <h6 class="text-white-50 text-uppercase small fw-bold mb-4">Total Facturation</h6>

                    <div class="d-flex justify-content-between mb-2">
                        <span class="opacity-75">Sous-total :</span>
                        <span class="fw-bold">{{ number_format($ttcTotal, 3, ',', ' ') }} DT</span>
                    </div>

                    @if($facture->remise > 0)
                    <div class="d-flex justify-content-between mb-2 text-warning">
                        <span class="opacity-75">Remise ({{ $facture->remise }}%) :</span>
                        <span class="fw-bold">- {{ number_format($montantRemise, 3, ',', ' ') }} DT</span>
                    </div>
                    @endif

                    <hr class="border-white opacity-25 my-3">

                    <div class="text-center py-2">
                        <div class="small opacity-75 text-uppercase">Montant Net TTC</div>
                        <div class="display-6 fw-bold mb-0">{{ number_format($facture->montant_total, 3, ',', ' ') }}</div>
                        <div class="fw-bold">DINARS TUNISIENS</div>
                    </div>

                    <div class="mt-4 bg-white text-dark p-3 rounded-3 shadow-sm">
                        <div class="d-flex align-items-center">
                            <div class="bg-success text-white p-2 rounded-circle me-3">
                                <i class="fas fa-check"></i>
                            </div>
                            <div>
                                <div class="fw-bold">Facture Payée</div>
                                <small class="text-muted">Le {{ \Carbon\Carbon::parse($facture->date_facture)->format('d/m/Y') }}</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
