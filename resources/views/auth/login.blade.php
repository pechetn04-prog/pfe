@extends('layouts.app')

@section('title', 'Connexion')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/auth-premium.css') }}">
@endpush

@section('content')
<div class="container d-flex align-items-center justify-content-center" style="min-height: 80vh;">
    <div class="card shadow-lg border-0 p-5" style="width: 450px; border-radius: 20px;">
        <h2 class="text-center fw-bold mb-4">Connexion</h2>

        <form action="{{ route('login.post') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label small fw-bold">Adresse email</label>
                <input type="email" name="email" class="form-control py-2" placeholder="Entrez votre email" required autofocus>
            </div>

            <div class="mb-4">
                <label class="form-label small fw-bold">Mot de passe</label>
                <input type="password" name="password" class="form-control py-2" placeholder="Entrez votre mot de passe" required>
            </div>

            <div class="d-grid gap-2 mb-4">
                <button type="submit" class="btn btn-primary py-2 fw-bold" style="background-color: #2563eb; border: none; border-radius: 10px;">
                    Se connecter
                </button>
            </div>
        </form>

        <div class="text-center">
            <p class="small text-muted mb-2">Vous voulez juste vérifier l'état d'un appareil ?</p>
            <a href="{{ route('client.suivi') }}" class="btn btn-light w-100 rounded-pill py-2 border small fw-bold text-primary mb-3">
                <i class="fas fa-search me-2"></i> Suivre ma réparation rapidement
            </a>
            <a href="{{ route('password.request') }}" class="text-decoration-none small text-muted">Mot de passe oublié ?</a>
        </div>
    </div>
</div>
@endsection
