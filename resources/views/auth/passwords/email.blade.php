@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/auth-premium.css') }}">
@endpush

@section('content')
<div class="auth-page">
    <div class="auth-card">
        <div class="auth-header">
            <h2>Réinitialisation</h2>
            <p>Entrez votre e-mail pour recevoir le lien de secours.</p>
        </div>

        @if (session('status'))
            <div class="alert alert-success alert-premium mb-4" role="alert">
                {{ session('status') }}
            </div>
        @endif

        <form action="{{ route('password.email') }}" method="POST" class="auth-form">
            @csrf
            <div class="mb-4">
                <label class="form-label">Adresse e-mail</label>
                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="votre@email.com" required autofocus>
                @error('email')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <button type="submit" class="btn-auth-submit mb-3">
                Envoyer le lien
            </button>
            
            <div class="text-center mt-4 pt-3 border-top">
                <a href="{{ route('login') }}" class="auth-link">
                    <i class="fas fa-arrow-left me-2"></i> Retour à la connexion
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
