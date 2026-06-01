<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Système SAV') - Maison Tel</title>

    <!-- Polices (Google Fonts) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bibliothèques de styles externes (Bootstrap, FontAwesome) bibliothèque d'icônes)-->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- Design Système Centralisé & Modulaire du SAV -->
    <!-- Tous les styles sont regroupés ici pour un chargement global, évitant les dépendances de page unitaires -->

    <!-- 1. Fondations & Variables Globales -->
    <link rel="stylesheet" href="{{ asset('css/theme.css') }}">
    <link rel="stylesheet" href="{{ asset('css/typography.css') }}">
    <link rel="stylesheet" href="{{ asset('css/common.css') }}">

    <!-- 2. Éléments Graphiques Communs -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/sidebar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/buttons.css') }}">
    <link rel="stylesheet" href="{{ asset('css/alerts.css') }}">
    <link rel="stylesheet" href="{{ asset('css/search.css') }}">

    <!-- 3. Tableaux de Bord & Indicateurs KPI -->
    <link rel="stylesheet" href="{{ asset('css/dashboard-cards.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin_dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin_statistiques.css') }}">

    <!-- 4. Modules Métiers Spécifiques -->
    <link rel="stylesheet" href="{{ asset('css/dossier-index.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dossier-create.css') }}">
    <link rel="stylesheet" href="{{ asset('css/diagnostic.css') }}">
    <link rel="stylesheet" href="{{ asset('css/devis.css') }}">
    <link rel="stylesheet" href="{{ asset('css/facture.css') }}">
    <link rel="stylesheet" href="{{ asset('css/intervention_create.css') }}">
    <link rel="stylesheet" href="{{ asset('css/replacement.css') }}">
    <link rel="stylesheet" href="{{ asset('css/stock.css') }}">
    <link rel="stylesheet" href="{{ asset('css/stock_mouvements.css') }}">
    <link rel="stylesheet" href="{{ asset('css/tarifs_mo.css') }}">
    <link rel="stylesheet" href="{{ asset('css/ticket_show.css') }}">
    <link rel="stylesheet" href="{{ asset('css/users.css') }}">
    <link rel="stylesheet" href="{{ asset('css/ventes.css') }}">
    <link rel="stylesheet" href="{{ asset('css/reject-demands.css') }}">
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
    <link rel="stylesheet" href="{{ asset('css/public_search.css') }}">

    @stack('styles')


</head>

<body class="@yield('body-class')">
    <div class="d-flex">
        <!-- Barre Latérale (Sidebar) -->
        @include('partials.sidebar')


        <!-- Zone Principale -->
        <div class="main-content">

            @auth
                <!-- Topbar Premium -->
                <header
                    class="header d-flex justify-content-between align-items-center shadow-sm py-3 px-4 bg-white sticky-top">

                    <div class="d-flex align-items-center">
                        <!-- Global Search -->
                        @if(auth()->user()->role !== 'Client')
                            <form
                                action="{{ auth()->user()->role === 'Technicien' ? route('technicien.tickets') : route('dossiers.index') }}"
                                method="GET"
                                class="search-bar d-none d-lg-flex align-items-center bg-light px-3 py-2 rounded-4 border-0"
                                style="width: 300px; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); border: 1px solid transparent !important;">
                                <i class="fas fa-search text-muted me-2"></i>
                                <input type="text" name="search" class="form-control border-0 bg-transparent p-0 small fw-bold"
                                    placeholder="Rechercher dossier, IMEI, client..." value="{{ request('search') }}"
                                    onfocus="this.parentElement.style.width='450px'; this.parentElement.style.backgroundColor='#fff'; this.parentElement.style.boxShadow='0 10px 15px -3px rgba(0,0,0,0.05)'; this.parentElement.style.borderColor='#e2e8f0';"
                                    onblur="this.parentElement.style.width='300px'; this.parentElement.style.backgroundColor='#f8fafc'; this.parentElement.style.boxShadow='none'; this.parentElement.style.borderColor='transparent';">
                            </form>
                        @endif
                    </div>

                    <div class="d-flex align-items-center gap-4">
                        <!-- Notifications -->
                        <div class="dropdown">
                            <button class="btn p-0 border-0 bg-transparent position-relative topbar-icon-btn" type="button"
                                data-bs-toggle="dropdown"
                                style="background: #f8fafc; width: 42px; height: 42px; border-radius: 12px;">
                                <i class="far fa-bell fs-5 text-secondary"></i>
                                @php $unreadCount = auth()->user()->unreadNotifications->count(); @endphp
                                @if($unreadCount > 0)
                                    <span
                                        class="position-absolute top-0 start-100 translate-middle badge rounded-circle bg-danger border border-white"
                                        style="width: 10px; height: 10px; padding: 0; margin-top: 5px; margin-left: -5px;">
                                    </span>
                                @endif
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow-premium border-0 mt-3 p-0 overflow-hidden"
                                style="width: 320px; border-radius: 16px;">
                                <li
                                    class="px-3 py-3 bg-light border-bottom d-flex justify-content-between align-items-center">
                                    <span class="fw-bold text-dark">Notifications</span>
                                    @if($unreadCount > 0)
                                        <button onclick="markAllRead()"
                                            class="btn btn-link p-0 text-decoration-none small text-primary fw-bold"
                                            style="font-size: 0.75rem;">Marquer lu</button>
                                    @endif
                                </li>
                                <div id="notifications-list" style="max-height: 350px; overflow-y: auto;">
                                    @forelse(auth()->user()->unreadNotifications->take(10) as $notification)
                                        @php
                                            $isClient = auth()->user()->role === 'Client';
                                            $isTech = auth()->user()->role === 'Technicien';
                                            
                                            if ($isClient) {
                                                $dossierId = $notification->data['dossier_id'] ?? null;
                                                $notifUrl = $dossierId ? route('client.ticket', $dossierId) : '#';
                                                $cursorType = $dossierId ? 'pointer' : 'default';
                                            } elseif ($isTech) {
                                                $notifUrl = '#';
                                                $cursorType = 'default';
                                            } else {
                                                $notifUrl = $notification->data['url'] ?? '#';
                                                $cursorType = 'pointer';
                                            }
                                        @endphp
                                        <li class="px-3 py-3 border-bottom notification-item {{ $notification->read_at ? 'bg-white opacity-75' : 'bg-light' }}"
                                            onclick="markRead('{{ $notification->id }}', '{{ $notifUrl }}')"
                                            style="cursor: {{ $cursorType }}; transition: all 0.2s;">
                                            <div class="d-flex justify-content-between align-items-start mb-1">
                                                <div
                                                    class="small fw-bold {{ $notification->read_at ? 'text-muted' : 'text-dark' }}">
                                                    {{ $notification->data['title'] ?? '' }}</div>
                                                @if(!$notification->read_at)
                                                    <div class="bg-primary rounded-circle" style="width: 8px; height: 8px;"></div>
                                                @endif
                                            </div>
                                            <div class="text-muted small mb-1" style="font-size: 0.75rem; line-height: 1.4;">
                                                {{ $notification->data['message'] ?? '' }}</div>
                                            <div class="text-muted extra-small"><i
                                                                      

                                                    class="far fa-clock me-1"></i>{{ $notification->created_at->diffForHumans() }}
                                            </div>
                                        </li>
                                    @empty
                                        <li class="px-3 py-5 text-center text-muted fw-bold">Aucune alerte</li>
                                    @endforelse
                                </div>
                               
                            </ul>
                        </div>

                        <!-- Profile -->
                        <div class="dropdown">
                            <a href="#" class="d-flex align-items-center gap-3 text-decoration-none"
                                data-bs-toggle="dropdown">
                                <div class="text-end d-none d-sm-block">
                                    <div class="fw-bold text-dark small lh-1">{{ auth()->user()->name }}</div>
                                    <small class="text-muted fw-bold"
                                        style="font-size:0.65rem;">{{ strtoupper(auth()->user()->role) }}</small>
                                </div>
                                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold shadow-sm"
                                    style="width:40px; height:40px; font-size:0.9rem; border: 2px solid #fff; outline: 1px solid #e2e8f0;">
                                    {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                                </div>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow-premium border-0 mt-3 p-2"
                                style="border-radius: 16px; min-width: 220px;">
                                <li class="px-3 py-2 border-bottom mb-2 bg-light rounded-3">
                                    <div class="fw-bold text-dark small">{{ auth()->user()->name }}</div>
                                    <div class="text-muted extra-small">{{ auth()->user()->email }}</div>
                                </li>
                                <li>
                                    <a class="dropdown-item d-flex align-items-center py-2 rounded-3"
                                        href="{{ route('profile.edit') }}">
                                        <i class="fas fa-user-circle me-2 text-muted"></i> Mon Profil
                                    </a>
                                </li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li>
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit"
                                            class="dropdown-item d-flex align-items-center py-2 text-danger rounded-3 w-100 text-start border-0 bg-transparent">
                                            <i class="fas fa-power-off me-2"></i> Déconnexion
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                </header>
            @endauth

            <!-- Content Area -->
            <div class="content-area py-4 px-4">

                @if(!Route::is('client.suivi') && !Route::is('client.search'))
                    @if(session('success'))
                        <div class="alert alert-premium alert-premium-success alert-dismissible fade show">
                            <i class="fas fa-check-circle alert-icon"></i>
                            <div class="alert-content">{{ session('success') }}</div>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-premium alert-premium-danger alert-dismissible fade show">
                            <i class="fas fa-exclamation-triangle alert-icon"></i>
                            <div class="alert-content">{{ session('error') }}</div>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                @endif

                @yield('content')
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Passage du Token CSRF au JS externe
        window.csrfToken = '{{ csrf_token() }}';
    </script>
    <script src="{{ asset('js/alerts.js') }}"></script>
    <script src="{{ asset('js/notifications.js') }}"></script>
    @stack('scripts')
</body>

</html>