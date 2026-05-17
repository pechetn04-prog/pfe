@extends('layouts.app')

@section('title', 'Devis #' . $devis->numero)

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="d-flex align-items-center justify-content-between mb-4">
                <div>
                    <h1 class="h3 fw-bold mb-0 text-dark">Détails du Devis</h1>
                    <p class="text-muted mb-0">{{ $devis->numero }} — Dossier #{{ $devis->dossier->num_dossier }}</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('devis.pdf', $devis->id) }}" target="_blank" class="btn btn-outline-primary rounded-pill px-4 shadow-sm">
                        <i class="fas fa-file-pdf me-2"></i> Télécharger PDF
                    </a>
                    <a href="{{ route('dossiers.show', $devis->dossier_id) }}" class="btn btn-light rounded-pill px-4 shadow-sm">
                        <i class="fas fa-arrow-left me-2"></i> Retour au dossier
                    </a>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success border-0 shadow-sm rounded-4 mb-4">
                    <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                </div>
            @endif

            <div class="row g-4">
                <div class="col-md-8">
                    <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px;">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between mb-5">
                                <div>
                                    <h6 class="text-muted text-uppercase small fw-bold mb-3">DESTINATAIRE</h6>
                                    <h5 class="fw-bold mb-1">{{ $devis->dossier->client->name ?? '—' }}</h5>
                                    <p class="text-muted mb-0 small"><i class="fas fa-envelope me-2"></i>{{ $devis->dossier->client->email ?? '—' }}</p>
                                    <p class="text-muted mb-0 small"><i class="fas fa-phone me-2"></i>{{ $devis->dossier->client->telephone ?? '—' }}</p>
                                </div>
                                <div class="text-end">
                                    <h6 class="text-muted text-uppercase small fw-bold mb-3">STATUT DU DEVIS</h6>
                                    <span class="badge bg-{{ $badgeColor }} rounded-pill px-4 py-2 mb-2" style="font-size: 0.8rem;">
                                        {{ $devis->statut }}
                                    </span>
                                    <p class="text-muted mb-0 small">Créé le : {{ $devis->date_creation ? $devis->date_creation->format('d/m/Y') : $devis->created_at->format('d/m/Y') }}</p>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-4">
                                    <thead class="bg-light">
                                        <tr class="small text-muted text-uppercase">
                                            <th style="width: 50%;">Désignation</th>
                                            <th class="text-center">Qté</th>
                                            <th class="text-end">P.U (DT)</th>
                                            <th class="text-end">Total (DT)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {{-- Pièces --}}
                                        @foreach($devis->pieces as $piece)
                                        <tr>
                                            <td>
                                                <div class="fw-bold text-dark">{{ $piece->nom }}</div>
                                                <small class="text-muted">{{ $piece->reference }}</small>
                                            </td>
                                            <td class="text-center">{{ $piece->pivot->quantite }}</td>
                                            <td class="text-end">{{ number_format($piece->pivot->prix_unitaire, 3, '.', ' ') }}</td>
                                            <td class="text-end fw-bold">{{ number_format($piece->pivot->quantite * $piece->pivot->prix_unitaire, 3, '.', ' ') }}</td>
                                        </tr>
                                        @endforeach

                                        {{-- Main d'œuvre --}}
                                        @foreach($devis->tarifsMo as $tarif)
                                        <tr>
                                            <td>
                                                <div class="fw-bold text-dark">{{ $tarif->type_intervention }}</div>
                                                <small class="text-muted text-uppercase" style="font-size: 0.6rem;">Main d'œuvre</small>
                                            </td>
                                            <td class="text-center">1</td>
                                            <td class="text-end">{{ number_format($tarif->pivot->montant, 3, '.', ' ') }}</td>
                                            <td class="text-end fw-bold">{{ number_format($tarif->pivot->montant, 3, '.', ' ') }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot class="bg-light bg-opacity-50">

                                        <tr class="h5">
                                            <th colspan="3" class="text-end fw-bold">TOTAL TTC</th>
                                            <th class="text-end fw-bold text-primary">{{ number_format($devis->montant_total, 3, '.', ' ') }} DT</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    {{-- Actions Agent --}}
                    @if($devis->statut === 'EN_ATTENTE' && in_array(auth()->user()->role, ['Agent', 'Admin']))
                    <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px; border-top: 4px solid #ff9800 !important;">
                        <div class="card-body p-4 text-center">
                            <div class="bg-soft-warning p-3 rounded-circle d-inline-flex mb-3">
                                <i class="fas fa-phone-alt fa-2x text-warning"></i>
                            </div>
                            <h5 class="fw-bold mb-3">Validation Manuelle</h5>
                            <p class="text-muted small mb-4">Après avoir contacté le client par téléphone, vous pouvez valider sa décision ici.</p>
                            
                            <div class="d-grid gap-2">
                                <form action="{{ route('devis.accepter', $devis->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-success w-100 rounded-pill py-2 fw-bold shadow-sm" onclick="return confirm('Confirmer l\'acceptation du devis par le client ?')">
                                        <i class="fas fa-check-circle me-2"></i> LE CLIENT ACCEPTE
                                    </button>
                                </form>
                                <form action="{{ route('devis.refuser', $devis->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-danger w-100 rounded-pill py-2 fw-bold" onclick="return confirm('Confirmer le refus du devis par le client ?')">
                                        <i class="fas fa-times-circle me-2"></i> LE CLIENT REFUSE
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endif

                    <div class="card border-0 shadow-sm" style="border-radius: 15px;">
                        <div class="card-body p-4">
                            <h6 class="fw-bold mb-3">Détails Appareil</h6>
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-light p-2 rounded-3 me-3"><i class="fas fa-mobile-alt text-muted"></i></div>
                                <div>
                                    <div class="small text-muted">Modèle</div>
                                    <div class="fw-bold">{{ $devis->dossier->appareil->modele ?? '—' }}</div>
                                </div>
                            </div>
                            <div class="d-flex align-items-center mb-0">
                                <div class="bg-light p-2 rounded-3 me-3"><i class="fas fa-fingerprint text-muted"></i></div>
                                <div>
                                    <div class="small text-muted">IMEI</div>
                                    <div class="fw-bold text-primary">{{ $devis->dossier->imei }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
