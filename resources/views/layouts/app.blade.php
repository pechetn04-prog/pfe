<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Système SAV') - Maison Tel</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Styles -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/theme.css') }}">
    <link rel="stylesheet" href="{{ asset('css/common.css') }}">
    <link rel="stylesheet" href="{{ asset('css/sidebar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard-cards.css') }}">
    <link rel="stylesheet" href="{{ asset('css/style-premium.css') }}">
    @stack('styles')
    
    <style>
        :root { --primary-blue: #2563eb; }
        body { font-family: 'Outfit', sans-serif; background-color: #f1f5f9; color: #1e293b; }
        /* Styles de la sidebar (Thème sombre pour tous) */
        .sidebar { width: 260px; min-height: 100vh; background-color: #1a2234; flex-shrink: 0; transition: all 0.3s; }
        .sidebar .nav-link { color: #cbd5e1 !important; font-weight: 500; transition: all 0.2s; }
        .sidebar .nav-link i { color: #64748b; transition: all 0.2s; }
        .sidebar .nav-link:hover { background-color: #242e42; color: #ffffff !important; }
        .sidebar .nav-link:hover i { color: #ffffff; }
        .sidebar .nav-link.active { background-color: #2563eb !important; color: #ffffff !important; }
        .sidebar .nav-link.active i { color: #ffffff !important; }
        .sidebar .text-muted { color: #64748b !important; }
        .sidebar .logout-btn { color: #fb7185 !important; }
        .sidebar .logout-btn i { color: #fb7185 !important; }

        .header { background: white; border-bottom: 1px solid #e2e8f0; padding: 0.75rem 2rem; position: sticky; top: 0; z-index: 900; }
        .main-content { flex-grow: 1; min-height: 100vh; overflow-x: hidden; display: flex; flex-direction: column; }
        .content-area { padding: 1.5rem; flex-grow: 1; }
        .search-bar { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 0.5rem 1rem; width: 400px; }
        .hover-opacity:hover { opacity: 0.8; }
        .transition-all { transition: all 0.2s; }
    </style>
</head>
<body>
    <div class="d-flex">
        @include('partials.sidebar')
        
        <div class="main-content">
            @auth
            <header class="header d-flex justify-content-between align-items-center shadow-sm">
                <div class="text-muted small">Bonjour, <span class="fw-bold text-dark">{{ auth()->user()->name }}</span></div>
                
                <form action="{{ route('dossiers.index') }}" method="GET" class="search-bar d-none d-md-flex align-items-center">
                    <i class="fas fa-search text-muted me-2"></i>
                    <input type="text" name="search" class="form-control border-0 bg-transparent p-0 small" placeholder="Rechercher un dossier, IMEI, client..." value="{{ request('search') }}">
                </form>

                <div class="d-flex align-items-center gap-3">
                    {{-- Notifications --}}
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

                    {{-- User Profile --}}
                    <a href="{{ route('profile.edit') }}" class="d-flex align-items-center gap-2 ps-3 border-start text-decoration-none transition-all hover-opacity">
                        <div class="text-end d-none d-sm-block">
                            <div class="fw-bold small text-dark lh-1">{{ auth()->user()->name }}</div>
                            <small class="text-muted" style="font-size:0.7rem;">{{ auth()->user()->role }}</small>
                        </div>
                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold shadow-sm"
                            style="width:38px; height:38px; font-size:0.8rem;">
                            {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                        </div>
                    </a>
                </div>
            </header>
            @endauth
            
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
