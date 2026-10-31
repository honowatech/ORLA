<?php

namespace Tests\Feature\Tenancy;

use App\Models\Concerns\AppartientAEntreprise;
use App\Models\Entreprise;
use App\Models\ville\Ville;
use App\Models\zone\Zone;
use App\Tenancy\EntrepriseCourante;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use RuntimeException;
use Tests\TestCase;

class IsolationEntreprisesTest extends TestCase
{
    use RefreshDatabase;

    protected EntrepriseCourante $courante;

    protected function setUp(): void
    {
        parent::setUp();
        $this->courante = app(EntrepriseCourante::class);
    }

    protected function entreprise(string $nom): Entreprise
    {
        return Entreprise::create(['name' => $nom, 'statut' => 1, 'essai_jusqu_au' => now()->addDays(14)]);
    }

    protected function ville(): Ville
    {
        return $this->courante->sansFiltre(function () {
            $ville = new Ville;
            $ville->libelle = 'Douala';
            $ville->code = 'DLA';
            $ville->save();

            return $ville;
        });
    }

    protected function zone(string $libelle, Ville $ville): Zone
    {
        $zone = new Zone;
        $zone->libelle = $libelle;
        $zone->id_ville = $ville->id;
        $zone->statut = 1;
        $zone->save();

        return $zone;
    }

    public function test_une_entreprise_ne_voit_que_ses_donnees(): void
    {
        [$a, $b] = [$this->entreprise('Alpha Livraison'), $this->entreprise('Beta Express')];
        $ville = $this->ville();

        $zoneA = $this->courante->pour($a, fn () => $this->zone('Akwa', $ville));
        $zoneB = $this->courante->pour($b, fn () => $this->zone('Bonapriso', $ville));

        $this->courante->pour($a, function () use ($zoneA, $zoneB) {
            $this->assertSame([$zoneA->id], Zone::pluck('id')->all());
            $this->assertNull(Zone::find($zoneB->id));
        });

        $this->courante->pour($b, function () use ($zoneB) {
            $this->assertSame([$zoneB->id], Zone::pluck('id')->all());
        });
    }

    public function test_entreprise_id_est_rempli_a_la_creation(): void
    {
        $a = $this->entreprise('Alpha Livraison');
        $ville = $this->ville();

        $zone = $this->courante->pour($a, fn () => $this->zone('Akwa', $ville));

        $this->assertSame($a->id, (int) $zone->entreprise_id);
    }

    public function test_creer_une_donnee_metier_sans_entreprise_est_refuse(): void
    {
        $ville = $this->ville();

        $this->expectException(RuntimeException::class);
        $this->zone('Akwa', $ville);
    }

    public function test_un_referentiel_commun_est_visible_mais_pas_modifiable(): void
    {
        [$a, $b] = [$this->entreprise('Alpha Livraison'), $this->entreprise('Beta Express')];
        $commune = $this->ville();

        $propreA = $this->courante->pour($a, function () {
            $ville = new Ville;
            $ville->libelle = 'Kribi';
            $ville->code = 'KRI';
            $ville->save();

            return $ville;
        });

        $this->courante->pour($b, function () use ($commune, $propreA) {
            $this->assertNotNull(Ville::find($commune->id));
            $this->assertNull(Ville::find($propreA->id));
        });

        $this->expectException(RuntimeException::class);
        $this->courante->pour($a, function () use ($commune) {
            $ville = Ville::find($commune->id);
            $ville->libelle = 'Douala (modifiée)';
            $ville->save();
        });
    }

    public function test_unique_et_exists_ne_regardent_que_l_entreprise_courante(): void
    {
        [$a, $b] = [$this->entreprise('Alpha Livraison'), $this->entreprise('Beta Express')];
        $ville = $this->ville();
        $zoneA = $this->courante->pour($a, fn () => $this->zone('Akwa', $ville));

        $this->courante->pour($b, function () use ($zoneA) {
            $this->assertTrue(Validator::make(['libelle' => 'Akwa'], ['libelle' => 'unique:zone'])->passes());
            $this->assertTrue(Validator::make(['id' => $zoneA->id], ['id' => 'exists:zone,id'])->fails());
        });

        $this->courante->pour($a, function () use ($zoneA) {
            $this->assertTrue(Validator::make(['libelle' => 'Akwa'], ['libelle' => 'unique:zone'])->fails());
            $this->assertTrue(Validator::make(['id' => $zoneA->id], ['id' => 'exists:zone,id'])->passes());
        });
    }

    public function test_l_email_reste_unique_sur_toute_la_plateforme(): void
    {
        [$a, $b] = [$this->entreprise('Alpha Livraison'), $this->entreprise('Beta Express')];
        $this->courante->pour($a, fn () => \Illuminate\Support\Facades\DB::table('users')->insert([
            'entreprise_id' => $a->id, 'noms' => 'Awa', 'email' => 'awa@example.com', 'password' => 'x',
            'id_type_utilisateur' => $this->typeUtilisateur(),
        ]));

        $this->courante->pour($b, function () {
            $this->assertTrue(Validator::make(['email' => 'awa@example.com'], ['email' => 'unique:users'])->fails());
        });
    }

    public function test_impossible_de_lier_une_donnee_d_une_autre_entreprise(): void
    {
        [$a, $b] = [$this->entreprise('Alpha Livraison'), $this->entreprise('Beta Express')];
        $ville = $this->ville();
        $zoneA = $this->courante->pour($a, fn () => $this->zone('Akwa', $ville));

        $this->expectException(ValidationException::class);
        $this->courante->pour($b, function () use ($zoneA, $ville) {
            $quartier = new \App\Models\quartier\Quartier;
            $quartier->libelle = 'Bonanjo';
            $quartier->id_ville = $ville->id;
            $quartier->id_zone = $zoneA->id;
            $quartier->save();
        });
    }

    public function test_chaque_entreprise_a_son_propre_siege(): void
    {
        [$a, $b] = [$this->entreprise('Alpha Livraison'), $this->entreprise('Beta Express')];
        $this->ville();

        $siegeA = $this->courante->pour($a, fn () => \App\Models\quartier\Quartier::siege());
        $siegeB = $this->courante->pour($b, fn () => \App\Models\quartier\Quartier::siege());

        $this->assertNotSame($siegeA->id, $siegeB->id);
        $this->assertSame($siegeA->id, $this->courante->pour($a, fn () => \App\Models\quartier\Quartier::siege())->id);
    }

    protected function typeUtilisateur(): int
    {
        return \Illuminate\Support\Facades\DB::table('type_utilisateur')->insertGetId(['libelle' => 'Client']);
    }

    public function test_l_acces_depend_de_l_essai_et_de_l_abonnement(): void
    {
        $essai = $this->entreprise('En essai');
        $expiree = Entreprise::create(['name' => 'Expirée', 'statut' => 1, 'essai_jusqu_au' => now()->subDay()]);
        $desactivee = Entreprise::create(['name' => 'Désactivée', 'statut' => 0, 'essai_jusqu_au' => now()->addDays(14)]);

        $this->assertTrue($essai->accesComplet());
        $this->assertFalse($expiree->accesComplet());
        $this->assertFalse($desactivee->accesComplet());
    }

    /**
     * Garde-fou : toute table métier qui a une colonne entreprise_id doit
     * avoir un modèle filtré. Une table oubliée = une fuite entre entreprises.
     */
    public function test_chaque_modele_metier_est_filtre_par_entreprise(): void
    {
        $oublis = [];

        foreach (File::allFiles(app_path('Models')) as $fichier) {
            $classe = 'App\\Models\\'.str_replace(['/', '.php'], ['\\', ''], $fichier->getRelativePathname());
            if (! class_exists($classe) || ! is_subclass_of($classe, \Illuminate\Database\Eloquent\Model::class)) {
                continue;
            }
            $table = (new $classe)->getTable();
            if ($table === 'entreprises' || ! Schema::hasColumn($table, 'entreprise_id')) {
                continue;
            }
            if (! in_array(AppartientAEntreprise::class, class_uses_recursive($classe), true)) {
                $oublis[] = $classe;
            }
        }

        $this->assertSame([], $oublis, 'Modèles non filtrés par entreprise : '.implode(', ', $oublis));
    }
}
