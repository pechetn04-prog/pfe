@extends('layouts.app')

@section('title', 'Modifier — ' . $user->name)

@section('content')
<div class="container-fluid" style="max-width: 700px;">
    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('users.index') }}" class="btn btn-outline-secondary btn-sm me-3">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h1 class="h4 fw-bold mb-0">Modifier — {{ $user->name }}</h1>
    </div>

    @if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
    @endif

    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <form action="{{ route('users.update', $user->id) }}" method="POST">
                @csrf @method('PUT')
                <div class="mb-3">
                    <label class="form-label fw-semibold">Nom complet</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Email</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Rôle</label>
                    <select name="role" class="form-select" id="roleSelect" onchange="toggleSpecialite(this.value)">
                        <option value="Admin"     {{ $user->role == 'Admin'      ? 'selected' : '' }}>Administrateur</option>
                        <option value="Agent"     {{ $user->role == 'Agent'      ? 'selected' : '' }}>Agent SAV</option>
                        <option value="Technicien"{{ $user->role == 'Technicien' ? 'selected' : '' }}>Technicien</option>
                        <option value="Client"    {{ $user->role == 'Client'     ? 'selected' : '' }}>Client</option>
                    </select>
                </div>
                {{-- Spécialités Technicien --}}
                <div class="card bg-light border-0 mb-3" id="specialiteField" style="display:none; border-radius: 12px;">
                    <div class="card-body">
                        <label class="form-label fw-bold small text-uppercase mb-2 text-primary">Spécialités Techniques</label>
                        @php $currentSpecs = explode(', ', $user->specialite ?? ''); @endphp
                        <div class="row">
                            @foreach(['Écrans / LCD', 'Micro-soudure', 'Logiciel / Flash', 'Batteries', 'Connecteurs', 'iOS / Apple', 'Android / Samsung'] as $spec)
                            <div class="col-md-4 mb-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="specialites[]" value="{{ $spec }}" id="spec_{{ $loop->index }}" {{ in_array($spec, $currentSpecs) ? 'checked' : '' }}>
                                    <label class="form-check-label small" for="spec_{{ $loop->index }}">{{ $spec }}</label>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Téléphone</label>
                    <input type="text" name="telephone" class="form-control" value="{{ old('telephone', $user->telephone) }}">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Nouveau mot de passe <span class="text-muted small">(laisser vide pour ne pas changer)</span></label>
                    <input type="password" name="password" class="form-control" autocomplete="new-password">
                </div>
                <div class="mb-4">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="actif" id="actifSwitch" value="1"
                            {{ $user->actif ? 'checked' : '' }}>
                        <label class="form-check-label" for="actifSwitch">Compte actif</label>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary px-4">Enregistrer</button>
                    <a href="{{ route('users.index') }}" class="btn btn-outline-secondary px-4">Annuler</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function toggleSpecialite(role) {
    document.getElementById('specialiteField').style.display = role === 'Technicien' ? 'block' : 'none';
}
toggleSpecialite('{{ $user->role }}');
</script>
@endsection
