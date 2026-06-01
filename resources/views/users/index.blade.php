@extends('layouts.app')

@section('title', 'Liste des utilisateurs')



@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 users-title mb-0">Gestion des Utilisateurs</h1>
                <small class="text-muted fw-bold">Contrôle des accès et comptes système</small>
            </div>
            @if(auth()->user()->role === 'Admin')
                <a href="{{ route('users.create') }}" class="btn btn-primary px-4 shadow-sm fw-bold">
                    <i class="fas fa-plus me-2"></i> Créer un compte
                </a>
            @endif
        </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm card-user-stat">
                <div class="card-body p-3 d-flex align-items-center">
                    <div class="rounded-3 d-flex align-items-center justify-content-center me-3 icon-wrapper-stat icon-wrapper-blue">
                        <i class="fas fa-users text-primary fs-5"></i>
                    </div>
                    <div>
                        <div class="h3 fw-bold mb-0">{{ $stats['total'] }}</div>
                        <div class="small text-uppercase fw-bold text-muted text-caps-stat">Utilisateurs</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm card-user-stat">
                <div class="card-body p-3 d-flex align-items-center">
                    <div class="rounded-3 d-flex align-items-center justify-content-center me-3 icon-wrapper-stat icon-wrapper-green">
                        <i class="fas fa-user-check text-success fs-5"></i>
                    </div>
                    <div>
                        <div class="h3 fw-bold mb-0 text-success">{{ $stats['actifs'] }}</div>
                        <div class="small text-uppercase fw-bold text-muted text-caps-stat">Comptes Actifs</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm card-user-stat">
                <div class="card-body p-3 d-flex align-items-center">
                    <div class="rounded-3 d-flex align-items-center justify-content-center me-3 icon-wrapper-stat icon-wrapper-red">
                        <i class="fas fa-user-tie text-danger fs-5"></i>
                    </div>
                    <div>
                        <div class="h3 fw-bold mb-0 text-danger">{{ $stats['clients'] }}</div>
                        <div class="small text-uppercase fw-bold text-muted text-caps-stat">Clients SAV</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm card-user-stat">
                <div class="card-body p-3 d-flex align-items-center">
                    <div class="rounded-3 d-flex align-items-center justify-content-center me-3 icon-wrapper-stat icon-wrapper-orange">
                        <i class="fas fa-user-shield text-warning fs-5"></i>
                    </div>
                    <div>
                        <div class="h3 fw-bold mb-0 text-warning">{{ $stats['staff'] }}</div>
                        <div class="small text-uppercase fw-bold text-muted text-caps-stat">Équipe Système</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="filter-container shadow-sm mb-4">
        <form action="{{ route('users.index') }}" method="GET" class="row g-3 align-items-center">
            <div class="col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-transparent border-0 pe-0"><i
                            class="fas fa-search text-muted"></i></span>
                    <input type="text" name="search" class="form-control filter-input border-0"
                        placeholder="Nom, email ou téléphone..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-3">
                <select name="role" class="form-select filter-select">
                    <option value="">Tous les rôles</option>
                    <option value="Admin" {{ request('role') == 'Admin' ? 'selected' : '' }}>Administrateur</option>
                    <option value="Agent" {{ request('role') == 'Agent' ? 'selected' : '' }}>Agent SAV</option>
                    <option value="Technicien" {{ request('role') == 'Technicien' ? 'selected' : '' }}>Technicien</option>
                    <option value="Client" {{ request('role') == 'Client' ? 'selected' : '' }}>Client</option>
                </select>
            </div>
            <div class="col-md-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-grow-1 shadow-sm">
                    <i class="fas fa-filter me-2"></i> FILTRER
                </button>
                <a href="{{ route('users.index') }}" class="btn btn-light border-0 fw-bold btn-raz-users">
                    <i class="fas fa-undo me-2"></i> Réinitialiser
                </a>
            </div>
        </form>
    </div>

    <div class="card border-0 shadow-sm overflow-hidden card-users-list">
        <div class="table-responsive">
            <table class="table mb-0 align-middle">
                <thead>
                    <tr class="bg-light">
                        <th class="ps-4">Nom et Prénom</th>
                        <th>Email</th>
                        <th>Rôle</th>
                        <th>Contact</th>
                        <th class="text-end pe-4">État & Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                        <tr>
                            <td class="ps-4">
                                <div class="user-name fw-bold">{{ $user->name }}</div>
                            </td>
                            <td>
                                <div class="user-email small text-muted">{{ $user->email }}</div>
                            </td>
                            <td>
                                <span class="badge role-badge role-{{ strtolower($user->role) }}">{{ $user->role }}</span>
                            </td>
                            <td>
                                <div class="small fw-bold text-dark">{{ $user->telephone ?? '—' }}</div>
                            </td>
                          
                            <td class="text-end pe-4">
                                <div class="d-flex align-items-center justify-content-end gap-3">
                                    <div class="form-check form-switch mb-0"
                                        title="{{ $user->actif ? 'Désactiver' : 'Activer' }}">
                                        <input class="form-check-input status-switch cursor-pointer" type="checkbox"
                                            role="switch" {{ $user->actif ? 'checked' : '' }}
                                            onchange="toggleUserStatus({{ $user->id }})">
                                    </div>
                                    <a href="{{ route('users.edit', $user->id) }}"
                                        class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-bold" title="Modifier">
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
            <div class="p-4 bg-white border-top d-flex justify-content-between align-items-center">
                <div class="small text-muted fw-bold">
                    Affichage de <span class="text-primary">{{ $users->firstItem() }}</span> à <span
                        class="text-primary">{{ $users->lastItem() }}</span> sur {{ $users->total() }}
                </div>
                <div>
                    {{ $users->links() }}
                </div>
            </div>
        @endif
    </div>
    </div>

    <script src="{{ asset('js/users_index.js') }}"></script>
@endsection