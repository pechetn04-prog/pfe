@php
    // dashboard/index.blade.php — Redirection intelligente selon le rôle
    $role = auth()->user()->role ?? null;
    $routes = [
        'Admin'      => 'admin.dashboard',
        'Agent'      => 'agent.dashboard',
        'Technicien' => 'technicien.dashboard',
        'Client'     => 'client.dashboard',
    ];
    $target = isset($routes[$role]) ? route($routes[$role]) : '/';
@endphp
<script>window.location.href = "{{ $target }}";</script>
<meta http-equiv="refresh" content="0;url={{ $target }}">
