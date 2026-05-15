<?php

namespace App\Http\Controllers;

use App\Models\Dossier;
use App\Models\DossierMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DossierMessageController extends Controller
{
    /**
     * Enregistrer un message interne sur un dossier.
     */
    public function store(Request $request, Dossier $dossier)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        DossierMessage::create([
            'dossier_id' => $dossier->id,
            'user_id'    => Auth::id(),
            'message'    => $request->message,
        ]);

        return back()->with('success', 'Message enregistré.');
    }
}
