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

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show mb-4 text-start small border-0" 
                     style="border-radius: 12px; font-size: 0.85rem; background-color: #fef2f2; color: #dc2626; border-left: 4px solid #dc2626 !important; padding-right: 2.5rem;">
                    <div class="d-flex align-items-start">
                        <i class="fas fa-exclamation-triangle me-2 mt-1"></i>
                        <div>{{ session('error') }}</div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" style="font-size: 0.7rem; padding: 1.15rem;"></button>
                </div>
            @endif
 
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