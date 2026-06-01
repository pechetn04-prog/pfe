<?php

use App\Models\User;
use App\Models\TarifMo;

it('requires validation when storing a new main d\'œuvre tariff', function () {
    $admin = User::create([
        'name' => 'Admin SAV',
        'email' => 'admin@example.com',
        'password' => bcrypt('password'),
        'role' => 'Admin',
        'actif' => true,
    ]);

    // Send invalid request (negative amount, empty type)
    $response = $this->actingAs($admin)
        ->from(route('admin.tarifs_mo.index'))
        ->post(route('admin.tarifs_mo.store'), [
            'type_intervention' => '',
            'montant' => -10,
        ]);

    $response->assertRedirect(route('admin.tarifs_mo.index'));
    $response->assertSessionHasErrors(['type_intervention', 'montant']);
});

it('successfully stores a valid main d\'œuvre tariff', function () {
    $admin = User::create([
        'name' => 'Admin SAV',
        'email' => 'admin@example.com',
        'password' => bcrypt('password'),
        'role' => 'Admin',
        'actif' => true,
    ]);

    $response = $this->actingAs($admin)
        ->post(route('admin.tarifs_mo.store'), [
            'type_intervention' => 'Diagnostic Avancé',
            'montant' => 45.00,
        ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('tarif_mos', [
        'type_intervention' => 'Diagnostic Avancé',
        'montant' => 45.00,
    ]);
});

it('requires validation when updating a main d\'œuvre tariff', function () {
    $admin = User::create([
        'name' => 'Admin SAV',
        'email' => 'admin@example.com',
        'password' => bcrypt('password'),
        'role' => 'Admin',
        'actif' => true,
    ]);

    $tarifMo = TarifMo::create([
        'type_intervention' => 'Ancien Type',
        'montant' => 20.00,
    ]);

    $response = $this->actingAs($admin)
        ->from(route('admin.tarifs_mo.index'))
        ->put(route('admin.tarifs_mo.update', $tarifMo->id), [
            'type_intervention' => '',
            'montant' => -5,
        ]);

    $response->assertRedirect(route('admin.tarifs_mo.index'));
    $response->assertSessionHasErrors(['type_intervention', 'montant']);
});

it('successfully updates a valid main d\'œuvre tariff', function () {
    $admin = User::create([
        'name' => 'Admin SAV',
        'email' => 'admin@example.com',
        'password' => bcrypt('password'),
        'role' => 'Admin',
        'actif' => true,
    ]);

    $tarifMo = TarifMo::create([
        'type_intervention' => 'Ancien Type',
        'montant' => 20.00,
    ]);

    $response = $this->actingAs($admin)
        ->put(route('admin.tarifs_mo.update', $tarifMo->id), [
            'type_intervention' => 'Nouveau Type',
            'montant' => 30.00,
        ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('tarif_mos', [
        'id' => $tarifMo->id,
        'type_intervention' => 'Nouveau Type',
        'montant' => 30.00,
    ]);
});
