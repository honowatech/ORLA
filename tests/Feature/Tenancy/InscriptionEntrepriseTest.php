<?php

namespace Tests\Feature\Tenancy;

use App\Models\Entreprise;
use App\Models\User;
use App\Notifications\InvitationUtilisateur;
use App\Tenancy\EntrepriseCourante;
use App\Tenancy\InscriptionEntreprise;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class InscriptionEntrepriseTest extends TestCase
{
    use RefreshDatabase;

    protected function donnees(array $surcharge = []): array
    {
        return array_merge([
            'entreprise' => 'Alpha Livraison',
            'telephone' => '690000000',
            'ville' => 'Douala',
            'noms' => 'Awa Ngono',
            'email' => 'awa@alpha.cm',
            'password' => 'motdepasse',
            'password_confirmation' => 'motdepasse',
            'conditions' => '1',
        ], $surcharge);
    }

    public function test_une_entreprise_peut_s_inscrire_seule(): void
    {
        Notification::fake();

        $this->get('/inscription')->assertOk();
        $this->post('/inscription', $this->donnees())->assertRedirect(route('demarrage'));

        $entreprise = Entreprise::where('name', 'Alpha Livraison')->firstOrFail();
        $user = User::where('email', 'awa@alpha.cm')->firstOrFail();

        $this->assertAuthenticatedAs($user);
        $this->assertSame($entreprise->id, (int) $user->entreprise_id);
        $this->assertSame($user->id, (int) $entreprise->id_proprietaire);
        $this->assertTrue($entreprise->enEssai());
        $this->assertTrue($entreprise->accesComplet());
        $this->assertFalse($user->hasVerifiedEmail());
        Notification::assertSentTo($user, VerifyEmail::class);
    }

    public function test_l_email_doit_etre_libre_sur_toute_la_plateforme(): void
    {
        Notification::fake();
        $this->post('/inscription', $this->donnees());
        auth()->logout();

        $this->post('/inscription', $this->donnees(['entreprise' => 'Beta Express']))
            ->assertSessionHasErrors('email');
        $this->assertSame(0, Entreprise::where('name', 'Beta Express')->count());
    }

    public function test_deux_inscriptions_donnent_deux_espaces_separes(): void
    {
        Notification::fake();
        $this->post('/inscription', $this->donnees());
        auth()->logout();
        $this->post('/inscription', $this->donnees(['entreprise' => 'Beta Express', 'email' => 'paul@beta.cm']));

        $alpha = Entreprise::where('name', 'Alpha Livraison')->firstOrFail();
        $users = app(EntrepriseCourante::class)->pour($alpha, fn () => \App\Models\users\Users::pluck('email')->all());

        $this->assertSame(['awa@alpha.cm'], $users);
    }

    public function test_le_super_admin_peut_inviter_l_administrateur_d_une_entreprise(): void
    {
        Notification::fake();
        $entreprise = Entreprise::create(['name' => 'Gamma Transport', 'statut' => 1]);
        $service = app(InscriptionEntreprise::class);

        $user = $service->creerAdministrateur($entreprise, 'Jean Mballa', 'jean@gamma.cm');
        $service->inviter($user);

        $this->assertSame($user->id, (int) $entreprise->fresh()->id_proprietaire);
        Notification::assertSentTo($user, InvitationUtilisateur::class);
    }
}
