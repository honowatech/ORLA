<?php

namespace Tests\Feature\Tenancy;

use App\Models\Entreprise;
use App\Models\User;
use App\Models\ville\Ville;
use App\Models\zone\Zone;
use App\Tenancy\EntrepriseCourante;
use App\Tenancy\InscriptionEntreprise;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Isolation vérifiée de bout en bout, à travers les middlewares et les contrôleurs.
 */
class AccesHttpTest extends TestCase
{
    use RefreshDatabase;

    protected function administrateur(string $nom, array $entreprise = []): User
    {
        $entreprise = Entreprise::create(array_merge(['name' => $nom, 'statut' => 1, 'essai_jusqu_au' => now()->addDays(14)], $entreprise));
        $user = app(InscriptionEntreprise::class)->creerAdministrateur($entreprise, "Admin $nom", strtolower($nom).'@exemple.cm', null, 'motdepasse');
        $user->markEmailAsVerified();

        return $user;
    }

    protected function zone(User $user, string $libelle): Zone
    {
        $courante = app(EntrepriseCourante::class);
        $ville = $courante->sansFiltre(function () {
            $ville = Ville::where('libelle', 'Douala')->first() ?? new Ville;
            $ville->libelle = 'Douala';
            $ville->code = 'DLA';
            $ville->save();

            return $ville;
        });

        return $courante->pour($user->entreprise_id, function () use ($libelle, $ville) {
            $zone = new Zone;
            $zone->libelle = $libelle;
            $zone->id_ville = $ville->id;
            $zone->statut = 1;
            $zone->save();

            return $zone;
        });
    }

    public function test_un_admin_ne_peut_pas_ouvrir_la_zone_d_une_autre_entreprise(): void
    {
        $alpha = $this->administrateur('Alpha');
        $beta = $this->administrateur('Beta');
        $zoneAlpha = $this->zone($alpha, 'Akwa');
        $zoneBeta = $this->zone($beta, 'Bonapriso');

        $this->actingAs($alpha)->get(route('zone.edit', $zoneAlpha->id))->assertOk();
        $this->actingAs($alpha)->get(route('zone.edit', $zoneBeta->id))->assertNotFound();
    }

    public function test_un_compte_non_verifie_doit_confirmer_son_email(): void
    {
        $alpha = $this->administrateur('Alpha');
        $alpha->forceFill(['email_verified_at' => null])->save();

        $this->actingAs($alpha)->get(route('home.admin'))->assertRedirect(route('verification.notice'));
    }

    public function test_un_abonnement_expire_passe_en_lecture_seule(): void
    {
        $alpha = $this->administrateur('Alpha', ['essai_jusqu_au' => now()->subDay()]);

        $this->actingAs($alpha)->get(route('mon_abonnement'))->assertOk();
        $this->actingAs($alpha)->post(route('zone.store'), ['libelle' => 'Akwa'])->assertRedirect(route('SuperAdmin.empty_abonnement'));
    }

    public function test_une_entreprise_desactivee_est_bloquee(): void
    {
        $alpha = $this->administrateur('Alpha', ['statut' => 0]);

        $this->actingAs($alpha)->get(route('home.admin'))->assertRedirect(route('SuperAdmin.site_inactif'));
    }

    public function test_pages_demarrage_et_abonnement(): void
    {
        $alpha = $this->administrateur('Alpha');

        $this->actingAs($alpha)->get(route('demarrage'))->assertOk()->assertSee('Bienvenue, Alpha');
        $this->actingAs($alpha)->get(route('mon_abonnement'))->assertOk()->assertSee('Essai gratuit');
    }
}
