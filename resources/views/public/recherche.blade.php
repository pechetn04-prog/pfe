<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Suivi de réparation — Maison Tel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/public_search.css') }}">
</head>
<body>
 
    <div class="search-container">
        <div class="card-pro">
            <div class="logo-container">
                <i class="fas fa-microchip"></i>
            </div>
 
            <h1>Suivi de réparation</h1>
            <p class="subtitle">Consultez l'état d'avancement de votre appareil en temps réel.</p>
 
            @if(session('error'))
                <div class="alert alert-danger border-0 rounded-3 small mb-4 py-2">
                    <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
                </div>
            @endif
 
            <form action="{{ route('client.search') }}" method="POST">
                @csrf
                <div class="mb-4 position-relative">
                    <i class="fas fa-search position-absolute text-muted" style="left: 16px; top: 50%; transform: translateY(-50%);"></i>
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
 
</body>
</html>
