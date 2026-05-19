@extends('layouts.app')

@section('title', 'Mon Profil')

@section('content')
    <div class="container-fluid profile-container">
        <div class="mb-4">
            <h1 class="h3 fw-bold mb-1">Mon Profil</h1>
            <p class="text-muted">Gérez vos informations personnelles et votre mot de passe.</p>
        </div>

        @if($errors->any())
            <div class="alert alert-danger border-0 shadow-sm mb-4">
                <ul class="mb-0 small">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if(session('success'))

            <div class="alert alert-success border-0 shadow-sm mb-4">
                <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            </div>
        @endif

        <div class="card border-0 shadow-sm profile-card">
            <div class="card-body p-4">
                <form action="{{ route('profile.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    {{-- Section Informations --}}
                    <div class="mb-4">
                        <label class="form-label fw-bold text-muted small text-uppercase">Nom complet</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-0"><i class="fas fa-user text-muted"></i></span>
                            <input type="text" name="name" class="form-control bg-light border-0"
                                value="{{ old('name', auth()->user()->name) }}" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold text-muted small text-uppercase">Adresse Email</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-0"><i
                                    class="fas fa-envelope text-muted"></i></span>
                            <input type="email" name="email" class="form-control bg-light border-0"
                                value="{{ old('email', auth()->user()->email) }}" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold text-muted small text-uppercase">Téléphone</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-0"><i class="fas fa-phone text-muted"></i></span>
                            <input type="text" name="telephone" class="form-control bg-light border-0"
                                value="{{ old('telephone', auth()->user()->telephone) }}">
                        </div>
                    </div>

                    <hr class="my-4 opacity-25">

                    {{-- Section Sécurité --}}
                    <div class="mb-3">
                        <label class="form-label fw-bold text-muted small text-uppercase">Nouveau mot de passe</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-0"><i class="fas fa-lock text-muted"></i></span>
                            <input type="password" name="password" class="form-control bg-light border-0"
                                placeholder="Laissez vide pour ne pas changer">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold text-muted small text-uppercase">Confirmer le mot de passe</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-0"><i
                                    class="fas fa-check-circle text-muted"></i></span>
                            <input type="password" name="password_confirmation" class="form-control bg-light border-0"
                                placeholder="Répétez le mot de passe">
                        </div>
                    </div>


                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary py-2 fw-bold rounded-pill shadow-sm">
                            <i class="fas fa-save me-2"></i> Mettre à jour mon profil
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection