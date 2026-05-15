@extends('layouts.app')

@section('title', 'Réinitialiser le mot de passe')

@section('content')
<div class="container d-flex align-items-center justify-content-center" style="min-height: 80vh;">
    <div class="card shadow-lg border-0 p-5" style="width: 450px; border-radius: 20px;">
        <h2 class="text-center fw-bold mb-4">Réinitialisation</h2>
        <p class="text-muted text-center small mb-4">Entrez votre adresse email pour recevoir un lien de réinitialisation.</p>

        @if (session('status'))
            <div class="alert alert-success small mb-4" role="alert">
                {{ session('status') }}
            </div>
        @endif

        <form action="{{ route('password.email') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="form-label small fw-bold">Adresse email</label>
                <input type="email" name="email" class="form-control py-2 @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="votre@email.com" required autofocus>
                @error('email')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <div class="d-grid gap-2 mb-3">
                <button type="submit" class="btn btn-primary py-2 fw-bold" style="background-color: #2563eb; border: none; border-radius: 10px;">
                    Envoyer le lien
                </button>
            </div>
            <div class="text-center">
                <a href="{{ route('login') }}" class="text-decoration-none small text-muted">Retour à la connexion</a>
            </div>
        </form>
    </div>
</div>
@endsection
