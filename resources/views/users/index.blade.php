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

    {{-- Filtres --}}
    <div class="card shadow mb-4 border-0">
        <div class="card-body">
            <form action="{{ route('users.index') }}" method="GET" class="row g-3">
                <div class="col-md-3">
                    <select name="role" class="form-select">
                        <option value="">Tous les rôles</option>
                        <option value="Admin" {{ request('role') == 'Admin' ? 'selected' : '' }}>Admin</option>
                        <option value="Agent" {{ request('role') == 'Agent' ? 'selected' : '' }}>Agent</option>
                        <option value="Technicien" {{ request('role') == 'Technicien' ? 'selected' : '' }}>Technicien</option>
                        <option value="Client" {{ request('role') == 'Client' ? 'selected' : '' }}>Client</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Filtrer</button>
                </div>
                <div class="col-md-2">
                    <a href="{{ route('users.index') }}" class="btn btn-outline-secondary w-100">Réinitialiser</a>
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
                        <th class="text-center">Actif</th>
                        <th>Téléphone</th>
                        <th>Adresse</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                    <tr>
                        <td class="ps-4 fw-bold">{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td><span class="badge bg-secondary rounded-pill px-3">{{ $user->role }}</span></td>
                        <td class="text-center">
                            <div class="form-check form-switch d-inline-block">
                                <input class="form-check-input" type="checkbox" role="switch" {{ $user->actif ? 'checked' : '' }} 
                                    onchange="toggleUserStatus({{ $user->id }})">
                            </div>
                        </td>
                        <td>{{ $user->telephone ?? '-' }}</td>
                        <td>{{ $user->adresse ?? '-' }}</td>
                        <td class="text-end pe-4">
                            <a href="{{ route('users.edit', $user->id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">Modifier</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
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
