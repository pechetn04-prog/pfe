@auth
    @php
        $societe = \App\Models\ParametreSociete::first();
    @endphp
    <div class="sidebar flex-column flex-shrink-0 p-0 shadow-sm vh-100 sticky-top">
        {{-- Header Logo (Fixe) --}}
        <div class="p-4">
            <a href="/" class="d-flex align-items-center justify-content-center text-decoration-none">
                @if($societe && $societe->logo)
                    <img src="{{ asset('storage/' . $societe->logo) }}" alt="Logo" class="rounded-3 shadow-sm w-100"
                        style="max-height: 60px; object-fit: contain; background: white; padding: 5px;">
                @else
                    <div class="bg-primary rounded-3 p-2 me-2 shadow-sm d-flex align-items-center justify-content-center"
                        style="width: 35px; height: 35px;">
                        <i class="fas fa-microchip fs-6 text-white"></i>
                    </div>
                    <span class="fs-5 fw-bold tracking-tight text-white">{{ $societe->nom_societe ?? 'Maison Tel' }}</span>
                @endif
            </a>
        </div>

        {{-- Menu Items (Défilable) --}}
        <div class="sidebar-content px-3 py-3 overflow-y-auto flex-grow-1">
            @if(auth()->user()->role === 'Admin')
                {{-- MENU ADMIN --}}
                <small class="text-muted text-uppercase fw-bold mb-2 d-block ps-3"
                    style="font-size: 0.65rem; letter-spacing: 1px; opacity: 0.6;">ADMINISTRATION</small>
                <ul class="nav nav-pills flex-column mb-auto">
                    <li class="nav-item mb-1"><a href="{{ route('dashboard') }}"
                            class="nav-link d-flex align-items-center {{ request()->routeIs('dashboard') ? 'active' : '' }}"><i
                                class="fas fa-chart-line me-3"></i> Dashboard</a></li>
                    <li class="nav-item mb-1"><a href="{{ route('dossiers.index') }}"
                            class="nav-link d-flex align-items-center {{ request()->routeIs('dossiers.index') && !request('statut') ? 'active' : '' }}"><i
                                class="fas fa-ticket-alt me-3"></i> Tous les Tickets</a></li>
                    <li class="nav-item mb-1"><a href="{{ route('admin.demandes_rejet.index') }}"
                            class="nav-link d-flex align-items-center {{ request()->routeIs('admin.demandes_rejet.*') ? 'active' : '' }}"><i
                                class="fas fa-exclamation-triangle me-3"></i> Demandes de Retrait</a></li>
                    <li class="nav-item mb-1"><a href="{{ route('dossiers.index', ['statut' => 'CLOTURE']) }}"
                            class="nav-link d-flex align-items-center {{ request('statut') === 'CLOTURE' ? 'active' : '' }}"><i
                                class="fas fa-archive me-3"></i> Archives Clôturées</a></li>
                    <li class="nav-item mb-1"><a href="{{ route('admin.statistiques') }}"
                            class="nav-link d-flex align-items-center {{ request()->routeIs('admin.statistiques') ? 'active' : '' }}"><i
                                class="fas fa-chart-bar me-3"></i> Statistiques</a></li>
                    <li class="nav-item mb-1"><a href="{{ route('stock.index') }}"
                            class="nav-link d-flex align-items-center {{ request()->routeIs('stock.*') ? 'active' : '' }}"><i
                                class="fas fa-boxes me-3"></i> Gestion du Stock</a></li>
                    <li class="nav-item mb-1"><a href="{{ route('admin.tarifs_mo.index') }}"
                            class="nav-link d-flex align-items-center {{ request()->routeIs('admin.tarifs_mo.*') ? 'active' : '' }}"><i
                                class="fas fa-user-tag me-3"></i> Tarifs Main d'oeuvre</a></li>
                    <li class="nav-item mb-1"><a href="{{ route('users.index') }}"
                            class="nav-link d-flex align-items-center {{ request()->routeIs('users.*') ? 'active' : '' }}"><i
                                class="fas fa-users-cog me-3"></i> Utilisateurs</a></li>
                    <li class="nav-item mb-1"><a href="{{ route('ventes.index') }}"
                            class="nav-link d-flex align-items-center {{ request()->routeIs('ventes.*') ? 'active' : '' }}"><i
                                class="fas fa-shopping-cart me-3"></i> Ventes</a></li>
                    <li class="nav-item mb-1"><a href="{{ route('parametres-societe.edit') }}"
                            class="nav-link d-flex align-items-center {{ request()->routeIs('parametres-societe.*') ? 'active' : '' }}"><i
                                class="fas fa-tools me-3"></i> Paramètres Société</a></li>
                </ul>
            @elseif(auth()->user()->role === 'Agent')
                {{-- MENU AGENT --}}
                <small class="text-muted text-uppercase fw-bold mb-2 d-block ps-3"
                    style="font-size: 0.65rem; letter-spacing: 1px; opacity: 0.6;">VUE GÉNÉRALE</small>
                <ul class="nav nav-pills flex-column mb-4">
                    <li class="nav-item mb-1"><a href="{{ route('dashboard') }}"
                            class="nav-link d-flex align-items-center {{ request()->routeIs('dashboard') ? 'active' : '' }}"><i
                                class="fas fa-th-large me-3"></i> Dashboard</a></li>
                </ul>
                <small class="text-muted text-uppercase fw-bold mb-2 d-block ps-3"
                    style="font-size: 0.65rem; letter-spacing: 1px; opacity: 0.6;">OPÉRATIONS SAV</small>
                <ul class="nav nav-pills flex-column mb-4">
                    <li class="nav-item mb-1"><a href="{{ route('dossiers.create') }}"
                            class="nav-link d-flex align-items-center {{ request()->routeIs('dossiers.create') ? 'active' : '' }}"><i
                                class="fas fa-plus me-3"></i> Créer un Dossier</a></li>
                    <li class="nav-item mb-1"><a href="{{ route('dossiers.index') }}"
                            class="nav-link d-flex align-items-center {{ request()->routeIs('dossiers.index') && !request('statut') ? 'active' : '' }}"><i
                                class="fas fa-clipboard-list me-3"></i> Tous les Dossiers</a></li>
                    <li class="nav-item mb-1"><a href="{{ route('dossiers.index', ['statut' => 'RECU']) }}"
                            class="nav-link d-flex align-items-center {{ request('statut') === 'RECU' ? 'active' : '' }}"><i
                                class="fas fa-inbox me-3"></i> Nouveaux (Reçus)</a></li>
                    <li class="nav-item mb-1"><a href="{{ route('dossiers.index', ['statut' => 'EN_ATTENTE_DEVIS']) }}"
                            class="nav-link d-flex align-items-center {{ request('statut') === 'EN_ATTENTE_DEVIS' ? 'active' : '' }}"><i
                                class="fas fa-file-invoice-dollar me-3"></i> Attente Devis</a></li>
                    <li class="nav-item mb-1"><a href="{{ route('dossiers.index', ['statut' => 'REPARE']) }}"
                            class="nav-link d-flex align-items-center {{ request('statut') === 'REPARE' ? 'active' : '' }}"><i
                                class="fas fa-file-invoice me-3"></i> Attente Facturation</a></li>
                    <li class="nav-item mb-1"><a href="{{ route('dossiers.index', ['statut' => 'FACTURE']) }}"
                            class="nav-link d-flex align-items-center {{ request('statut') === 'FACTURE' ? 'active' : '' }}"><i
                                class="fas fa-truck-loading me-3"></i> À Livrer</a></li>
                    <li class="nav-item mb-1"><a href="{{ route('dossiers.index', ['statut' => 'CLOTURE']) }}"
                            class="nav-link d-flex align-items-center {{ request('statut') === 'CLOTURE' ? 'active' : '' }}"><i
                                class="fas fa-archive me-3"></i> Archives Clôturées</a></li>
                </ul>
                <small class="text-muted text-uppercase fw-bold mb-2 d-block ps-3"
                    style="font-size: 0.65rem; letter-spacing: 1px; opacity: 0.6;">CLIENTÈLE</small>
                <ul class="nav nav-pills flex-column mb-auto">
                    <li class="nav-item mb-1"><a href="{{ route('users.index') }}"
                            class="nav-link d-flex align-items-center {{ request()->routeIs('users.*') ? 'active' : '' }}"><i
                                class="fas fa-users-cog me-3"></i> Gestion Clients</a></li>
                </ul>
            @elseif(auth()->user()->role === 'Technicien')
                {{-- MENU TECHNICIEN --}}
                <small class="text-muted text-uppercase fw-bold mb-2 d-block ps-3"
                    style="font-size: 0.65rem; letter-spacing: 1px; opacity: 0.6;">TABLEAU DE BORD</small>
                <ul class="nav nav-pills flex-column mb-4">
                    <li class="nav-item mb-1"><a href="{{ route('dashboard') }}"
                            class="nav-link d-flex align-items-center {{ request()->routeIs('dashboard') ? 'active' : '' }}"><i
                                class="fas fa-th-large me-3"></i> Vue d'ensemble</a></li>
                </ul>

                <small class="text-muted text-uppercase fw-bold mb-2 d-block ps-3"
                    style="font-size: 0.65rem; letter-spacing: 1px; opacity: 0.6;">ATELIER TECHNIQUE</small>
                <ul class="nav nav-pills flex-column mb-auto">
                    <li class="nav-item mb-1"><a href="{{ route('technicien.tickets') }}"
                            class="nav-link d-flex align-items-center {{ request()->routeIs('technicien.tickets') && !request('statut') ? 'active' : '' }}"><i
                                class="fas fa-clipboard-list me-3"></i> Tous mes Dossiers</a></li>
                    <li class="nav-item mb-1"><a href="{{ route('technicien.tickets', ['statut' => 'EN_DIAGNOSTIC']) }}"
                            class="nav-link d-flex align-items-center {{ request('statut') === 'EN_DIAGNOSTIC' ? 'active' : '' }}"><i
                                class="fas fa-search me-3"></i> En Diagnostic</a></li>
                    <li class="nav-item mb-1"><a href="{{ route('technicien.tickets', ['statut' => 'EN_ATTENTE_DEVIS']) }}"
                            class="nav-link d-flex align-items-center {{ request('statut') === 'EN_ATTENTE_DEVIS' ? 'active' : '' }}"><i
                                class="fas fa-file-invoice-dollar me-3"></i> En attente devis</a></li>
                    <li class="nav-item mb-1"><a href="{{ route('technicien.tickets', ['statut' => 'EN_REPARATION']) }}"
                            class="nav-link d-flex align-items-center {{ request('statut') === 'EN_REPARATION' ? 'active' : '' }}"><i
                                class="fas fa-tools me-3"></i> En Réparation</a></li>
                    <li class="nav-item mb-1"><a href="{{ route('technicien.tickets', ['statut' => 'ATTENTE_PIECE']) }}"
                            class="nav-link d-flex align-items-center {{ request('statut') === 'ATTENTE_PIECE' ? 'active' : '' }}"><i
                                class="fas fa-hourglass-half me-3"></i> En attente pièces</a></li>
                    <li class="nav-item mb-1"><a href="{{ route('technicien.tickets', ['statut' => 'CLOTURE']) }}"
                            class="nav-link d-flex align-items-center {{ request('statut') === 'CLOTURE' ? 'active' : '' }}"><i
                                class="fas fa-archive me-3"></i> Dossiers Archivés</a></li>
                    <li class="nav-item mb-1"><a href="{{ route('technicien.stock') }}"
                            class="nav-link d-flex align-items-center {{ request()->routeIs('technicien.stock') ? 'active' : '' }}"><i
                                class="fas fa-microchip me-3"></i> État du Stock</a></li>
                </ul>
            @elseif(auth()->user()->role === 'Client')
                {{-- MENU CLIENT --}}
                <small class="text-muted text-uppercase fw-bold mb-2 d-block ps-3"
                    style="font-size: 0.65rem; letter-spacing: 1px; opacity: 0.6;">MON ESPACE CLIENT</small>
                <ul class="nav nav-pills flex-column mb-auto">
                    <li class="nav-item mb-1">
                        <a href="{{ route('client.dashboard') }}"
                            class="nav-link d-flex align-items-center {{ request()->routeIs('client.dashboard') && !request('archive') ? 'active' : '' }}">
                            <i class="fas fa-ticket-alt me-3"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item mb-1">
                        <a href="{{ route('client.dashboard', ['archive' => 1]) }}"
                            class="nav-link d-flex align-items-center {{ request('archive') == 1 ? 'active' : '' }}">
                            <i class="fas fa-archive me-3"></i> Mes Archives
                        </a>
                    </li>
                </ul>
            @endif
        </div>

        {{-- Footer Actions (Fixe en bas) --}}
        <div class="mt-auto p-3">

            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="nav-link logout-btn d-flex align-items-center border-0 w-100 fw-bold py-3"
                    style="cursor: pointer;">
                    <i class="fas fa-sign-out-alt me-3"></i> Déconnexion
                </button>
            </form>
        </div>
    </div>
@endauth