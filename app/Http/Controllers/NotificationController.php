<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Marquer une notification comme lue.
     */
    public function markAsRead($id)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $notification = $user->notifications()->findOrFail($id);
        $notification->markAsRead();

        return response()->json(['success' => true]);
    }

    /**
     * Marquer toutes les notifications comme lues.
     */
    public function markAllAsRead()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $user->unreadNotifications->markAsRead();

        return response()->json(['success' => true]);
    }
}
