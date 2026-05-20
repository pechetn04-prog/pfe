@extends('layouts.app')

@section('title', 'Suivi de réparation')

@section('body-class', 'public-search')

@section('content')
    <div class="search-container">
        <div class="card-pro">
            <div class="logo-container">
                <i class="fas fa-microchip"></i>
            </div>
 
            <h1>Suivi de réparation</h1>
            <p class="subtitle">Consultez l'état d'avancement de votre appareil en temps réel.</p>

 
            <form action="{{ route('client.search') }}" method="POST">
                @csrf
                <div class="mb-4 position-relative">
                    <i class="fas fa-search position-absolute text-muted search-input-icon"></i>
                    <input type="text" name="search" class="input-pro ps-5 mb-0" 
                           placeholder="N° Ticket, IMEI ou Téléphone" 
                           value="{{ old('search') }}" required autofocus>
                </div>
 
                <button type="submit" class="btn-pro">
                    Vérifier le statut
                </button>
            </form>
 
            <div class="footer">
                <p class="mb-2">
                    Besoin d'aide ? <a href="#">Contactez le support</a>
                </p>
                <div class="mt-3 pt-3 border-top">
                    <a href="{{ route('login') }}" class="text-muted small">
                        <i class="fas fa-user-circle me-1"></i> Espace Client
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection