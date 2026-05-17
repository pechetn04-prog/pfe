@extends('layouts.app')

@section('title', 'Liste des tickets SAV')

@section('content')
    <div class="container-fluid">

        {{-- Header --}}
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <div>
                <h1 class="h3 fw-bold mb-0">Liste des tickets SAV</h1>
            </div>
            @if(auth()->user()->role === 'Agent')
                <a href="{{ route('dossiers.create') }}" class="btn btn-primary px-4 shadow-sm fw-bold">
                    <i class="fas fa-plus me-2"></i> Nouveau ticket
                </a>
            @endif
        </div>

        {{-- KPIs --}}
        <div class="row g-3 mb-4">

            @foreach($stats_kpis as $k)
                <div class="col-xl-3 col-md-6">
                    <div class="card border-0 shadow-sm h-100" style="border-radius: 12px;">
                        <div class="card-body d-flex align-items-center p-3">
                            <div class="rounded-3 p-3 me-3 d-flex align-items-center justify-content-center"
                                style="background: {{ $k['bg'] }}; width: 60px; height: 60px;">
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
        <div class="card border-0 shadow-sm mb-4" style="border-radius: 20px;">
            <div class="card-body p-4">
                <form action="{{ route('dossiers.index') }}" method="GET">
                    <div class="row g-3">
                        <div class="col-12">
                            <div class="input-group">
                                <input type="text" name="search" class="form-control bg-light border-0"
                                    placeholder="Rechercher par ID, IMEI, nom client..." value="{{ request('search') }}"
                                    style="border-radius: 10px 0 0 10px;">
                                <button class="btn btn-primary px-4" type="submit" style="border-radius: 0 10px 10px 0;">
                                    <i class="fas fa-search me-2"></i> Filtrer
                                </button>
                                <a href="{{ route('dossiers.index') }}" class="btn btn-light ms-2 px-4"
                                    style="border-radius: 10px;">
                                    <i class="fas fa-sync-alt me-2"></i> Réinitialiser
                                </a>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="small text-muted fw-bold mb-1">Statut</label>
                            <select name="statut" class="form-select bg-light border-0">
                                <option value="">Tous les statuts</option>
                                @foreach(['RECU', 'AFFECTE', 'EN_DIAGNOSTIC', 'EN_ATTENTE_DEVIS', 'EN_REPARATION', 'REPARE', 'FACTURE', 'LIVRE', 'CLOTURE'] as $s)
                                    <option value="{{ $s }}" {{ request('statut') == $s ? 'selected' : '' }}>
                                        {{ str_replace('_', ' ', $s) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="small text-muted fw-bold mb-1">Technicien</label>
                            <select name="technicien_id" class="form-select bg-light border-0">
                                <option value="">Tous les techniciens</option>
                                @foreach(\App\Models\User::where('role', 'Technicien')->get() as $t)
                                    <option value="{{ $t->id }}" {{ request('technicien_id') == $t->id ? 'selected' : '' }}>
                                        {{ $t->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="small text-muted fw-bold mb-1">Garantie</label>
                            <select name="garantie" class="form-select bg-light border-0">
                                <option value="">Toutes</option>
                                <option value="1" {{ request('garantie') == '1' ? 'selected' : '' }}>Sous garantie</option>
                                <option value="0" {{ request('garantie') == '0' ? 'selected' : '' }}>Hors garantie</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="small text-muted fw-bold mb-1">Depuis le</label>
                            <input type="date" name="from" class="form-control bg-light border-0"
                                value="{{ request('from') }}">
                        </div>
                        <div class="col-md-2">
                            <label class="small text-muted fw-bold mb-1">Jusqu'au</label>
                            <input type="date" name="to" class="form-control bg-light border-0" value="{{ request('to') }}">
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- Tableau --}}
        <div class="card border-0 shadow-sm overflow-hidden" style="border-radius: 20px;">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
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
                                <td class="ps-4 fw-bolder" style="font-weight: 800;">
                                    <a href="{{ route('dossiers.show', $d->id) }}" class="text-decoration-none text-dark hover-primary">
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
                                        <span
                                            class="badge rounded-pill bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 px-3 py-1"
                                            style="font-size: 0.65rem; font-weight: 800;">GARANTIE EXCLUE</span>
                                    @elseif($d->sous_garantie)
                                        <span
                                            class="badge rounded-pill bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-1"
                                            style="font-size: 0.65rem; font-weight: 800;">SOUS GARANTIE</span>
                                    @else
                                        <span
                                            class="badge rounded-pill bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-3 py-1"
                                            style="font-size: 0.65rem; font-weight: 800;">HORS GARANTIE</span>
                                    @endif
                                </td>
                                <td class="small">{{ $d->technicien->name ?? 'Non assigné' }}</td>
                                <td>
                                    <span class="badge rounded-pill px-3 py-1 fw-bold text-uppercase shadow-sm"
                                        style="background: {{ $d->statut_bg }}; color: {{ $d->statut_color }}; font-size: 0.68rem; letter-spacing: 0.5px;">
                                        {{ $d->statut_text }}
                                    </span>
                                </td>
                                <td class="text-end pe-4">
                                    <a href="{{ route('dossiers.show', $d->id) }}" class="btn btn-sm btn-light text-primary rounded-circle shadow-sm d-inline-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center py-5 text-muted">Aucun ticket trouvé.</td>
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