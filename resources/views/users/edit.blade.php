@extends('layouts.app')

@section('title', 'Modifier — ' . $user->name)

@section('content')
<div class="container-fluid" style="max-width: 900px;">

    <div class="mb-4">
        <div class="d-flex align-items-center mb-1">
            <a href="{{ route('users.index') }}" class="text-decoration-none text-muted small fw-bold text-uppercase">
                <i class="fas fa-users me-1"></i> Utilisateurs
            </a>
        </div>
        <h1 class="h3 fw-bold mb-0">Modifier le profil</h1>
        <small class="text-muted">Mise à jour des informations de <strong>{{ $user->name }}</strong></small>
    </div>


    @if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <form action="{{ route('users.update', $user->id) }}" method="POST">
                @csrf @method('PUT')
                
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-muted small text-uppercase">Nom complet</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required placeholder="Ex: Jean Dupont">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-muted small text-uppercase">Adresse Email</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required placeholder="email@exemple.com">
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-muted small text-uppercase">Rôle utilisateur</label>
                        <select name="role" class="form-select" id="roleSelect" onchange="toggleSpecialite(this.value)">
                            <option value="Admin"     {{ $user->role == 'Admin'      ? 'selected' : '' }}>Administrateur</option>
                            <option value="Agent"     {{ $user->role == 'Agent'      ? 'selected' : '' }}>Agent SAV</option>
                            <option value="Technicien"{{ $user->role == 'Technicien' ? 'selected' : '' }}>Technicien</option>
                            <option value="Client"    {{ $user->role == 'Client'     ? 'selected' : '' }}>Client</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-muted small text-uppercase">Téléphone</label>
                        <input type="text" name="telephone" class="form-control" value="{{ old('telephone', $user->telephone) }}" placeholder="Ex: 05 55 55 55 55">
                    </div>
                </div>

                {{-- Spécialités Technicien --}}
                <div class="card bg-light border-0 mb-4" id="specialiteField" style="display:none; border-radius: 12px;">
                    <div class="card-body">
                        <label class="form-label fw-bold small text-uppercase mb-2 text-primary">Spécialités Techniques</label>

                        <div class="row">
                            @foreach($specialitesSAV as $spec)
                            <div class="col-md-6 mb-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="specialites[]" value="{{ $spec }}" id="spec_{{ $loop->index }}" {{ in_array($spec, $currentSpecs) ? 'checked' : '' }}>
                                    <label class="form-check-label small" for="spec_{{ $loop->index }}">{{ $spec }}</label>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-muted small text-uppercase">Nouveau mot de passe</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0"><i class="fas fa-lock text-muted"></i></span>
                            <input type="password" name="password" class="form-control border-start-0" placeholder="Laisser vide pour ne pas changer" autocomplete="new-password">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-muted small text-uppercase">Confirmer le nouveau mot de passe</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0"><i class="fas fa-check-circle text-muted"></i></span>
                            <input type="password" name="password_confirmation" class="form-control border-start-0" placeholder="Répétez le mot de passe" autocomplete="new-password">
                        </div>
                    </div>
                </div>

                <hr class="my-4 opacity-25">

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary px-5 rounded-pill shadow-sm">
                        <i class="fas fa-save me-2"></i> Enregistrer les modifications
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


