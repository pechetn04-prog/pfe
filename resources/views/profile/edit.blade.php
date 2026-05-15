@extends('layouts.app')

@section('title', 'Mon Profil')

@section('content')
<div class="container" style="max-width: 680px;">

    <div class="d-flex align-items-center mb-4">
        <div>
            <h1 class="h4 fw-bold mb-0">Mon Profil</h1>
            <small class="text-muted">{{ auth()->user()->role }} — {{ auth()->user()->email }}</small>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <div class="card border-0 shadow-sm" style="border-radius: 14px;">
        {{-- Avatar + infos --}}
        <div class="card-body pb-0">
            <div class="d-flex align-items-center gap-4 mb-4 p-3 rounded-3" style="background: linear-gradient(135deg, #eff6ff, #dbeafe);">
                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold"
                    style="width: 64px; height: 64px; font-size: 1.5rem;">
                    {{ strtoupper(substr($user->name, 0, 2)) }}
                </div>
                <div>
                    <div class="fw-bold fs-5">{{ $user->name }}</div>
                    <div class="text-muted small">{{ $user->email }}</div>
                    <span class="badge bg-primary rounded-pill mt-1">{{ $user->role }}</span>
                    @if($user->specialite)
                        <span class="badge bg-secondary rounded-pill mt-1">{{ $user->specialite }}</span>
                    @endif
                </div>
            </div>
        </div>

        <div class="card-body pt-0">
            <form action="{{ route('profile.update') }}" method="POST">
                @csrf @method('PUT')

                <div class="mb-3">
                    <label class="form-label fw-semibold">Nom complet</label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                        value="{{ old('name', $user->name) }}" required>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Email</label>
                    <input type="email" class="form-control bg-light" value="{{ $user->email }}" disabled>
                    <small class="text-muted">L'email ne peut pas être modifié ici.</small>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Téléphone</label>
                    <input type="text" name="telephone" class="form-control"
                        value="{{ old('telephone', $user->telephone) }}"
                        placeholder="Ex: 07 XX XX XX XX">
                </div>

                <hr class="my-4">
                <div class="fw-semibold mb-3 text-muted small text-uppercase">Changer de mot de passe</div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Nouveau mot de passe</label>
                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                        placeholder="Laisser vide pour ne pas changer">
                    @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-4">
                    <label class="form-label fw-semibold">Confirmer le mot de passe</label>
                    <input type="password" name="password_confirmation" class="form-control"
                        placeholder="Répéter le nouveau mot de passe">
                </div>

                <button type="submit" class="btn btn-primary px-5">
                    <i class="fas fa-save me-2"></i> Enregistrer les modifications
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
