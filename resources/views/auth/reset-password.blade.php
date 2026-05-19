@extends('layouts.app')
 
@section('title', 'Réinitialiser le mot de passe')
 

 
@section('content')
<div class="auth-page">
    <div class="auth-card">
        <div class="auth-header">
            <h2>Nouveau mot de passe</h2>
            <p>Définissez votre nouveau mot de passe sécurisé.</p>
        </div>
 
        <form action="{{ route('password.update') }}" method="POST" class="auth-form">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
 
            <div class="mb-3">
                <label class="form-label">Adresse e-mail</label>
                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ $email ?? old('email') }}" required readonly>
                @error('email')
                    <span class="invalid-feedback small fw-bold mt-2 d-block auth-error-feedback" role="alert">
                        {{ $message }}
                    </span>
                @enderror
            </div>
 
            <div class="mb-3">
                <label class="form-label">Nouveau mot de passe</label>
                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="••••••••" required autofocus>
                @error('password')
                    <span class="invalid-feedback small fw-bold mt-2 d-block auth-error-feedback" role="alert">
                        {{ $message }}
                    </span>
                @enderror
            </div>
 
            <div class="mb-4">
                <label class="form-label">Confirmer le mot de passe</label>
                <input type="password" name="password_confirmation" class="form-control" placeholder="••••••••" required>
            </div>
 
            <button type="submit" class="btn-auth-submit">
                Enregistrer le mot de passe
            </button>
        </form>
    </div>
</div>
@endsection
