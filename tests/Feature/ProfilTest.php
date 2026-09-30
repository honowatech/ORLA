<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\Concerns\CreeDesDonnees;
use Tests\TestCase;

/**
 * Profil et mot de passe, communs aux espaces client et coursier
 * (traits GereSonProfil et ModifieSonMotDePasse).
 */
class ProfilTest extends TestCase
{
    use CreeDesDonnees;
    use RefreshDatabase;

    public function test_le_coursier_modifie_son_profil_et_sa_fiche(): void
    {
        $paul = $this->coursier('paul@example.com');

        $this->actingAs($paul)->put(route('Coursierusers.update', $paul->id), [
            'noms' => 'Mbarga', 'prenoms' => 'Paul', 'email' => 'paul@example.com', 'telephone' => '6 90 00 01 11',
        ])->assertRedirect(route('Coursierusers.show', $paul->id));

        $this->assertSame('Mbarga Paul', $paul->fresh()->noms);
        $this->assertSame('690000111', DB::table('coursiers')->where('id', $paul->id_coursier)->value('telephone'));
    }

    public function test_un_email_deja_utilise_est_refuse(): void
    {
        $alice = $this->client('alice@example.com');
        $this->client('bob@example.com');

        $this->actingAs($alice)->put(route('Clientusers.update', $alice->id), [
            'noms' => 'Alice', 'prenoms' => 'X', 'email' => 'bob@example.com', 'telephone' => '690000222',
        ])->assertSessionHasErrors('email');

        $this->assertSame('alice@example.com', $alice->fresh()->email);
    }

    public function test_le_client_change_son_mot_de_passe_et_reste_dans_son_espace(): void
    {
        $alice = $this->client('alice@example.com');

        $this->actingAs($alice)->put(route('Clientpassword.update', $alice->id), [
            'password' => 'ancien-mdp', 'password_new' => 'nouveau-mdp', 'password_confirm' => 'nouveau-mdp',
        ])->assertRedirect(route('Clientusers.show', $alice->id));

        $this->assertTrue(Hash::check('nouveau-mdp', $alice->fresh()->password));
    }

    public function test_un_mauvais_mot_de_passe_actuel_est_refuse(): void
    {
        $paul = $this->coursier('paul@example.com');

        $this->actingAs($paul)->put(route('Coursierpassword.update', $paul->id), [
            'password' => 'faux', 'password_new' => 'nouveau-mdp', 'password_confirm' => 'nouveau-mdp',
        ]);

        $this->assertTrue(Hash::check('ancien-mdp', $paul->fresh()->password));
    }
}
