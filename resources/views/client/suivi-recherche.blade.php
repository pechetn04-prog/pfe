<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Suivi de réparation — Maison Tel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/suivi.css') }}">
</head>
<body>

    <div class="search-card">
        
        <div class="icon-box">
            <i class="fas fa-microchip"></i>
        </div>

        <h1>Suivi de réparation</h1>
        <p class="subtitle">Consultez l'état d'avancement de votre appareil en temps réel.</p>

        @if(session('error'))
            <div class="alert alert-danger border-0 rounded-3 small py-2 mb-3 shadow-sm">
                <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('client.search') }}" method="POST">
            @csrf
            <div class="input-wrapper">
                <i class="fas fa-search"></i>
                <input type="text" name="search" class="form-control-custom" 
                       placeholder="N° Ticket, IMEI ou Téléphone" 
                       value="{{ old('search') }}" required autofocus>
            </div>

            <button type="submit" class="btn-primary-custom">
                Vérifier le statut
            </button>
        </form>

        <p class="help-text">
            Besoin d'aide ? <a href="#">Contactez le support</a>
        </p>

        <a href="{{ route('login') }}" class="client-link">
            <i class="fas fa-user-circle"></i> Espace Client
        </a>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
