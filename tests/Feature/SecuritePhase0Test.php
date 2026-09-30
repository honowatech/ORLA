<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Tests\Concerns\CreeDesDonnees;
use Tests\TestCase;

/**
 * Non-régression des failles corrigées en phase 0 de l'audit (docs/AUDIT_QUALITE.md).
 */
class SecuritePhase0Test extends TestCase
{
    use CreeDesDonnees;
    use RefreshDatabase;

    public function test_un_client_ne_peut_pas_changer_le_mot_de_passe_d_un_autre_compte(): void
    {
        $alice = $this->client('alice@example.com');
        $bob = $this->client('bob@example.com');

        $this->actingAs($alice)->put(route('Clientpassword.update', $bob->id), [
            'password' => 'ancien-mdp',
            'password_new' => 'pirate123',
            'password_confirm' => 'pirate123',
        ]);

        $this->assertTrue(Hash::check('ancien-mdp', $bob->fresh()->password));
        $this->assertTrue(Hash::check('pirate123', $alice->fresh()->password));
    }

    public function test_un_client_ne_peut_pas_modifier_le_profil_d_un_autre_compte(): void
    {
        $alice = $this->client('alice@example.com');
        $bob = $this->client('bob@example.com');

        $this->actingAs($alice)->put(route('Clientusers.update', $bob->id), [
            'noms' => 'Pirate', 'prenoms' => 'X', 'email' => 'pirate@example.com', 'telephone' => '690000000',
        ]);

        $this->assertSame('bob@example.com', $bob->fresh()->email);
        $this->assertSame('pirate@example.com', $alice->fresh()->email);
    }

    public function test_un_client_ne_voit_ni_ne_modifie_les_commandes_d_un_autre_client(): void
    {
        $alice = $this->client('alice@example.com');
        $bob = $this->client('bob@example.com');
        $commandeBob = $this->commande($bob, $bob->id_client);

        $this->actingAs($alice)->get(route('Clientcommandes.show', $commandeBob))->assertNotFound();
        $this->actingAs($alice)->get(route('Clientcommandes.edit', $commandeBob))->assertNotFound();
        $this->actingAs($alice)->delete(route('Clientcommandes.destroy', $commandeBob), ['statut' => 'annulee'])->assertNotFound();
        $this->actingAs($alice)->delete(route('Clientusers.destroy', $commandeBob))->assertNotFound();

        $this->assertSame('attente', DB::table('commandes')->where('id', $commandeBob)->value('statut'));
    }

    public function test_un_client_peut_seulement_annuler_sa_commande(): void
    {
        $alice = $this->client('alice@example.com');
        $commande = $this->commande($alice, $alice->id_client);

        $this->actingAs($alice)->delete(route('Clientcommandes.destroy', $commande), ['statut' => 'attribue']);
        $this->assertSame('attente', DB::table('commandes')->where('id', $commande)->value('statut'));

        $this->actingAs($alice)->delete(route('Clientcommandes.destroy', $commande), ['statut' => 'annulee']);
        $this->assertSame('annulee', DB::table('commandes')->where('id', $commande)->value('statut'));
    }

    public function test_un_coursier_ne_modifie_que_ses_commandes(): void
    {
        $paul = $this->coursier('paul@example.com');
        $jean = $this->coursier('jean@example.com');
        $commandeJean = $this->commande($jean, null, $jean->id_coursier, 'attribue');

        $this->actingAs($paul)->delete(route('Coursiercommandes.destroy', $commandeJean), ['statut' => 'livre'])->assertNotFound();
        $this->actingAs($paul)->delete(route('Coursierusers.destroy', $commandeJean))->assertNotFound();

        $this->assertSame('attribue', DB::table('commandes')->where('id', $commandeJean)->value('statut'));
    }

    public function test_un_coursier_ne_voit_que_les_clients_de_ses_commandes(): void
    {
        $paul = $this->coursier('paul@example.com');
        $alice = $this->client('alice@example.com');

        $this->actingAs($paul)->get(route('Coursierclients.show', $alice->id_client))->assertNotFound();
        $this->assertFalse(Route::has('Coursierclients.update'));
    }

    public function test_un_visiteur_ne_peut_pas_s_attribuer_un_abonnement(): void
    {
        $this->post(route('Sc-transaction.store'), [
            'id_abonnement' => 1, 'id_client' => 1, 'methode' => 'application',
        ])->assertForbidden();

        $this->assertSame(0, DB::table('super_admin_transaction')->count());
    }

    public function test_le_retour_de_paiement_exige_une_signature_valide(): void
    {
        DB::table('super_admin_api')->insert([
            'name' => 'Monetbill', 'key' => 'cle', 'secret' => 'secret', 'statut' => 1,
        ]);
        $transaction = DB::table('super_admin_transaction')->insertGetId([
            'id_client' => 1, 'id_abonnement' => 1, 'methode' => 'mobile', 'montant' => 10000,
            'date_fin' => now()->addMonths(2), 'statut' => 'waiting',
        ]);

        // La librairie Monetbil lit l'URL courante dans $_SERVER, que les requêtes de test ne renseignent pas.
        $url = '/Sc-transaction.checkpay/'.$transaction.'?transaction_id=x&status=success&payment_ref=x&sign=faux';
        $_SERVER['SERVER_NAME'] = 'localhost';
        $_SERVER['SERVER_PORT'] = 80;
        $_SERVER['REQUEST_URI'] = $url;

        $this->get($url)->assertForbidden();

        $this->assertSame('waiting', DB::table('super_admin_transaction')->where('id', $transaction)->value('statut'));
        $this->assertNotEquals(now()->addMonths(2)->toDateString(), substr((string) DB::table('super_admin_client')->where('id', 1)->value('date_fin'), 0, 10));
    }

    public function test_un_visiteur_ne_voit_pas_les_transactions(): void
    {
        $transaction = DB::table('super_admin_transaction')->insertGetId([
            'id_client' => 1, 'id_abonnement' => 1, 'methode' => 'mobile', 'montant' => 10000, 'statut' => 'success',
        ]);

        $this->get(route('Sc-transaction.show', $transaction))->assertForbidden();
    }

    public function test_un_agent_ne_peut_pas_creer_de_super_admin(): void
    {
        $agent = $this->staff('agent@example.com', 2);

        $this->actingAs($agent)->post(route('users.store'), [
            'noms' => 'Pirate', 'email' => 'pirate@example.com', 'password' => 'motdepasse',
            'type_user' => 1, 'telephone' => '690000001',
        ])->assertForbidden();

        $this->assertFalse(DB::table('users')->where('email', 'pirate@example.com')->exists());
    }

    public function test_un_agent_ne_peut_pas_toucher_a_un_compte_super_admin(): void
    {
        $agent = $this->staff('agent@example.com', 2);
        $superAdmin = $this->staff('admin@example.com', 1);

        $this->actingAs($agent)->put(route('users.update', $superAdmin->id), [
            'noms' => 'Pirate', 'email' => 'pirate@example.com', 'telephone' => '690000001',
        ])->assertForbidden();
        $this->actingAs($agent)->delete(route('users.destroy', $superAdmin->id))->assertForbidden();
        $this->actingAs($agent)->delete(route('password.destroy', $superAdmin->id))->assertForbidden();

        $superAdmin->refresh();
        $this->assertSame('admin@example.com', $superAdmin->email);
        $this->assertEquals(1, $superAdmin->statut);
        $this->assertTrue(Hash::check('ancien-mdp', $superAdmin->password));
    }

    public function test_la_reinitialisation_genere_un_mot_de_passe_aleatoire(): void
    {
        $superAdmin = $this->staff('admin@example.com', 1);
        $agent = $this->staff('agent@example.com', 2);

        $this->actingAs($superAdmin)->delete(route('password.destroy', $agent->id));

        $hash = $agent->fresh()->password;
        $this->assertFalse(Hash::check('ancien-mdp', $hash));
        $this->assertFalse(Hash::check('11111111', $hash));
        $this->assertMatchesRegularExpression('/[A-Za-z0-9]{12}/', session('message'));
    }
}
