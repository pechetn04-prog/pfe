@extends('layouts.app')

@section('title', 'Connexion')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/auth-premium.css') }}">
@endpush

@section('content')
<div class="auth-page">
    <div class="auth-card">
        <div class="auth-header">
            <h2>Connexion</h2>
            <p>Accédez à votre espace professionnel</p>
        </div>

        <form action="{{ route('login.post') }}" method="POST" class="auth-form">
            @csrf
            <div class="mb-3">
                <label class="form-label">Adresse e-mail</label>
                <input type="email" name="email" class="form-control" placeholder="nom@exemple.com" required autofocus>
            </div>

            <div class="mb-4">
                <label class="form-label">Mot de passe</label>
                <input type="password" name="password" class="form-control" placeholder="••••••••" required>
            </div>

            <button type="submit" class="btn-auth-submit">
                Se connecter
            </button>
        </form>

        <div class="auth-footer">
            <p class="small text-muted mb-3">Besoin de suivre un dossier ?</p>
            <a href="{{ route('client.suivi') }}" class="btn-tracking-alt">
                <i class="fas fa-search me-2"></i> Suivi Client Rapide
            </a>
            <a href="{{ route('password.request') }}" class="auth-link">Mot de passe oublié ?</a>
        </div>
    </div>
</div>
@endsection
