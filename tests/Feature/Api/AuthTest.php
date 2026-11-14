<?php

namespace Tests\Feature\Api;

use Illuminate\Support\Facades\DB;

class AuthTest extends ApiTestCase
{
    public function test_ping_repond_sans_authentification(): void
    {
        $this->getJson('/api/v1/ping')->assertOk()->assertJson(['status' => 'ok', 'version' => 'v1']);
    }

    public function test_connexion_renvoie_un_jeton_et_le_profil(): void
    {
        $this->coursier(['email' => 'paul@example.com', 'password' => 'secret123']);

        $this->postJson('/api/v1/auth/login', [
            'email' => 'paul@example.com',
            'password' => 'secret123',
            'device_name' => 'Pixel 8',
        ])
            ->assertOk()
            ->assertJsonStructure(['token', 'token_type', 'user' => ['id', 'noms', 'email', 'role', 'coursier' => ['id', 'noms']]])
            ->assertJsonPath('user.role', 'coursier')
            ->assertJsonMissingPath('user.password');
    }

    public function test_connexion_refusee_avec_mauvais_mot_de_passe(): void
    {
        $this->coursier(['email' => 'paul@example.com']);

        $this->postJson('/api/v1/auth/login', [
            'email' => 'paul@example.com',
            'password' => 'mauvais',
            'device_name' => 'Pixel 8',
        ])->assertStatus(422)->assertJsonValidationErrors('email');
    }

    public function test_connexion_refusee_si_compte_desactive(): void
    {
        $this->coursier(['email' => 'paul@example.com', 'statut' => 0]);

        $this->postJson('/api/v1/auth/login', [
            'email' => 'paul@example.com',
            'password' => 'secret123',
            'device_name' => 'Pixel 8',
        ])->assertForbidden()->assertJsonPath('code', 'compte_inactif');
    }

    public function test_routes_protegees_sans_jeton_renvoient_401_en_json(): void
    {
        $this->get('/api/v1/auth/me')->assertUnauthorized()->assertJsonPath('code', 'non_authentifie');
    }

    public function test_me_et_deconnexion(): void
    {
        $user = $this->client();
        $token = $user->createToken('test')->plainTextToken;

        $this->withToken($token)->getJson('/api/v1/auth/me')
            ->assertOk()
            ->assertJsonPath('data.role', 'client')
            ->assertJsonPath('data.client.prenoms', 'Marie');

        $this->withToken($token)->postJson('/api/v1/auth/logout')->assertOk();

        $this->assertSame(0, $user->tokens()->count());
    }

    public function test_changement_de_mot_de_passe(): void
    {
        $user = $this->client(['password' => 'secret123']);
        $user->createToken('autre appareil');
        $token = $user->createToken('test')->plainTextToken;

        $this->withToken($token)->putJson('/api/v1/auth/password', [
            'current_password' => 'faux',
            'password' => 'nouveau123',
            'password_confirmation' => 'nouveau123',
        ])->assertStatus(422)->assertJsonValidationErrors('current_password');

        $this->withToken($token)->putJson('/api/v1/auth/password', [
            'current_password' => 'secret123',
            'password' => 'nouveau123',
            'password_confirmation' => 'nouveau123',
        ])->assertOk();

        $this->assertSame(1, $user->tokens()->count(), 'Les autres appareils doivent être déconnectés.');
    }

    public function test_compte_desactive_apres_connexion_perd_l_acces(): void
    {
        $user = $this->client();
        $token = $user->createToken('test')->plainTextToken;
        $user->statut = 0;
        $user->save();

        $this->withToken($token)->getJson('/api/v1/auth/me')->assertForbidden()->assertJsonPath('code', 'compte_inactif');
    }

    public function test_abonnement_expire_renvoie_402(): void
    {
        DB::table('super_admin_client')->update(['date_fin' => now()->subYear()]);

        $this->postJson('/api/v1/auth/login', [
            'email' => 'x@example.com', 'password' => 'x', 'device_name' => 'x',
        ])->assertStatus(402)->assertJsonPath('code', 'abonnement_expire');
    }
}
