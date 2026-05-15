<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Suivi de Dossier SAV — Maison Tel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Outfit', sans-serif; background: linear-gradient(135deg, #0f172a 0%, #1e3a5f 100%); min-height: 100vh; }
        .search-card { background: white; border-radius: 20px; max-width: 560px; margin: 0 auto; padding: 2.5rem; box-shadow: 0 25px 60px rgba(0,0,0,0.3); }
        .brand { color: white; text-align: center; margin-bottom: 2rem; }
        .brand h1 { font-size: 2rem; font-weight: 800; }
        .brand p { opacity: 0.7; font-size: 0.95rem; }
        .search-input { border-radius: 12px; border: 2px solid #e2e8f0; padding: 14px 18px; font-size: 1rem; transition: border-color 0.2s; }
        .search-input:focus { border-color: #2563eb; box-shadow: none; }
        .btn-search { background: linear-gradient(135deg, #2563eb, #1d4ed8); color: white; border: none; border-radius: 12px; padding: 14px 24px; font-weight: 600; width: 100%; margin-top: 12px; font-size: 1rem; transition: transform 0.15s, box-shadow 0.15s; }
        .btn-search:hover { transform: translateY(-1px); box-shadow: 0 8px 20px rgba(37,99,235,0.4); color: white; }
        .features { display: flex; justify-content: center; gap: 2rem; margin-top: 1.5rem; }
        .feature { display: flex; align-items: center; gap: 6px; color: rgba(255,255,255,0.7); font-size: 0.82rem; }
        .divider { text-align: center; margin: 1.5rem 0; position: relative; }
        .divider::before { content: ''; position: absolute; top: 50%; left: 0; right: 0; height: 1px; background: #e2e8f0; }
        .divider span { background: white; padding: 0 12px; position: relative; color: #94a3b8; font-size: 0.85rem; }
    </style>
</head>
<body class="d-flex align-items-center py-5">
<div class="container">

    <div class="brand">
        <h1>📱 Maison Tel</h1>
        <p>Service Après-Vente — Suivi en ligne de votre réparation</p>
    </div>

    <div class="search-card">
        <h2 class="fw-bold text-center mb-1" style="font-size:1.4rem;">Suivre mon dossier</h2>
        <p class="text-muted text-center small mb-4">Entrez votre numéro de dossier ou l'IMEI de l'appareil</p>

        @if(session('error'))
        <div class="alert alert-danger rounded-3 py-2">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
        </div>
        @endif

        <form action="{{ route('client.search') }}" method="POST">
            @csrf
            <div class="mb-2">
                <input type="text" name="search" class="form-control search-input"
                    placeholder="Ex: T20260512-00001 ou 356938035643809"
                    value="{{ old('search') }}" required autofocus>
                <div class="text-muted small mt-1 px-1">
                    <i class="fas fa-info-circle me-1"></i>Le numéro de dossier se trouve sur votre bon de réception.
                </div>
            </div>
            <button type="submit" class="btn btn-search">
                <i class="fas fa-search me-2"></i> Rechercher mon dossier
            </button>
        </form>

        <div class="divider"><span>ou</span></div>

        <div class="text-center">
            <a href="{{ route('login') }}" class="btn btn-outline-primary w-100 rounded-3 py-2">
                <i class="fas fa-user me-2"></i> Se connecter à mon espace client
            </a>
        </div>
    </div>

    <div class="features mt-4">
        <div class="feature"><i class="fas fa-shield-alt"></i> Données sécurisées</div>
        <div class="feature"><i class="fas fa-eye-slash"></i> Données sensibles masquées</div>
        <div class="feature"><i class="fas fa-clock"></i> Suivi en temps réel</div>
    </div>

</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
