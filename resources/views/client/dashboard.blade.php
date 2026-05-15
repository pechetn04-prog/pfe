@extends('layouts.app')

@section('title', 'Mes Dossiers — Espace Client')

@section('content')
<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 fw-bold mb-0">Bonjour, {{ $user->name }}</h1>
            <small class="text-muted">Suivez l'état de vos réparations en temps réel</small>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }} <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @forelse($dossiers as $dossier)
    <div class="card border-0 shadow-sm mb-3" style="border-radius: 12px; border-left: 4px solid #2563eb !important;">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-3">
                    <div class="fw-bold text-primary">#{{ $dossier->num_dossier }}</div>
                    <small class="text-muted">{{ \Carbon\Carbon::parse($dossier->date_reception)->format('d/m/Y') }}</small>
                </div>
                <div class="col-md-4">
                    <div class="small text-muted mb-1">Panne déclarée</div>
                    <div class="fw-medium">{{ Str::limit($dossier->panne_declaree, 60) }}</div>
                </div>
                <div class="col-md-3 text-center">
                    @php
                        $badges = [
                            'AFFECTE'          => ['bg-secondary',  'Affecté'],
                            'EN_DIAGNOSTIC'    => ['bg-info text-dark', 'En diagnostic'],
                            'EN_ATTENTE_DEVIS' => ['bg-warning text-dark', 'Devis à valider'],
                            'EN_REPARATION'    => ['bg-primary', 'En réparation'],
                            'REPARE'           => ['bg-success', 'Réparé'],
                            'FACTURE'          => ['bg-success', 'Facturé'],
                            'LIVRE'            => ['bg-success', 'Livré ✓'],
                            'CLOTURE'          => ['bg-dark', 'Clôturé'],
                            'IRREPARABLE'      => ['bg-danger', 'Irréparable'],
                            'DEVIS_REFUSE'     => ['bg-danger', 'Devis refusé'],
                        ];
                        $badge = $badges[$dossier->statut] ?? ['bg-secondary', $dossier->statut];
                    @endphp
                    <span class="badge {{ $badge[0] }} rounded-pill px-3 py-2">{{ $badge[1] }}</span>
                </div>
                <div class="col-md-2 text-end">
                    <a href="{{ route('client.ticket', $dossier->id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                        Détails →
                    </a>
                </div>
            </div>

            {{-- Alerte devis en attente --}}
            @if($dossier->statut === 'EN_ATTENTE_DEVIS' && $dossier->devis && $dossier->devis->statut === 'EN_ATTENTE')
            <div class="alert alert-warning mt-3 mb-0 py-2 d-flex align-items-center justify-content-between">
                <span><i class="fas fa-file-invoice me-2"></i> Un devis est en attente de votre validation.</span>
                <a href="{{ route('client.ticket', $dossier->id) }}" class="btn btn-sm btn-warning">Voir le devis</a>
            </div>
            @endif
        </div>
    </div>
    @empty
    <div class="card border-0 shadow-sm text-center py-5">
        <div class="text-muted">
            <i class="fas fa-folder-open fa-3x mb-3 d-block"></i>
            Aucun dossier SAV trouvé pour votre compte.
        </div>
    </div>
    @endforelse

</div>
@endsection
