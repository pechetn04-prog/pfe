@extends('layouts.app')

@section('title', 'Liste des tickets SAV')



@section('content')
    <div class="container-fluid px-4 py-3">

        {{-- Header --}}
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <div>
                <h1 class="h3 fw-bold mb-0" style="color: #1a2332; letter-spacing: -0.5px;">Liste des tickets SAV</h1>
            </div>
            @if(auth()->user()->role === 'Agent')
                <a href="{{ route('dossiers.create') }}" class="btn btn-primary px-4 py-2 shadow-sm fw-bold" style="border-radius: 10px; background-color: #2563eb;">
                    <i class="fas fa-plus me-2"></i> Nouveau ticket
                </a>
            @endif
        </div>

        {{-- KPIs --}}
        <div class="row g-3 mb-4">
            @foreach($stats_kpis as $k)
                <div class="col-xl-3 col-md-6">
                    <div class="card kpi-card shadow-sm h-100">
                        <div class="card-body d-flex align-items-center p-3">
                            <div class="kpi-icon-wrapper me-3" style="background: {{ $k['bg'] }};">
                                <i class="fas {{ $k['icon'] }} fa-lg" style="color:{{ $k['color'] }};"></i>
                            </div>
                            <div>
                                <div class="h3 fw-bold mb-0 text-dark">{{ $k['val'] }}</div>
                                <div class="text-muted text-uppercase fw-bold"
                                    style="font-size: 0.65rem; letter-spacing: 0.5px;">{{ $k['label'] }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Filtres --}}
        <div class="card filter-card shadow-sm mb-4">
            <div class="card-body p-4">
                <form action="{{ route('dossiers.index') }}" method="GET">
                    <div class="row g-3">
                        <div class="col-12">
                            <div class="input-group">
                                <input type="text" name="search" class="form-control filter-input border-0"
                                    placeholder="Rechercher par ID, IMEI, nom client..." value="{{ request('search') }}"
                                    style="border-radius: 10px 0 0 10px !important;">
                                <button class="btn btn-primary px-4 fw-bold" type="submit" style="border-radius: 0 10px 10px 0 !important; background-color: #2563eb;">
                                    <i class="fas fa-search me-2"></i> Filtrer
                                </button>
                                <a href="{{ route('dossiers.index') }}" class="btn btn-light ms-2 px-4 fw-bold"
                                    style="border-radius: 10px !important;">
                                    <i class="fas fa-sync-alt me-2"></i> Réinitialiser
                                </a>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="small text-muted fw-bold mb-1">Statut</label>
                            <select name="statut" class="form-select filter-select border-0">
                                <option value="">Tous les statuts</option>
                                @foreach(['RECU', 'AFFECTE', 'EN_DIAGNOSTIC', 'EN_ATTENTE_DEVIS', 'EN_REPARATION', 'REPARE', 'FACTURE', 'LIVRE', 'CLOTURE'] as $s)
                                    <option value="{{ $s }}" {{ request('statut') == $s ? 'selected' : '' }}>
                                        {{ str_replace('_', ' ', $s) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="small text-muted fw-bold mb-1">Technicien</label>
                            <select name="technicien_id" class="form-select filter-select border-0">
                                <option value="">Tous les techniciens</option>
                                @foreach(\App\Models\User::where('role', 'Technicien')->get() as $t)
                                    <option value="{{ $t->id }}" {{ request('technicien_id') == $t->id ? 'selected' : '' }}>
                                        {{ $t->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="small text-muted fw-bold mb-1">Garantie</label>
                            <select name="garantie" class="form-select filter-select border-0">
                                <option value="">Toutes</option>
                                <option value="1" {{ request('garantie') == '1' ? 'selected' : '' }}>Sous garantie</option>
                                <option value="0" {{ request('garantie') == '0' ? 'selected' : '' }}>Hors garantie</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="small text-muted fw-bold mb-1">Depuis le</label>
                            <input type="date" name="from" class="form-control filter-input border-0"
                                value="{{ request('from') }}">
                        </div>
                        <div class="col-md-2">
                            <label class="small text-muted fw-bold mb-1">Jusqu'au</label>
                            <input type="date" name="to" class="form-control filter-input border-0" value="{{ request('to') }}">
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- Tableau --}}
        <div class="card dossiers-table-card shadow-sm overflow-hidden mb-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 dossier-table">
                    <thead class="bg-white border-bottom">
                        <tr class="small text-dark fw-bold">
                            <th class="ps-4">N° Ticket</th>
                            <th>IMEI</th>
                            <th>Produit</th>
                            <th>Client</th>
                            <th>Date Réception</th>
                            <th>Garantie</th>
                            <th>Technicien</th>
                            <th>Statut</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white">
                        @forelse($dossiers as $d)
                            <tr>
                                <td class="ps-4">
                                    <a href="{{ route('dossiers.show', $d->id) }}" class="dossier-num-link">
                                        #{{ $d->num_dossier }}
                                    </a>
                                </td>
                                <td class="text-muted small">{{ $d->imei }}</td>
                                <td class="text-primary fw-bolder small" style="font-weight: 800;">
                                    {{ $d->appareil->modele ?? '—' }}</td>
                                <td>
                                    <div class="fw-bold text-dark small">{{ $d->client->name ?? '—' }}</div>
                                    <div class="text-muted small" style="font-size: 0.7rem;">{{ $d->client->telephone ?? '' }}
                                    </div>
                                </td>
                                <td class="small">{{ $d->date_reception ? $d->date_reception->format('d/m/Y') : '—' }}</td>
                                <td>
                                    @if($d->garantie_annulee)
                                        <span class="warranty-badge-capsule excluded-warranty">GARANTIE EXCLUE</span>
                                    @elseif($d->sous_garantie)
                                        <span class="warranty-badge-capsule under-warranty">SOUS GARANTIE</span>
                                    @else
                                        <span class="warranty-badge-capsule out-of-warranty">HORS GARANTIE</span>
                                    @endif
                                </td>
                                <td class="small">{{ $d->technicien->name ?? 'Non assigné' }}</td>
                                <td>
                                    <span class="status-badge-capsule {{ $d->statut_class }} text-uppercase">
                                        {{ $d->statut_text }}
                                    </span>
                                </td>
                                <td class="text-end pe-4">
                                    <a href="{{ route('dossiers.show', $d->id) }}" class="action-btn-circle">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-5 text-muted">Aucun ticket trouvé.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($dossiers->hasPages())
                <div class="p-4 bg-white border-top">
                    {{ $dossiers->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection