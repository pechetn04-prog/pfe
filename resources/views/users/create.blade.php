@extends('layouts.app')

@section('title', 'Créer un utilisateur')

@section('content')
<div class="container-fluid" style="max-width: 700px;">
    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('users.index') }}" class="btn btn-outline-secondary btn-sm me-3">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h1 class="h4 fw-bold mb-0">Créer un utilisateur</h1>
    </div>

    @if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <form action="{{ route('users.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-semibold">Nom complet <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" value="{{ old('name') }}" required placeholder="Ex: Jean Dupont">
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control" value="{{ old('email') }}" required placeholder="email@exemple.com">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Téléphone</label>
                        <input type="text" name="telephone" class="form-control" value="{{ old('telephone') }}" placeholder="0X XX XX XX XX">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Rôle <span class="text-danger">*</span></label>
                    <select name="role" class="form-select" required id="roleSelect" onchange="toggleSpecialite(this.value)">
                        <option value="">Sélectionner un rôle</option>
                        <option value="Admin" {{ old('role') == 'Admin' ? 'selected' : '' }}>Administrateur</option>
                        <option value="Agent" {{ old('role') == 'Agent' ? 'selected' : '' }}>Agent SAV</option>
                        <option value="Technicien" {{ old('role') == 'Technicien' ? 'selected' : '' }}>Technicien</option>
                        <option value="Client" {{ old('role') == 'Client' ? 'selected' : '' }}>Client</option>
                    </select>
                </div>

                {{-- Spécialités Technicien --}}
                <div class="card bg-light border-0 mb-3" id="specialiteField" style="display:none; border-radius: 12px;">
                    <div class="card-body">
                        <label class="form-label fw-bold small text-uppercase mb-2 text-primary">Spécialités Techniques</label>
                        @php 
                            $specialitesSAV = [
                                'Écran & Affichage', 'Batterie & Alimentation', 
                                'Connectique & Ports', 'Caméra',
                                'Audio', 'Connectivité',
                                'Logiciel & Système', 'Dommages Physiques',
                                'Sécurité & Accès'
                            ];
                        @endphp
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

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Mot de passe <span class="text-danger">*</span></label>
                        <input type="password" name="password" class="form-control" required placeholder="••••••••">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Confirmer le mot de passe <span class="text-danger">*</span></label>
                        <input type="password" name="password_confirmation" class="form-control" required placeholder="••••••••">
                    </div>
                </div>

                <div class="d-flex gap-2 mt-4">
                    <button type="submit" class="btn btn-primary px-4 fw-bold">
                        <i class="fas fa-user-plus me-2"></i> Créer l'utilisateur
                    </button>
                    <a href="{{ route('users.index') }}" class="btn btn-outline-secondary px-4 fw-bold">Annuler</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function toggleSpecialite(role) {
    document.getElementById('specialiteField').style.display = role === 'Technicien' ? 'block' : 'none';
}
toggleSpecialite('{{ old('role') }}');
</script>
@endsection
