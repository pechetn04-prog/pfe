@extends('layouts.app')

@section('title', 'Dossier #{{ $dossier->num_dossier }} — Suivi Client')

@section('content')
<div class="container py-4" style="max-width: 860px;">

    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('client.dashboard') }}" class="btn btn-outline-secondary btn-sm me-3">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="h4 fw-bold mb-0">Dossier #{{ $dossier->num_dossier }}</h1>
            <small class="text-muted">Reçu le {{ \Carbon\Carbon::parse($dossier->date_reception)->format('d/m/Y') }}</small>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    {{-- Statut --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body text-center py-4">
            @php
                $badges = [
                    'AFFECTE'          => ['secondary', 'Affecté au technicien'],
                    'EN_DIAGNOSTIC'    => ['info',      'Diagnostic en cours'],
                    'EN_ATTENTE_DEVIS' => ['warning',   'Devis en attente de validation'],
                    'EN_REPARATION'    => ['primary',   'Réparation en cours'],
                    'REPARE'           => ['success',   'Appareil réparé — en attente de livraison'],
                    'FACTURE'          => ['success',   'Facturé — en attente de livraison'],
                    'LIVRE'            => ['success',   'Livré ✓'],
                    'CLOTURE'          => ['dark',      'Dossier clôturé'],
                    'IRREPARABLE'      => ['danger',    'Appareil irréparable'],
                    'DEVIS_REFUSE'     => ['danger',    'Devis refusé'],
                ];
                $badge = $badges[$dossier->statut] ?? ['secondary', $dossier->statut];
            @endphp
            <span class="badge bg-{{ $badge[0] }} fs-6 px-4 py-2 rounded-pill">{{ $badge[1] }}</span>
        </div>
    </div>

    {{-- Devis à valider --}}
    @if($dossier->devis && $dossier->devis->statut === 'EN_ATTENTE')
    <div class="card border-warning border-2 shadow-sm mb-4">
        <div class="card-header bg-warning text-dark fw-bold">
            <i class="fas fa-file-invoice me-2"></i> Devis N° {{ $dossier->devis->numero }} — En attente de votre décision
        </div>
        <div class="card-body">
            <div class="row text-center mb-3">
                <div class="col">
                    <div class="small text-muted">Montant Total TTC</div>
                    <div class="fs-4 fw-bold text-primary">{{ number_format($dossier->devis->montant_total, 3, ',', ' ') }} DA</div>
                </div>
            </div>
            <div class="d-flex gap-2 justify-content-center">
                <form action="{{ route('client.devis.accepter', $dossier->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-success px-4">
                        <i class="fas fa-check me-2"></i> Accepter le devis
                    </button>
                </form>
                <form action="{{ route('client.devis.refuser', $dossier->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger px-4"
                        onclick="return confirm('Êtes-vous sûr de vouloir refuser ce devis ?')">
                        <i class="fas fa-times me-2"></i> Refuser
                    </button>
                </form>
            </div>
        </div>
    </div>
    @endif

    {{-- Historique --}}
    @if($dossier->suivi && $dossier->suivi->count())
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white fw-bold border-0">
            <i class="fas fa-history me-2 text-primary"></i> Historique du dossier
        </div>
        <div class="card-body py-2">
            @foreach($dossier->suivi->sortByDesc('created_at') as $suivi)
            <div class="d-flex align-items-start py-2 border-bottom">
                <div class="text-muted small me-3" style="min-width: 120px;">
                    {{ \Carbon\Carbon::parse($suivi->created_at)->format('d/m/Y H:i') }}
                </div>
                <div class="small">{{ $suivi->commentaire }}</div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Télécharger facture --}}
    @if($dossier->facture)
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body d-flex justify-content-between align-items-center">
            <span><i class="fas fa-file-pdf text-danger me-2"></i> Votre facture est disponible</span>
            <a href="{{ route('factures.pdf', $dossier->facture->id) }}" class="btn btn-sm btn-danger" target="_blank">
                Télécharger PDF
            </a>
        </div>
    </div>
    @endif

    {{-- Avis client (si livré) --}}
    @if($dossier->statut === 'LIVRE' || $dossier->statut === 'CLOTURE')
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white fw-bold border-0">
            <i class="fas fa-star me-2 text-warning"></i> Votre avis
        </div>
        <div class="card-body">
            <form action="{{ route('client.avis.submit', $dossier->id) }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label small">Note (1 à 5)</label>
                    <select name="note" class="form-select" style="max-width: 120px;">
                        @for($i = 5; $i >= 1; $i--)
                        <option value="{{ $i }}">{{ $i }} ★</option>
                        @endfor
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label small">Commentaire</label>
                    <textarea name="commentaire" class="form-control" rows="3" placeholder="Votre expérience..."></textarea>
                </div>
                <button type="submit" class="btn btn-primary px-4">Soumettre mon avis</button>
            </form>
        </div>
    </div>
    @endif

</div>
@endsection
