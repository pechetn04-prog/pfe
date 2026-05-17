@extends('layouts.app')

@section('title', 'Créer un utilisateur')

@section('content')
<div class="container-fluid" style="max-width: 900px;">
    
    {{-- Titres Hiérarchiques --}}
    <div class="mb-4">
        <div class="d-flex align-items-center mb-1">
            <a href="{{ route('users.index') }}" class="text-decoration-none text-muted small fw-bold text-uppercase">
                <i class="fas fa-users me-1"></i> Utilisateurs
            </a>
        </div>
        <h1 class="h3 fw-bold mb-0">Créer un utilisateur</h1>
        <small class="text-muted">Enregistrez un nouveau compte dans le système SAV</small>
    </div>

    @if($errors->any())
    <div class="alert alert-danger border-0 shadow-sm mb-4">
        <ul class="mb-0 small">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
    @endif

    <div class="card border-0 shadow-sm" style="border-radius: 15px;">
        <div class="card-body p-4">
            <form action="{{ route('users.store') }}" method="POST">
                @csrf
                
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-muted small text-uppercase">Nom complet <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control bg-light border-0" value="{{ old('name') }}" required placeholder="Ex: Jean Dupont">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-muted small text-uppercase">Adresse Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control bg-light border-0" value="{{ old('email') }}" required placeholder="email@exemple.com">
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-muted small text-uppercase">Rôle <span class="text-danger">*</span></label>
                        <select name="role" class="form-select bg-light border-0" required id="roleSelect" onchange="toggleSpecialite(this.value)">
                            @if(auth()->user()->role === 'Admin')
                                <option value="">Sélectionner un rôle</option>
                                <option value="Admin" {{ old('role') == 'Admin' ? 'selected' : '' }}>Administrateur</option>
                                <option value="Agent" {{ old('role') == 'Agent' ? 'selected' : '' }}>Agent SAV</option>
                                <option value="Technicien" {{ old('role') == 'Technicien' ? 'selected' : '' }}>Technicien</option>
                                <option value="Client" {{ old('role') == 'Client' ? 'selected' : '' }}>Client</option>
                            @else
                                <option value="Client" selected>Client</option>
                            @endif
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-muted small text-uppercase">Téléphone</label>
                        <input type="text" name="telephone" class="form-control bg-light border-0" value="{{ old('telephone') }}" placeholder="Ex: 05 55 55 55 55">
                    </div>
                </div>

                {{-- Spécialités Technicien (Uniquement visible pour l'Admin s'il choisit Technicien) --}}
                @if(auth()->user()->role === 'Admin')
                <div class="card bg-light border-0 mb-4" id="specialiteField" style="display:none; border-radius: 12px;">
                    <div class="card-body">
                        <label class="form-label fw-bold small text-uppercase mb-2 text-primary">Spécialités Techniques</label>

                        <div class="row">
                            @foreach($specialitesSAV as $spec)
                            <div class="col-md-6 mb-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="specialites[]" value="{{ $spec }}" id="spec_{{ $loop->index }}">
                                    <label class="form-check-label small" for="spec_{{ $loop->index }}">{{ $spec }}</label>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-muted small text-uppercase">Mot de passe <span class="text-danger">*</span></label>
                        <input type="password" name="password" class="form-control bg-light border-0" required placeholder="••••••••">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-muted small text-uppercase">Confirmer le mot de passe <span class="text-danger">*</span></label>
                        <input type="password" name="password_confirmation" class="form-control bg-light border-0" required placeholder="••••••••">
                    </div>
                </div>

                <hr class="my-4 opacity-25">

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary px-5 rounded-pill shadow-sm fw-bold">
                        <i class="fas fa-user-plus me-2"></i> Créer le compte
                    </button>
                    <a href="{{ route('users.index') }}" class="btn btn-light px-4 rounded-pill">Annuler</a>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
    <script src="{{ asset('js/users_form.js') }}"></script>
@endpush
@endsection
