<?php

use App\Http\Controllers\AgentDashboardController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DiagnosticController;
use App\Http\Controllers\InterventionController;
use App\Http\Controllers\PieceController;
use App\Http\Controllers\TechnicienDashboardController;
use App\Http\Controllers\TechnicienDossierController;
use App\Http\Controllers\DossierController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\VenteController;
use App\Http\Controllers\DevisController;
use App\Http\Controllers\FactureController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ParametreSocieteController;
use App\Http\Controllers\DossierMessageController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ClientDashboardController;
use App\Http\Controllers\MouvementStockController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\DemandeRejetController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| ROUTES PUBLIQUES (Accessibles par tout le monde)
|--------------------------------------------------------------------------
*/
Route::redirect('/', '/login');

// Suivi de dossier pour le client (sans connexion)
Route::get('/suivi', [ClientController::class, 'index'])->name('client.suivi');
Route::match(['GET', 'POST'], '/client/search', [ClientController::class, 'search'])->name('client.search');
Route::get('/client/suivi/{id}', [ClientController::class, 'suiviPublic'])->name('client.suivi.public');
Route::get('/suivi/dossier/{id}', [ClientController::class, 'show'])->name('client.ticket.view');
Route::post('/suivi/dossier/{id}/devis/accepter', [ClientController::class, 'accepterDevis'])->name('client.devis.accepter.public');
Route::post('/suivi/dossier/{id}/devis/refuser', [ClientController::class, 'refuserDevis'])->name('client.devis.refuser.public');
Route::post('/suivi/dossier/{id}/avis', [ClientController::class, 'submitAvis'])->name('client.avis.submit.public');

/*
|--------------------------------------------------------------------------
| ROUTES GUEST (Uniquement pour les utilisateurs NON connectés)
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'getFormLogin'])->name('login');
    Route::post('/login', [LoginController::class, 'loginUser'])->name('login.post');

    // Route::get('/register', [RegisterController::class, 'getFormRegister'])->name('register');
// Route::post('/register', [RegisterController::class, 'registerUser'])->name('register.post');

    // Mot de passe oublié
    Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [ResetPasswordController::class, 'reset'])->name('password.update');
});

/*
|--------------------------------------------------------------------------
| ROUTES AUTHENTIFIÉES (Nécessitent d'être connecté - 'auth')
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    // Déconnexion
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // Profil utilisateur
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::view('/profile-view', 'auth.profile')->name('profile');

    // Accueil dynamique selon le rôle
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/admin/stock', [PieceController::class, 'index'])->name('stock.index');

    // Routes Dossiers partagées
    Route::get('/dossiers/check-imei', [DossierController::class, 'checkImei'])->name('dossiers.checkImei');
    Route::get('/dossiers/suggest-technicians', [DossierController::class, 'suggestTechnicians'])->name('dossiers.suggestTechnicians');
    Route::post('/dossiers/{dossier}/assign', [DossierController::class, 'assign'])->name('dossiers.assign');

    /*
    |----------------------------------------------------------------------
    | RÔLE : ADMIN UNIQUEMENT
    |----------------------------------------------------------------------
    */
    Route::middleware('role:Admin')->group(function () {
        // Dashboard Admin & Stats
        Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
        Route::get('/admin/statistiques', [AdminDashboardController::class, 'statistiques'])->name('admin.statistiques');

        // Paramètres Société
        Route::get('/admin/parametres-societe', [ParametreSocieteController::class, 'edit'])->name('parametres-societe.edit');
        Route::put('/admin/parametres-societe', [ParametreSocieteController::class, 'update'])->name('parametres-societe.update');

        // Gestion avancée du Stock (Admins peuvent tout faire)
        Route::get('/admin/stock/create', [PieceController::class, 'create'])->name('stock.create');
        Route::post('/admin/stock', [PieceController::class, 'store'])->name('stock.store');
        Route::get('/admin/stock/{piece}/edit', [PieceController::class, 'edit'])->name('stock.edit');
        Route::put('/admin/stock/{piece}', [PieceController::class, 'update'])->name('stock.update');
        Route::delete('/admin/stock/{piece}', [PieceController::class, 'destroy'])->name('stock.destroy');
        Route::patch('/admin/stock/{piece}/toggle', [PieceController::class, 'toggleStatus'])->name('stock.toggle');

        // Mouvements de stock
        Route::get('/admin/stock-mouvements', [MouvementStockController::class, 'index'])->name('stock.mouvements');
        Route::get('/admin/stock-mouvements/create', [MouvementStockController::class, 'create'])->name('stock.mouvements.create');
        Route::post('/admin/stock-mouvements', [MouvementStockController::class, 'store'])->name('stock.mouvements.store');

        // Ventes
        Route::get('/admin/ventes-produits', [VenteController::class, 'index'])->name('ventes.index');
        Route::get('/admin/ventes/create', [VenteController::class, 'create'])->name('ventes.create');
        Route::post('/admin/ventes', [VenteController::class, 'store'])->name('ventes.store');

        // Gestion des Tarifs Main d'œuvre
        Route::get('/admin/tarifs-mo', [\App\Http\Controllers\TarifMoController::class, 'index'])->name('admin.tarifs_mo.index');
        Route::post('/admin/tarifs-mo', [\App\Http\Controllers\TarifMoController::class, 'store'])->name('admin.tarifs_mo.store');
        Route::put('/admin/tarifs-mo/{tarifMo}', [\App\Http\Controllers\TarifMoController::class, 'update'])->name('admin.tarifs_mo.update');
        Route::delete('/admin/tarifs-mo/{tarifMo}', [\App\Http\Controllers\TarifMoController::class, 'destroy'])->name('admin.tarifs_mo.destroy');
        Route::patch('/admin/tarifs-mo/{tarifMo}/toggle', [\App\Http\Controllers\TarifMoController::class, 'toggleStatus'])->name('admin.tarifs_mo.toggle');

        // Validation Remplacement
        Route::post('/dossiers/{dossier}/valider-remplacement', [DossierController::class, 'validateReplacement'])->name('dossiers.validerRemplacement');
        Route::post('/dossiers/{dossier}/refuser-remplacement', [DossierController::class, 'refuseReplacement'])->name('dossiers.refuserRemplacement');

        // Pièce introuvable (Admin)
        Route::post('/dossiers/{dossier}/piece-introuvable', [DossierController::class, 'pieceIntrouvable'])->name('dossiers.pieceIntrouvable');
        Route::post('/dossiers/{dossier}/marquer-irreparable', [DossierController::class, 'marquerIrreparable'])->name('dossiers.marquerIrreparable');
        Route::post('/dossiers/{dossier}/marquer-piece-recue', [DossierController::class, 'marquerPieceRecue'])->name('dossiers.marquerPieceRecue');

        // Gestion des demandes de rejet de diagnostic
        Route::get('/admin/demandes-rejet', [DemandeRejetController::class, 'index'])->name('admin.demandes_rejet.index');
        Route::post('/admin/demandes-rejet/{demande}/approve', [DemandeRejetController::class, 'approve'])->name('admin.demandes_rejet.approve');
        Route::post('/admin/demandes-rejet/{demande}/reject', [DemandeRejetController::class, 'reject'])->name('admin.demandes_rejet.reject');
    });

    /*
    |----------------------------------------------------------------------
    | RÔLE : AGENT SAV ou ADMIN (Gestion opérationnelle)
    |----------------------------------------------------------------------
    */
    Route::middleware('role:Agent,Admin')->group(function () {
        Route::get('/agent/dashboard', [AgentDashboardController::class, 'index'])->name('agent.dashboard');

        // Gestion des dossiers (Réception)
        Route::get('/dossiers', [DossierController::class, 'index'])->name('dossiers.index');

        // Création réservée à l'Agent (DOIT être avant /{dossier} pour éviter le conflit)
        Route::middleware('role:Agent')->group(function () {
            Route::get('/dossiers/create', [DossierController::class, 'create'])->name('dossiers.create');
            Route::post('/dossiers', [DossierController::class, 'store'])->name('dossiers.store');
        });

        Route::get('/dossiers/{dossier}', [DossierController::class, 'show'])->name('dossiers.show');


        // Devis et Factures
        Route::get('/devis/{devis}', [DevisController::class, 'show'])->name('devis.show');
        Route::get('/factures/{facture}', [FactureController::class, 'show'])->name('factures.show');
        Route::get('/dossiers/{dossier}/devis/create', [DevisController::class, 'create'])->name('devis.create');
        Route::post('/dossiers/{dossier}/devis', [DevisController::class, 'store'])->name('devis.store');
        Route::post('/devis/{devis}/accepter', [DevisController::class, 'accepterDevis'])->name('devis.accepter');
        Route::post('/devis/{devis}/refuser', [DevisController::class, 'refuser'])->name('devis.refuser');

        Route::get('/dossiers/{dossier}/facture/create', [FactureController::class, 'create'])->name('factures.create');
        Route::post('/dossiers/{dossier}/facture', [FactureController::class, 'store'])->name('facture.store');

        // Livraison et Clôture
        Route::middleware('role:Agent,Admin')->group(function () {
            Route::post('/dossiers/{dossier}/livrer', [DossierController::class, 'livrer'])->name('dossiers.livrer');
            Route::post('/dossiers/{dossier}/cloturer', [DossierController::class, 'cloturer'])->name('dossiers.cloturer');
        });

        // Préparer Remplacement
        Route::get('/dossiers/{dossier}/preparer-remplacement', [DossierController::class, 'preparerRemplacement'])->name('dossiers.preparerRemplacement');
        Route::post('/dossiers/{dossier}/preparer-remplacement', [DossierController::class, 'storeRemplacement'])->name('dossiers.storeRemplacement');

        Route::get('/dossiers/{dossier}/etiquette', [DossierController::class, 'etiquette'])->name('dossiers.etiquette');

        // Gestion des Comptes Clients
        Route::prefix('gestion-clients')->group(function () {
            Route::get('/', [UserController::class, 'index'])->name('users.index');
            Route::get('/create', [UserController::class, 'create'])->name('users.create');
            Route::post('/', [UserController::class, 'store'])->name('users.store');
            Route::get('/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
            Route::put('/{user}', [UserController::class, 'update'])->name('users.update');
            Route::delete('/{user}', [UserController::class, 'destroy'])->name('users.destroy');
            Route::patch('/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggleStatus');
        });
    });

    // Routes PDF partagées
    Route::middleware('role:Agent,Admin,Client,Technicien')->group(function () {
        Route::get('/factures/{facture}/pdf', [FactureController::class, 'pdf'])->name('factures.pdf');
        Route::get('/devis/{devis}/pdf', [DevisController::class, 'pdf'])->name('devis.pdf');
        Route::get('/dossiers/{dossier}/reception-pdf', [DossierController::class, 'receptionPdf'])->name('dossiers.reception.pdf');
        Route::get('/dossiers/{dossier}/diagnostic-pdf', [DossierController::class, 'diagnosticReport'])->name('dossiers.diagnostic.pdf');
        Route::get('/dossiers/{dossier}/intervention-pdf', [DossierController::class, 'interventionReport'])->name('dossiers.intervention.pdf');
    });

    /*
    |----------------------------------------------------------------------
    | RÔLE : TECHNICIEN ou ADMIN
    |----------------------------------------------------------------------
    */
    Route::middleware('role:Technicien,Admin')->group(function () {
        Route::get('/technicien/dashboard', [TechnicienDashboardController::class, 'index'])->name('technicien.dashboard');
        Route::get('/technicien/dossiers', [TechnicienDossierController::class, 'index'])->name('technicien.tickets');
        Route::get('/technicien/stock', [PieceController::class, 'index'])->name('technicien.stock');

        // Diagnostics
        Route::post('/dossiers/{dossier}/diagnostic/start', [DossierController::class, 'startDiagnostic'])->name('dossiers.startDiagnostic');
        Route::get('/technicien/dossiers/{dossier}/diagnostic', [DiagnosticController::class, 'create'])->name('diagnostics.create');
        Route::post('/technicien/dossiers/{dossier}/diagnostic', [DiagnosticController::class, 'store'])->name('diagnostics.store');
        Route::get('/technicien/dossiers/{dossier}/diagnostic/show', [DiagnosticController::class, 'show'])->name('diagnostics.show');

        // Interventions
        Route::get('/technicien/dossiers/{dossier}/intervention', [InterventionController::class, 'create'])->name('interventions.create');
        Route::post('/technicien/dossiers/{dossier}/intervention', [InterventionController::class, 'store'])->name('interventions.store');
        Route::get('/technicien/dossiers/{dossier}/intervention/show', [InterventionController::class, 'show'])->name('interventions.show');

        // Actions techniques
        Route::post('/dossiers/{dossier}/reparation', [DossierController::class, 'lancerReparation'])->name('dossiers.reparation');

        // Demande de retrait du dossier (Rejet)
        Route::post('/dossiers/{dossier}/rejeter', [DemandeRejetController::class, 'store'])->name('dossiers.rejeter');
    });

    // Communication
    Route::post('/dossiers/{dossier}/messages', [DossierMessageController::class, 'store'])->name('dossiers.messages.store');

    // Notifications
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.readAll');

    /*
    |----------------------------------------------------------------------
    | RÔLE : CLIENT UNIQUEMENT
    |----------------------------------------------------------------------
    */
    Route::middleware('role:Client')->group(function () {
        Route::get('/client/dashboard', [ClientDashboardController::class, 'index'])->name('client.dashboard');
        Route::get('/client/dossier/{id}', [ClientController::class, 'show'])->name('client.ticket');
        Route::post('/client/dossier/{id}/devis/accepter', [ClientController::class, 'accepterDevis'])->name('client.devis.accepter');
        Route::post('/client/dossier/{id}/devis/refuser', [ClientController::class, 'refuserDevis'])->name('client.devis.refuser');
        Route::post('/client/dossier/{id}/avis', [ClientController::class, 'submitAvis'])->name('client.avis.submit');
    });

});
