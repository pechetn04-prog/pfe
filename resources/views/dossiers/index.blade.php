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
        @php
            $stats_kpis = [
                ['label'=>'TOTAL TICKETS',       'val'=> $dossiers->total(),                                                     'icon'=>'fa-folder',      'color'=>'#2563eb', 'bg'=>'#eff6ff'],
                ['label'=>'EN DIAGNOSTIC',       'val'=> \App\Models\Dossier::where('statut','EN_DIAGNOSTIC')->count(),          'icon'=>'fa-microscope',  'color'=>'#f59e0b', 'bg'=>'#fff7ed'],
                ['label'=>'EN RÉPARATION',       'val'=> \App\Models\Dossier::where('statut','EN_REPARATION')->count(),          'icon'=>'fa-tools',       'color'=>'#0ea5e9', 'bg'=>'#f0f9ff'],
                ['label'=>'RÉPARÉS AUJOURD\'HUI','val'=> \App\Models\Dossier::where('statut','REPARE')->whereDate('updated_at', now())->count(), 'icon'=>'fa-check-circle', 'color'=>'#10b981', 'bg'=>'#f0fdf4'],
            ];
        @endphp
        @foreach($stats_kpis as $k)
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 12px;">
                <div class="card-body d-flex align-items-center p-3">
                    <div class="rounded-3 p-3 me-3 d-flex align-items-center justify-content-center" style="background: {{ $k['bg'] }}; width: 60px; height: 60px;">
                        <i class="fas {{ $k['icon'] }} fa-lg" style="color:{{ $k['color'] }};"></i>
                    </div>
                    <div>
                        <div class="h3 fw-bold mb-0 text-dark">{{ $k['val'] }}</div>
                        <div class="text-muted text-uppercase fw-bold" style="font-size: 0.65rem; letter-spacing: 0.5px;">{{ $k['label'] }}</div>
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
                            <input type="text" name="search" class="form-control bg-light border-0" placeholder="Rechercher par ID, IMEI, nom client..." value="{{ request('search') }}" style="border-radius: 10px 0 0 10px;">
                            <button class="btn btn-primary px-4" type="submit" style="border-radius: 0 10px 10px 0;">
                                <i class="fas fa-search me-2"></i> Filtrer
                            </button>
                            <a href="{{ route('dossiers.index') }}" class="btn btn-light ms-2 px-4" style="border-radius: 10px;">
                                <i class="fas fa-sync-alt me-2"></i> Réinitialiser
                            </a>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="small text-muted fw-bold mb-1">Statut</label>
                        <select name="statut" class="form-select bg-light border-0">
                            <option value="">Tous les statuts</option>
                            @foreach(['RECU','AFFECTE','EN_DIAGNOSTIC','EN_ATTENTE_DEVIS','EN_REPARATION','REPARE','FACTURE','LIVRE','CLOTURE'] as $s)
                                <option value="{{ $s }}" {{ request('statut') == $s ? 'selected' : '' }}>{{ str_replace('_', ' ', $s) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="small text-muted fw-bold mb-1">Technicien</label>
                        <select name="technicien_id" class="form-select bg-light border-0">
                            <option value="">Tous les techniciens</option>
                            @foreach(\App\Models\User::where('role','Technicien')->get() as $t)
                                <option value="{{ $t->id }}" {{ request('technicien_id') == $t->id ? 'selected' : '' }}>{{ $t->name }}</option>
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
                        <input type="date" name="from" class="form-control bg-light border-0" value="{{ request('from') }}">
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
                        <th>Délai</th>
                        <th>Garantie</th>
                        <th>Technicien</th>
                        <th>Statut</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white">
                    @forelse($dossiers as $d)
                    @php
                        $jours = \Carbon\Carbon::parse($d->date_reception)->diffInDays(now());
                        $sMap = [
                            'RECU' => ['#fef3c7', '#d97706', 'Ouvert'],
                            'AFFECTE' => ['#e0e7ff', '#4f46e5', 'Affecté'],
                            'EN_DIAGNOSTIC' => ['#fff7ed', '#ea580c', 'En diagnostic'],
                            'EN_ATTENTE_DEVIS' => ['#f0f9ff', '#0284c7', 'En attente devis'],
                            'EN_REPARATION' => ['#f0f9ff', '#0ea5e9', 'En réparation'],
                            'REPARE' => ['#f0fdf4', '#16a34a', 'Réparé'],
                            'FACTURE' => ['#eff6ff', '#2563eb', 'Facturé'],
                            'LIVRE' => ['#f0fdf4', '#16a34a', 'Livré'],
                            'CLOTURE' => ['#f1f5f9', '#475569', 'Clôturé'],
                        ];
                        $st = $sMap[$d->statut] ?? ['#f1f5f9', '#475569', $d->statut];
                    @endphp
                    <tr>
<td class="ps-4 fw-bolder" style="font-weight: 800;">#{{ $d->num_dossier }}</td>
                        <td class="text-muted small">{{ $d->imei }}</td>
                        <td class="text-primary fw-bolder small" style="font-weight: 800;">{{ $d->appareil->modele ?? '—' }}</td>
                        <td class="text-muted small">{{ $d->client->name ?? '—' }}</td>
                        <td class="small">{{ $d->date_reception ? $d->date_reception->format('d/m/Y') : '—' }}</td>
                        <td class="small fw-bold {{ $jours > 2 ? 'text-danger' : 'text-muted' }}">{{ $jours }}j</td>
                        <td>
                            @if($d->sous_garantie)
                                <span class="badge rounded-pill bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-1" style="font-size: 0.65rem;">Sous garantie</span>
                            @else
                                <span class="badge rounded-pill bg-light text-muted border border-secondary border-opacity-25 px-3 py-1" style="font-size: 0.65rem;">Hors garantie</span>
                            @endif
                        </td>
                        <td class="small">{{ $d->technicien->name ?? 'Non assigné' }}</td>
                        <td>
                            <span class="badge rounded-pill px-3 py-1" style="background: {{ $st[0] }}; color: {{ $st[1] }}; font-size: 0.65rem;">{{ $st[2] }}</span>
                        </td>
                        <td class="text-end pe-4">
                            <div class="d-flex justify-content-end gap-1">
                                @if(in_array(auth()->user()->role, ['Admin', 'Agent']) && $d->statut === 'EN_ATTENTE_DEVIS' && !$d->devis)
                                    <a href="{{ route('devis.create', $d->id) }}" class="btn btn-outline-primary btn-sm rounded-pill px-2" title="Établir le devis">
                                        <i class="fas fa-file-invoice-dollar"></i>
                                    </a>
                                @endif
                                @if(in_array(auth()->user()->role, ['Admin', 'Agent']) && $d->statut === 'REPARE' && !$d->facture)
                                    <a href="{{ route('factures.create', $d->id) }}" class="btn btn-outline-success btn-sm rounded-pill px-2" title="Générer la facture">
                                        <i class="fas fa-file-invoice"></i>
                                    </a>
                                @endif
                                <a href="{{ route('dossiers.show', $d->id) }}" class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm">
                                    <i class="fas fa-eye me-1"></i> Voir
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="10" class="text-center py-5 text-muted">Aucun ticket trouvé.</td></tr>
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
