<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Système SAV') - Maison Tel</title>
    
    <!-- Polices (Google Fonts) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bibliothèques de styles externes (Bootstrap, FontAwesome) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Design Système Premium du SAV (Fichiers CSS Centralisés) -->
    <link rel="stylesheet" href="{{ asset('css/theme.css') }}">
    <link rel="stylesheet" href="{{ asset('css/common.css') }}">

    <link rel="stylesheet" href="{{ asset('css/sidebar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard-cards.css') }}">
    <link rel="stylesheet" href="{{ asset('css/style-premium.css') }}">
    @stack('styles')
    

</head>
<body>
    <div class="d-flex">
        <!-- Barre Latérale (Sidebar) - Incluse depuis un partiel pour plus de clarté -->
        @include('partials.sidebar')

        
        <!-- Zone Principale de l'application -->
        <div class="main-content">

            @auth
            <!-- Barre de navigation supérieure (Topbar) -->
            <header class="header d-flex justify-content-between align-items-center shadow-sm">

                <div></div> {{-- Espaceur pour maintenir l'alignement --}}

                
                <!-- Barre de recherche globale (Dossiers, Clients, IMEI) -->
                <form action="{{ route('dossiers.index') }}" method="GET" class="search-bar d-none d-md-flex align-items-center">
                    <i class="fas fa-search text-muted me-2"></i>
                    <input type="text" name="search" class="form-control border-0 bg-transparent p-0 small" placeholder="Rechercher un dossier, IMEI, client..." value="{{ request('search') }}">
                </form>


                <div class="d-flex align-items-center gap-3">
                    <!-- Système de Notifications (Temps Réel / DB) -->
                    <div class="dropdown">

                        <button class="btn p-0 border-0 bg-transparent position-relative" type="button" data-bs-toggle="dropdown">
                            <i class="far fa-bell fs-5 text-muted"></i>
                            @php $unreadCount = auth()->user()->unreadNotifications->count(); @endphp
                            @if($unreadCount > 0)
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size:0.5rem; padding: 0.25em 0.4em;">{{ $unreadCount }}</span>
                            @endif
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2 p-0" style="width: 300px; border-radius: 12px; overflow: hidden;">
                            <li class="px-3 py-2 bg-light border-bottom d-flex justify-content-between align-items-center">
                                <span class="fw-bold small">Notifications</span>
                                @if($unreadCount > 0)
                                <button onclick="markAllRead()" class="btn btn-link p-0 text-decoration-none small text-primary" style="font-size: 0.7rem;">Tout marquer comme lu</button>
                                @endif
                            </li>
                            <div id="notifications-list" style="max-height: 300px; overflow-y: auto;">
                                @forelse(auth()->user()->notifications->take(10) as $notification)
                                <li class="px-3 py-2 border-bottom notification-item {{ $notification->read_at ? 'opacity-50' : 'bg-white' }}" 
                                    onclick="markRead('{{ $notification->id }}', '{{ $notification->data['url'] ?? '#' }}')"
                                    style="cursor: pointer; transition: background 0.2s;">
                                    <div class="d-flex justify-content-between">
                                        <div class="small fw-bold {{ $notification->read_at ? 'text-muted' : 'text-dark' }}">{{ $notification->data['title'] ?? 'Notification' }}</div>
                                        @if(!$notification->read_at)
                                        <span class="badge bg-primary p-1 rounded-circle" style="width: 6px; height: 6px;"> </span>
                                        @endif
                                    </div>
                                    <div class="text-muted small" style="font-size: 0.7rem;">{{ $notification->data['message'] ?? '' }}</div>
                                    <div class="text-muted" style="font-size: 0.6rem;">{{ $notification->created_at->diffForHumans() }}</div>
                                </li>
                                @empty
                                <li class="px-3 py-4 text-center text-muted small">Aucune notification</li>
                                @endforelse
                            </div>
                            <li class="p-2 text-center bg-light">
                                <a href="#" class="text-muted small text-decoration-none">Voir toutes les notifications</a>
                            </li>
                        </ul>
                    </div>

                    <!-- Menu Profil Utilisateur & Déconnexion -->
                    <div class="dropdown">

                        <a href="#" class="d-flex align-items-center gap-2 ps-3 border-start text-decoration-none transition-all hover-opacity" data-bs-toggle="dropdown" aria-expanded="false">
                            <div class="text-end d-none d-sm-block">
                                <div class="fw-bold small text-dark lh-1">{{ auth()->user()->name }}</div>
                                <small class="text-muted" style="font-size:0.7rem;">{{ auth()->user()->role }}</small>
                            </div>
                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold shadow-sm"
                                style="width:38px; height:38px; font-size:0.8rem;">
                                {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                            </div>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2 py-2" style="border-radius: 12px; min-width: 200px;">
                            <li>
                                <div class="px-3 py-2 border-bottom mb-1">
                                    <div class="fw-bold text-dark small text-truncate" style="max-width: 170px;">{{ auth()->user()->name }}</div>
                                    <div class="text-muted small" style="font-size: 0.65rem;">{{ auth()->user()->email }}</div>
                                </div>
                            </li>
                            <li>
                                <a class="dropdown-item d-flex align-items-center py-2" href="{{ route('profile.edit') }}">
                                    <i class="fas fa-user-circle me-2 text-muted"></i> 
                                    <span class="small fw-bold">Mon Profil</span>
                                </a>
                            </li>
                            <li><hr class="dropdown-divider opacity-50"></li>
                            <li>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="dropdown-item d-flex align-items-center py-2 text-danger border-0 bg-transparent w-100 text-start">
                                        <i class="fas fa-sign-out-alt me-2"></i> 
                                        <span class="small fw-bold">Déconnexion</span>
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </header>
            @endauth
            
            <!-- Zone de contenu dynamique -->
            <div class="content-area">

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4">
                        <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4">
                        <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @yield('content')
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        /**
         * Logique JavaScript pour la gestion des notifications (Fetch API)
         */
        function markRead(id, url) {
            fetch(`/notifications/${id}/read`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            }).then(() => {
                if (url && url !== '#') {
                    window.location.href = url;
                } else {
                    location.reload();
                }
            });
        }

        function markAllRead() {
            fetch(`/notifications/read-all`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            }).then(() => {
                location.reload();
            });
        }

    </script>
    @stack('scripts')
</body>
</html>
