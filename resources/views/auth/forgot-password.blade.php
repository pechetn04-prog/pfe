@extends('layouts.app')
 
@section('title', 'Mot de passe oublié')
 
@push('styles')
<link rel="stylesheet" href="{{ asset('css/auth-premium.css') }}">
@endpush
 
@section('content')
<div class="auth-page">
    <div class="auth-card">
        <div class="auth-header">
            <h2>Récupération</h2>
            <p>Saisissez votre email pour recevoir un lien de réinitialisation.</p>
        </div>
 
        @if (session('status'))
            <div class="alert alert-success alert-premium mb-4">
                <i class="fas fa-check-circle me-2"></i> {{ session('status') }}
            </div>
        @endif
 
        <form action="{{ route('password.email') }}" method="POST" class="auth-form">
            @csrf
            <div class="mb-4">
                <label class="form-label">Adresse e-mail</label>
                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="nom@exemple.com" required autofocus>
                @error('email')
                    <span class="invalid-feedback small fw-bold mt-2 d-block" role="alert" style="color: #dc2626;">
                        {{ $message }}
                    </span>
                @enderror
            </div>
 
            <button type="submit" class="btn-auth-submit">
                Envoyer le lien
            </button>
        </form>
 
        <div class="auth-footer">
            <a href="{{ route('login') }}" class="auth-link">
                <i class="fas fa-arrow-left me-2"></i> Retour à la connexion
            </a>
        </div>
    </div>
</div>
@endsection
