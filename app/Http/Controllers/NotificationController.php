<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

// Gestion des notifications des utilisateurs.
class NotificationController extends Controller
{
    // Marque une notification spécifique comme lue.
    public function markAsRead($id)
    {
        $user = Auth::user();
        
        // Recherche la notification et la marque comme lue
        $notification = $user->notifications()->findOrFail($id);
        $notification->markAsRead();

        return response()->json(['success' => true]);
    }

    // Marque toutes les notifications de l'utilisateur comme lues.
    public function markAllAsRead()
    {
        $user = Auth::user();
        
        // Marque en masse toutes les notifications non lues comme lues
        $user->unreadNotifications->markAsRead();

        return response()->json(['success' => true]);
    }
}
