<?php

use App\Models\User;
use App\Models\Piece;

it('requires admin role to delete a piece', function () {
    $nonAdmin = User::create([
        'name' => 'Agent SAV',
        'email' => 'agent@example.com',
        'password' => bcrypt('password'),
        'role' => 'Agent',
        'actif' => true,
    ]);

    $piece = Piece::create([
        'nom' => 'Ecran LCD iPhone 13',
        'reference' => 'LCD-IP13',
        'categorie' => 'Ecran',
        'quantite' => 10,
        'prix_unitaire' => 89.90,
        'seuil_alerte' => 2,
    ]);

    // An agent should not be authorized to delete
    $response = $this->actingAs($nonAdmin)
        ->delete(route('stock.destroy', $piece->id));

    $response->assertStatus(403);
    $this->assertDatabaseHas('pieces', ['id' => $piece->id]);
});

it('successfully deletes a piece when user is admin', function () {
    $admin = User::create([
        'name' => 'Admin SAV',
        'email' => 'admin@example.com',
        'password' => bcrypt('password'),
        'role' => 'Admin',
        'actif' => true,
    ]);

    $piece = Piece::create([
        'nom' => 'Ecran LCD iPhone 13',
        'reference' => 'LCD-IP13',
        'categorie' => 'Ecran',
        'quantite' => 10,
        'prix_unitaire' => 89.90,
        'seuil_alerte' => 2,
    ]);

    $response = $this->actingAs($admin)
        ->delete(route('stock.destroy', $piece->id));

    $response->assertRedirect(route('stock.index'));
    $response->assertSessionHas('success', 'La pièce a été retirée définitivement du stock.');
    $this->assertDatabaseMissing('pieces', ['id' => $piece->id]);
});
