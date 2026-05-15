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
            'type'    => 'nullable|string|in:public,internal',
        ]);

        $prefix = ($request->type === 'internal') ? '[INT] ' : '';

        DossierMessage::create([
            'dossier_id' => $dossier->id,
            'user_id'    => Auth::id(),
            'message'    => $prefix . $request->message,
        ]);

        return redirect()
            ->to(route('dossiers.show', $dossier->id) . '?tab=communication')
            ->with('success', 'Message envoyé avec succès.');
    }
}
