@extends('layouts.app')

@section('title', 'Demandes de Retrait')

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
    <div class="card shadow-sm border-0 text-center py-5" style="border-radius: 15px;">
        @if($demandes->isEmpty())
            <div class="card-body">
                <div class="bg-success bg-opacity-10 d-inline-block p-4 rounded-circle mb-3">
                    <i class="fas fa-check fa-2x text-success"></i>
                </div>
                <h5 class="text-muted">Aucune demande de retrait en attente</h5>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    {{-- Tableau si données présentes --}}
                </table>
            </div>
        @endif
    </div>
</div>
@endsection
