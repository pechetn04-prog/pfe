<?php

use App\Models\User;
use App\Models\Dossier;
use App\Models\Appareil;
use App\Models\Diagnostic;
use App\Models\Devis;
use App\Notifications\DevisDisponibleNotification;
use App\Notifications\DevisModifieNotification;
use Illuminate\Support\Facades\Notification;

it('sends DevisDisponibleNotification when a devis is created', function () {
    Notification::fake();

    $agent = User::create([
        'name' => 'Agent SAV',
        'email' => 'agent@example.com',
        'password' => bcrypt('password'),
        'role' => 'Agent',
        'actif' => true,
    ]);

    $client = User::create([
        'name' => 'Client Test',
        'email' => 'client@example.com',
        'password' => bcrypt('password'),
        'role' => 'Client',
        'actif' => true,
    ]);

    $appareil = Appareil::create([
        'imei' => '123456789012345',
        'modele' => 'iPhone 13',
        'client_id' => $client->id,
    ]);

    $dossier = Dossier::create([
        'num_dossier' => 'DOS-TEST-0001',
        'client_id' => $client->id,
        'appareil_id' => $appareil->id,
        'statut' => 'EN_DIAGNOSTIC',
        'date_reception' => now(),
    ]);

    $diagnostic = Diagnostic::create([
        'dossier_id' => $dossier->id,
        'constat' => 'Écran brisé',
        'recommandation' => 'Remplacer écran',
    ]);

    $response = $this->actingAs($agent)
        ->post(route('devis.store', $dossier->id), [
            'total_ttc' => 150.00,
            'pieces' => [],
            'labors' => [],
        ]);

    $response->assertRedirect();
    
    // Verify that the notification was sent to the client
    Notification::assertSentTo($client, DevisDisponibleNotification::class, function ($notification) use ($dossier) {
        return $notification->toMail($dossier->client)->subject === 'Votre devis est disponible - Dossier #DOS-TEST-0001';
    });
});

it('sends DevisModifieNotification when a devis is updated', function () {
    Notification::fake();

    $agent = User::create([
        'name' => 'Agent SAV',
        'email' => 'agent@example.com',
        'password' => bcrypt('password'),
        'role' => 'Agent',
        'actif' => true,
    ]);

    $client = User::create([
        'name' => 'Client Test',
        'email' => 'client@example.com',
        'password' => bcrypt('password'),
        'role' => 'Client',
        'actif' => true,
    ]);

    $appareil = Appareil::create([
        'imei' => '123456789012345',
        'modele' => 'iPhone 13',
        'client_id' => $client->id,
    ]);

    $dossier = Dossier::create([
        'num_dossier' => 'DOS-TEST-0002',
        'client_id' => $client->id,
        'appareil_id' => $appareil->id,
        'statut' => 'EN_ATTENTE_DEVIS',
        'date_reception' => now(),
    ]);

    $devis = Devis::create([
        'dossier_id' => $dossier->id,
        'numero' => 'DEV-TEST-0002',
        'montant_total' => 150.00,
        'frais_mod' => 30.00,
        'remise' => 0.00,
        'statut' => 'EN_ATTENTE',
        'date_creation' => now(),
    ]);

    $response = $this->actingAs($agent)
        ->put(route('devis.update', $devis->id), [
            'total_ttc' => 200.00,
            'frais_mod' => 50.00,
            'pieces' => [],
            'labors' => [],
        ]);

    $response->assertRedirect();
    
    // Verify that the notification was sent to the client
    Notification::assertSentTo($client, DevisModifieNotification::class, function ($notification) use ($dossier) {
        return $notification->toMail($dossier->client)->subject === 'Votre devis a été mis à jour - Dossier #DOS-TEST-0002';
    });
});
