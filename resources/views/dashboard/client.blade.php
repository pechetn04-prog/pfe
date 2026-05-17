@extends('layouts.app')

@section('title', 'Mon Espace SAV — Maison Tel')

@section('content')
<div class="container-fluid px-4 py-4">

    {{-- En-tête --}}
    <div class="mb-4">
        <h1 class="h3 fw-bold mb-0 text-dark">Espace Client</h1>
        <p class="text-muted small">Bienvenue sur votre portail de suivi SAV, {{ $user->name }}</p>
    </div>

    {{-- Résumé en Cartes --}}
    <div class="row g-3 mb-5">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 20px; background: linear-gradient(135deg, #1e69ff 0%, #0047d5 100%); color: white;">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="h3 fw-bold mb-0">{{ $totalDossiers }}</div>
                            <div class="small opacity-75 fw-bold text-uppercase">Total Dossiers</div>
                        </div>
                        <div class="bg-white bg-opacity-25 rounded-circle p-3">
                            <i class="fas fa-folder-open fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 20px; background: #ffffff;">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="h3 fw-bold mb-0 text-dark">{{ $dossiersEnCours }}</div>
                            <div class="small text-muted fw-bold text-uppercase">En cours de traitement</div>
                        </div>
                        <div class="bg-soft-warning rounded-circle p-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                            <i class="fas fa-tools text-warning fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 20px; background: #ffffff;">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="h3 fw-bold mb-0 text-success">{{ $dossiersPrets }}</div>
                            <div class="small text-muted fw-bold text-uppercase">Prêts pour retrait</div>
                        </div>
                        <div class="bg-soft-success rounded-circle p-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                            <i class="fas fa-check-circle text-success fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Historique complet --}}
    <div class="card border-0 shadow-sm" style="border-radius: 20px;">
        <div class="card-header bg-white border-0 py-4 px-4">
            <h5 class="fw-bold mb-0 text-dark">Historique de vos dossiers</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr class="small text-muted text-uppercase fw-bold">
                        <th class="ps-4">Référence</th>
                        <th>Appareil</th>
                        <th>Date</th>
                        <th class="text-center">Statut</th>
                        <th class="text-end pe-4">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($dossiers as $d)
                    <tr>
                        <td class="ps-4">
                            <a href="{{ route('client.ticket', $d->id) }}" class="fw-bold text-primary text-decoration-none">
                                #{{ $d->num_dossier }}
                            </a>
                        </td>
                        <td>
                            <div class="fw-bold text-dark">{{ $d->appareil->modele ?? '—' }}</div>
                            <div class="small text-muted text-truncate" style="max-width: 250px;">{{ $d->panne_declaree }}</div>
                        </td>
                        <td class="text-muted small">{{ \Carbon\Carbon::parse($d->date_reception)->format('d/m/Y') }}</td>
                        <td class="text-center">
                            <span class="badge bg-{{ $d->badge_color }} rounded-pill px-3 py-2 small fw-bold">
                                {{ $d->badge_label }}
                            </span>
                        </td>
                        <td class="text-end pe-4">
                            <a href="{{ route('client.ticket', $d->id) }}" class="btn btn-primary btn-sm rounded-pill px-4 fw-bold shadow-sm">
                                <i class="fas fa-eye me-1"></i> Voir
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center py-5 text-muted">Aucun dossier trouvé.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<style>
    .bg-soft-warning { background-color: #fff7ed; }
    .bg-soft-success { background-color: #f0fdf4; }
    .bg-soft-info { background-color: #f0f9ff; }
    .animate-pulse { animation: pulse 2s infinite; }
    @keyframes pulse {
        0% { transform: scale(1); box-shadow: 0 0 0 0 rgba(251, 191, 36, 0.7); }
        70% { transform: scale(1.05); box-shadow: 0 0 0 10px rgba(251, 191, 36, 0); }
        100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(251, 191, 36, 0); }
    }
</style>
@endsection
