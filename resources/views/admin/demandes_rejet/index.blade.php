@extends('layouts.app')

@section('title', 'Demandes de Retrait')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/reject-demands.css') }}">
@endpush

@section('content')
<div class="container-fluid">
    <div class="mb-4">
        <h1 class="h3 mb-1 text-gray-800 fw-bold">Demandes de Retrait</h1>
        <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-3 py-1 small fw-bold">
            <i class="fas fa-exclamation-triangle me-1"></i> ALERTES TECHNICIENS
        </span>
    </div>

    {{-- Statistiques --}}
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm border-0 h-100 p-3" style="border-radius: 15px;">
                <div class="d-flex align-items-center">
                    <div class="bg-primary bg-opacity-10 p-3 rounded-3 me-3 text-primary"><i class="fas fa-list"></i></div>
                    <div>
                        <small class="text-muted text-uppercase fw-bold d-block" style="font-size: 0.7rem;">TOTAL</small>
                        <h4 class="mb-0 fw-bold">{{ $total }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 h-100 p-3" style="border-radius: 15px;">
                <div class="d-flex align-items-center">
                    <div class="bg-warning bg-opacity-10 p-3 rounded-3 me-3 text-warning"><i class="fas fa-clock"></i></div>
                    <div>
                        <small class="text-muted text-uppercase fw-bold d-block" style="font-size: 0.7rem;">EN ATTENTE</small>
                        <h4 class="mb-0 fw-bold">{{ $enAttente }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 h-100 p-3" style="border-radius: 15px;">
                <div class="d-flex align-items-center">
                    <div class="bg-success bg-opacity-10 p-3 rounded-3 me-3 text-success"><i class="fas fa-check-circle"></i></div>
                    <div>
                        <small class="text-muted text-uppercase fw-bold d-block" style="font-size: 0.7rem;">ACCEPTÉES</small>
                        <h4 class="mb-0 fw-bold">{{ $acceptees }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 h-100 p-3" style="border-radius: 15px;">
                <div class="d-flex align-items-center">
                    <div class="bg-danger bg-opacity-10 p-3 rounded-3 me-3 text-danger"><i class="fas fa-times-circle"></i></div>
                    <div>
                        <small class="text-muted text-uppercase fw-bold d-block" style="font-size: 0.7rem;">REFUSÉES</small>
                        <h4 class="mb-0 fw-bold">{{ $refusees }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Liste --}}
    @php
        $enAttenteList = $demandes->where('statut', 'EN_ATTENTE');
        $historiqueList = $demandes->where('statut', '!=', 'EN_ATTENTE');
    @endphp

    {{-- Section : Demandes en Attente --}}
    <div class="mb-4">
        <h5 class="fw-bold text-dark mb-3 d-flex align-items-center">
            <span class="p-2 bg-warning bg-opacity-10 rounded-3 me-2 text-warning"><i class="fas fa-clock"></i></span>
            Demandes en Attente de Traitement
            <span class="badge bg-warning text-dark ms-2 rounded-pill small" style="font-size: 0.7rem;">{{ $enAttenteList->count() }}</span>
        </h5>
        
        <div class="card shadow-sm border-0" style="border-radius: 15px;">
            @if($enAttenteList->isEmpty())
                <div class="card-body text-center py-5">
                    <div class="bg-light d-inline-block p-4 rounded-circle mb-3">
                        <i class="fas fa-check fa-2x text-muted"></i>
                    </div>
                    <h5 class="text-muted mb-0">Aucune demande en attente</h5>
                    <p class="small text-muted">Tous les dossiers sont actuellement traités.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light text-muted small text-uppercase fw-bold">
                            <tr>
                                <th class="ps-4">Technicien</th>
                                <th>Dossier</th>
                                <th>Client</th>
                                <th>Motif / Raison</th>
                                <th class="text-center">Date</th>
                        <tbody>
                            @foreach($enAttenteList as $demande)
                                <tr>
                                    <td class="ps-4">
                                        <div class="fw-bold text-dark">{{ $demande->user->name ?? 'Technicien inconnu' }}</div>
                                        <div class="small text-muted">Auteur</div>
                                    </td>
                                    <td>
                                        @if($demande->dossier)
                                            <a href="{{ route('dossiers.show', $demande->dossier->id) }}" class="fw-bold text-primary text-decoration-none">
                                                #{{ $demande->dossier->num_dossier }}
                                            </a>
                                            <div class="small text-muted">{{ $demande->dossier->appareil->modele ?? 'Appareil' }}</div>
                                        @else
                                            <span class="text-danger small">Dossier supprimé</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="small fw-bold text-dark">{{ $demande->dossier->client->name ?? '—' }}</div>
                                        <div class="small text-muted">{{ $demande->dossier->client->telephone ?? '—' }}</div>
                                    </td>
                                    <td>
                                        <div class="small text-dark fw-medium" style="max-width: 300px; white-space: normal;">
                                            {{ $demande->raison }}
                                        </div>
                                    </td>
                                    <td class="text-center small text-muted">
                                        <div class="fw-bold text-dark">{{ $demande->created_at->format('d/m/Y') }}</div>
                                        <div>{{ $demande->created_at->format('H:i') }}</div>
                                    </td>
                                    <td class="text-end pe-4">
                                        <div class="d-flex justify-content-end gap-2">
                                            <button type="button" class="btn btn-success btn-sm rounded-pill px-3 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#modalApprouver{{ $demande->id }}">
                                                <i class="fas fa-check me-1"></i> ACCEPTER
                                            </button>
                                            <button type="button" class="btn btn-outline-danger btn-sm rounded-pill px-3 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#modalRefuser{{ $demande->id }}">
                                                <i class="fas fa-times me-1"></i> REFUSER
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Modaux (En dehors de la table pour éviter les bugs d'affichage) --}}
                @foreach($enAttenteList as $demande)
                    {{-- Modal Approuver --}}
                    <div class="modal fade text-start" id="modalApprouver{{ $demande->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                                <div class="modal-header border-0 pb-0 pt-4 px-4">
                                    <h5 class="modal-title fw-bold text-success d-flex align-items-center">
                                        <i class="fas fa-check-circle me-2"></i> Approuver le retrait
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <form action="{{ route('admin.demandes_rejet.approve', $demande->id) }}" method="POST">
                                    @csrf
                                    <div class="modal-body p-4">
                                        <p class="text-muted small">En approuvant, le dossier sera <strong>désaffecté</strong> du technicien et reviendra en statut <strong>"RECU"</strong>.</p>
                                        <div class="mb-0">
                                            <label class="form-label small fw-bold text-muted text-uppercase" style="font-size: 0.6rem;">Commentaire pour le technicien</label>
                                            <textarea name="commentaire_admin" class="form-control bg-light border-0" rows="3" placeholder="Ex: Retrait accepté..."></textarea>
                                        </div>
                                    </div>
                                    <div class="modal-footer border-0 pb-4 px-4">
                                        <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Annuler</button>
                                        <button type="submit" class="btn btn-success rounded-pill px-4 fw-bold shadow" onclick="this.innerHTML='Traitement...'; this.disabled=true; this.form.submit();">CONFIRMER</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    {{-- Modal Refuser --}}
                    <div class="modal fade text-start" id="modalRefuser{{ $demande->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                                <div class="modal-header border-0 pb-0 pt-4 px-4">
                                    <h5 class="modal-title fw-bold text-danger d-flex align-items-center">
                                        <i class="fas fa-times-circle me-2"></i> Refuser le retrait
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <form action="{{ route('admin.demandes_rejet.reject', $demande->id) }}" method="POST">
                                    @csrf
                                    <div class="modal-body p-4">
                                        <p class="text-muted small">Veuillez justifier votre refus.</p>
                                        <div class="mb-0">
                                            <label class="form-label small fw-bold text-muted text-uppercase" style="font-size: 0.6rem;">Motif du refus (Requis)</label>
                                            <textarea name="commentaire_admin" class="form-control bg-light border-0" rows="3" required placeholder="Ex: Expertise requise indispensable..."></textarea>
                                        </div>
                                    </div>
                                    <div class="modal-footer border-0 pb-4 px-4">
                                        <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Annuler</button>
                                        <button type="submit" class="btn btn-danger rounded-pill px-4 fw-bold shadow" onclick="this.innerHTML='Traitement...'; this.disabled=true; this.form.submit();">CONFIRMER LE REFUS</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach      </div>
            @endif
        </div>
    </div>

    {{-- Section : Historique --}}
    <div>
        <h5 class="fw-bold text-muted mb-3 d-flex align-items-center">
            <span class="p-2 bg-light rounded-3 me-2 text-muted"><i class="fas fa-history"></i></span>
            Historique des Demandes Traitées
            <span class="badge bg-light text-muted ms-2 rounded-pill small" style="font-size: 0.7rem;">{{ $historiqueList->count() }}</span>
        </h5>

        <div class="card shadow-sm border-0" style="border-radius: 15px; opacity: 0.85;">
            @if($historiqueList->isEmpty())
                <div class="card-body text-center py-4">
                    <p class="text-muted small mb-0">Aucun historique disponible.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-sm align-middle mb-0">
                        <thead class="bg-light text-muted small text-uppercase fw-bold">
                            <tr>
                                <th class="ps-4">Dossier</th>
                                <th>Technicien</th>
                                <th>Statut</th>
                                <th>Commentaire Admin</th>
                                <th class="text-end pe-4">Date Traitement</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($historiqueList as $demande)
                                <tr>
                                    <td class="ps-4 py-3">
                                        <div class="fw-bold text-dark">#{{ $demande->dossier->num_dossier ?? '???' }}</div>
                                        <div class="text-muted small">{{ $demande->dossier->appareil->modele ?? '—' }}</div>
                                    </td>
                                    <td>
                                        <div class="small fw-bold">{{ $demande->user->name ?? '—' }}</div>
                                    </td>
                                    <td>
                                        @php
                                            $hStatus = [
                                                'APPROUVE' => ['class' => 'bg-success text-white', 'label' => 'APPROUVÉ'],
                                                'REFUSE' => ['class' => 'bg-danger text-white', 'label' => 'REFUSÉ']
                                            ][$demande->statut] ?? ['class' => 'bg-secondary text-white', 'label' => $demande->statut];
                                        @endphp
                                        <span class="badge rounded-pill {{ $hStatus['class'] }} px-2 py-1 small fw-bold" style="font-size: 0.6rem;">
                                            {{ $hStatus['label'] }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="small text-muted italic" style="max-width: 400px; white-space: normal;">
                                            {{ $demande->commentaire_admin ?: '—' }}
                                        </div>
                                    </td>
                                    <td class="text-end pe-4 small text-muted">
                                        {{ $demande->updated_at->format('d/m/Y H:i') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
    {{-- Pagination --}}
    <div class="mt-4 d-flex justify-content-center">
        {{ $demandes->links() }}
    </div>
</div>
@endsection
