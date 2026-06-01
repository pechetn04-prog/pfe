<?php

use App\Models\User;
use App\Models\Dossier;
use App\Models\Appareil;

it('requires validation when storing a new dossier message', function () {
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
        'num_dossier' => 'DOS-TEST-0003',
        'client_id' => $client->id,
        'appareil_id' => $appareil->id,
        'statut' => 'EN_DIAGNOSTIC',
        'date_reception' => now(),
    ]);

    $response = $this->actingAs($client)
        ->from(route('client.ticket', $dossier->id))
        ->post(route('dossiers.messages.store', $dossier->id), [
            'message' => '',
            'type' => 'invalid_type',
        ]);

    $response->assertRedirect(route('client.ticket', $dossier->id));
    $response->assertSessionHasErrors(['message', 'type']);
});

it('successfully stores a valid dossier message', function () {
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
        'num_dossier' => 'DOS-TEST-0004',
        'client_id' => $client->id,
        'appareil_id' => $appareil->id,
        'statut' => 'EN_DIAGNOSTIC',
        'date_reception' => now(),
    ]);

    $response = $this->actingAs($client)
        ->post(route('dossiers.messages.store', $dossier->id), [
            'message' => 'Mon écran a aussi des rayures',
            'type' => 'public',
        ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('dossier_messages', [
        'dossier_id' => $dossier->id,
        'message' => 'Mon écran a aussi des rayures',
    ]);
});
