<?php

namespace Tests\Feature\Tenancy;

use App\Models\Entreprise;
use App\Models\Journal;
use App\Models\User;
use App\Models\ville\Ville;
use App\Models\zone\Zone;
use App\Tenancy\EntrepriseCourante;
use App\Tenancy\InscriptionEntreprise;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use ZipArchive;

class PersonnalisationSuiviTest extends TestCase
{
    use RefreshDatabase;

    protected function administrateur(string $nom): User
    {
        $entreprise = Entreprise::create(['name' => $nom, 'statut' => 1, 'essai_jusqu_au' => now()->addDays(14), 'telephone' => '690000000']);
        $user = app(InscriptionEntreprise::class)->creerAdministrateur($entreprise, "Admin $nom", strtolower($nom).'@exemple.cm', null, 'motdepasse');
        $user->markEmailAsVerified();

        return $user;
    }

    /** Commande minimale insérée directement (le formulaire de commande a de nombreuses dépendances). */
    protected function commande(User $user, string $statut = 'encours'): object
    {
        return app(EntrepriseCourante::class)->pour($user->entreprise_id, function () use ($user, $statut) {
            $commande = new \App\Models\commandes\Commandes;
            $commande->id_saver = $user->id;
            $commande->date_commande = now();
            $commande->type_commande = 'simple';
            $commande->date_livraison = now()->addDay();
            $commande->mode_de_paiement = 'coursier';
            $commande->statut = $statut;
            $commande->adresse_livraison = '12 rue secrète';
            $commande->save();

            return $commande;
        });
    }

    public function test_le_destinataire_suit_sa_livraison_sans_compte(): void
    {
        $alpha = $this->administrateur('Alpha');
        $commande = $this->commande($alpha);

        $this->assertNotEmpty($commande->jeton_suivi);
        $this->get($commande->lienSuivi())
            ->assertOk()
            ->assertSee('Alpha')
            ->assertSee('En cours de livraison')
            ->assertDontSee('12 rue secrète');
        $this->get('/suivi/jeton-inexistant')->assertNotFound();
    }

    public function test_le_journal_enregistre_qui_a_fait_quoi(): void
    {
        $alpha = $this->administrateur('Alpha');
        $beta = $this->administrateur('Beta');
        $courante = app(EntrepriseCourante::class);
        $ville = $courante->sansFiltre(function () {
            $ville = new Ville;
            $ville->libelle = 'Douala';
            $ville->code = 'DLA';
            $ville->save();

            return $ville;
        });

        $this->actingAs($alpha);
        $courante->pour($alpha->entreprise_id, function () use ($ville) {
            $zone = new Zone;
            $zone->libelle = 'Akwa';
            $zone->id_ville = $ville->id;
            $zone->statut = 1;
            $zone->save();
            $zone->libelle = 'Akwa Nord';
            $zone->save();
        });

        $entrees = $courante->pour($alpha->entreprise_id, fn () => Journal::where('sujet_table', 'zone')->orderBy('id')->get());
        $this->assertSame(['creation', 'modification'], $entrees->pluck('action')->all());
        $this->assertSame($alpha->id, (int) $entrees[1]->id_user);
        $this->assertSame(['avant' => 'Akwa', 'apres' => 'Akwa Nord'], $entrees[1]->changements['libelle']);

        $this->assertSame(0, $courante->pour($beta->entreprise_id, fn () => Journal::where('sujet_table', 'zone')->count()));
        $this->actingAs($alpha)->get(route('journal'))->assertOk()->assertSee('Akwa Nord');
    }

    public function test_l_administrateur_personnalise_son_espace(): void
    {
        Storage::fake('local');
        $alpha = $this->administrateur('Alpha');

        $this->actingAs($alpha)->put(route('parametres.update'), [
            'name' => 'Alpha Express',
            'fuseau_horaire' => 'Africa/Douala',
            'couleur_principale' => '#ff6600',
            'logo' => UploadedFile::fake()->image('logo.png', 100, 100),
        ])->assertRedirect(route('parametres.edit'));

        $entreprise = Entreprise::find($alpha->entreprise_id);
        $this->assertSame('Alpha Express', $entreprise->name);
        $this->assertSame('#ff6600', $entreprise->couleur_principale);
        Storage::disk('local')->assertExists($entreprise->logo);
        $this->get(route('entreprise.logo', $entreprise->slug))->assertOk();

        $this->actingAs($alpha)->put(route('parametres.update'), [
            'name' => 'Alpha', 'fuseau_horaire' => 'Africa/Douala', 'couleur_principale' => 'red;}</style><script>',
        ])->assertSessionHasErrors('couleur_principale');
    }

    public function test_l_export_ne_contient_que_les_donnees_de_l_entreprise(): void
    {
        $alpha = $this->administrateur('Alpha');
        $beta = $this->administrateur('Beta');

        $reponse = $this->actingAs($alpha)->get(route('export'));
        $reponse->assertOk();

        $zip = new ZipArchive;
        $zip->open($reponse->baseResponse->getFile()->getPathname());
        $users = $zip->getFromName('users.csv');
        $zip->close();

        $this->assertStringContainsString('alpha@exemple.cm', $users);
        $this->assertStringNotContainsString('beta@exemple.cm', $users);
        $this->assertStringNotContainsString('password', $users);
        $this->assertSame(1, DB::table('journal')->where('entreprise_id', $alpha->entreprise_id)->where('action', 'export')->count());
    }
}
