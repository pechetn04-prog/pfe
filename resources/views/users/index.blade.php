@extends('layouts.app')

@section('title', 'Liste des utilisateurs')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/users.css') }}">
@endpush

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Liste des utilisateurs</h1>
            <small class="text-muted">Gestion des comptes du système SAV</small>
        </div>
        <a href="{{ route('users.create') }}" class="btn btn-primary px-4 shadow">
            <i class="fas fa-plus me-2"></i> Créer un utilisateur
        </a>
    </div>

    <div class="card shadow-sm mb-4 border-0" style="border-radius: 15px;">
        <div class="card-body p-3">
            <form action="{{ route('users.index') }}" method="GET" class="row g-2 align-items-center">
                <div class="col-md-5">
                    <div class="input-group input-group-merge">
                        <span class="input-group-text bg-light border-0"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" name="search" class="form-control bg-light border-0" placeholder="Rechercher par nom, email ou téléphone..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <select name="role" class="form-select bg-light border-0">
                        <option value="">Tous les rôles</option>
                        <option value="Admin" {{ request('role') == 'Admin' ? 'selected' : '' }}>Admin</option>
                        <option value="Agent" {{ request('role') == 'Agent' ? 'selected' : '' }}>Agent</option>
                        <option value="Technicien" {{ request('role') == 'Technicien' ? 'selected' : '' }}>Technicien</option>
                        <option value="Client" {{ request('role') == 'Client' ? 'selected' : '' }}>Client</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100 rounded-3 fw-bold shadow-sm">FILTRER</button>
                </div>
                <div class="col-md-2">
                    <a href="{{ route('users.index') }}" class="btn btn-light w-100 rounded-3">RÉINITIALISER</a>
                </div>
            </form>
        </div>
    </div>

    {{-- Tableau --}}
    <div class="card shadow border-0 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">Nom</th>
                        <th>Email</th>
                        <th>Rôle</th>
                        <th>Téléphone</th>
                        <th>Adresse</th>
                        <th class="text-end pe-4">Statut & Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                    <tr>
                        <td class="ps-4 fw-bold">{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td><span class="badge bg-secondary rounded-pill px-3">{{ $user->role }}</span></td>
                        <td>{{ $user->telephone ?? '-' }}</td>
                        <td>{{ $user->adresse ?? '-' }}</td>
                        <td class="text-end pe-4">
                            <div class="d-flex align-items-center justify-content-end gap-3">
                                <div class="form-check form-switch mb-0" title="{{ $user->actif ? 'Désactiver le compte' : 'Activer le compte' }}">
                                    <input class="form-check-input cursor-pointer" type="checkbox" role="switch" {{ $user->actif ? 'checked' : '' }} 
                                        onchange="toggleUserStatus({{ $user->id }})">
                                </div>
                                <a href="{{ route('users.edit', $user->id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 shadow-sm">
                                    <i class="fas fa-edit me-1"></i> Modifier
                                </a>
                            </div>
                        </td>

                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if($users->hasPages())
        <div class="p-3 bg-white border-top d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
            <div class="small text-muted fw-bold">
                Affichage de <span class="text-primary">{{ $users->firstItem() }}</span> à <span class="text-primary">{{ $users->lastItem() }}</span> sur <span class="text-primary">{{ $users->total() }}</span> utilisateurs
            </div>
            <div class="pagination-sm">
                {{ $users->links() }}
            </div>
        </div>
        @endif

    </div>
</div>

<script>
function toggleUserStatus(userId) {
    fetch(`/gestion-clients/${userId}/toggle-status`, {
        method: 'PATCH',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    }).then(response => {
        if(!response.ok) alert('Erreur lors du changement de statut');
    });
}
</script>
@endsection
