<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\Concerns\CreeDesDonnees;
use Tests\TestCase;

/**
 * Bugs existants relevés par Larastan en phase 2 et corrigés.
 */
class BugsCorrigesTest extends TestCase
{
    use CreeDesDonnees;
    use RefreshDatabase;

    public function test_un_abonnement_a_l_heure_calcule_sa_date_de_fin(): void
    {
        $fin = Sa_prochaine_date_paie('heure', 3, '2026-01-01 10:00:00', 1);

        $this->assertSame('2026-01-01 13:00:00', $fin->format('Y-m-d H:i:s'));
    }

    public function test_un_client_qui_modifie_son_profil_met_a_jour_sa_fiche_client(): void
    {
        $alice = $this->client('alice@example.com');

        $this->actingAs($alice)->put(route('Clientusers.update', $alice->id), [
            'noms' => 'Durand', 'prenoms' => 'Alice', 'email' => 'alice@example.com', 'telephone' => '690000123',
        ])->assertRedirect(route('Clientusers.show', $alice->id));

        $fiche = DB::table('clients')->where('id', $alice->id_client)->first();
        $this->assertSame('Durand', $fiche->noms);
        $this->assertSame('690000123', $fiche->telephone);
    }

    public function test_un_paiement_par_banque_est_refuse_proprement(): void
    {
        $this->post(route('Sa-transaction.store'), [
            'id_abonnement' => 1, 'methode' => 'bank',
        ])->assertRedirect();

        $this->assertSame(0, DB::table('super_admin_transaction')->count());
    }

    public function test_un_utilisateur_introuvable_renvoie_une_404(): void
    {
        $admin = $this->staff('admin@example.com', 1);

        $this->actingAs($admin)->get(route('users.show', 'inconnu@example.com'))->assertNotFound();
    }
}
