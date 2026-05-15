@extends('layouts.app')

@section('title', 'Tableau de Bord Technicien')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Espace Technicien</h1>

    <div class="row g-3 mb-4">
        @php
            $techKpis = [
                ['label'=>'DOSSIERS ASSIGNÉS', 'val'=>$assignedDossiers,    'icon'=>'fa-clipboard-list', 'class'=>'bg-soft-primary'],
                ['label'=>'EN DIAGNOSTIC',     'val'=>$dossiersEnDiagnostic, 'icon'=>'fa-search',         'class'=>'bg-soft-warning'],
                ['label'=>'INTERV. TERMINÉES', 'val'=>$dossiersTermines,     'icon'=>'fa-check-circle',   'class'=>'bg-soft-success'],
            ];
        @endphp
        @foreach($techKpis as $k)
        <div class="col-md-4">
            <div class="card kpi-card">
                <div class="card-body">
                    <div class="kpi-icon-wrapper {{ $k['class'] }}">
                        <i class="fas {{ $k['icon'] }}"></i>
                    </div>
                    <div class="kpi-content">
                        <div class="kpi-value">{{ $k['val'] }}</div>
                        <div class="kpi-label">{{ $k['label'] }}</div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="card shadow">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Mes Interventions Récentes</h6>
        </div>
        <div class="card-body">
            <table class="table">
                <thead>
                    <tr>
                        <th>N° Dossier</th>
                        <th>Statut</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentDossiers as $dossier)
                    <tr>
                        <td>#{{ $dossier->num_dossier }}</td>
                        <td>{{ $dossier->statut }}</td>
                        <td>
                            <a href="{{ route('dossiers.show', $dossier->id) }}" class="btn btn-sm btn-primary">Gérer</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
