<?php

namespace Tests\Feature\Tenancy;

use App\Models\Coursiers\Coursiers;
use App\Models\Entreprise;
use App\Models\User;
use App\Tenancy\CurrentEntreprise;
use App\Tenancy\Exceptions\TenantNonResoluException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ScopeTest extends TestCase
{
    use RefreshDatabase;

    private CurrentEntreprise $courante;

    protected function setUp(): void
    {
        parent::setUp();
        $this->courante = app(CurrentEntreprise::class);
    }

    public function test_un_modele_strict_sans_entreprise_courante_leve_une_exception(): void
    {
        $this->expectException(TenantNonResoluException::class);

        Coursiers::count();
    }

    public function test_le_modele_user_tolere_l_absence_d_entreprise_courante(): void
    {
        $this->assertSame(0, User::count());
    }

    public function test_les_lectures_sont_filtrees_par_l_entreprise_courante(): void
    {
        [$a, $b] = Entreprise::factory()->count(2)->create();
        Coursiers::factory()->count(2)->create(['entreprise_id' => $a->id]);
        Coursiers::factory()->create(['entreprise_id' => $b->id]);

        $this->assertSame(2, $this->courante->runAs($a, fn () => Coursiers::count()));
        $this->assertSame(1, $this->courante->runAs($b, fn () => Coursiers::count()));
        $this->assertSame(3, $this->courante->runWithoutTenancy(fn () => Coursiers::count()));
        $this->assertSame(3, $this->courante->runAs($a, fn () => Coursiers::withoutTenancy()->count()));
    }

    public function test_la_creation_renseigne_l_entreprise_courante(): void
    {
        $a = Entreprise::factory()->create();

        $coursier = $this->courante->runAs($a, fn () => Coursiers::query()->forceCreate([
            'noms' => 'Mbappe', 'prenoms' => 'Jean', 'telephone' => '699111111', 'statut' => 1,
        ]));

        $this->assertSame($a->id, (int) $coursier->entreprise_id);
        $this->assertDatabaseHas('coursiers', ['id' => $coursier->id, 'entreprise_id' => $a->id]);
    }

    public function test_la_creation_sans_entreprise_courante_leve_une_exception(): void
    {
        $this->expectException(TenantNonResoluException::class);

        Coursiers::query()->forceCreate(['noms' => 'Sans', 'prenoms' => 'Entreprise', 'telephone' => '699222222', 'statut' => 1]);
    }

    public function test_run_as_et_run_without_tenancy_restaurent_le_contexte_precedent(): void
    {
        [$a, $b] = Entreprise::factory()->count(2)->create();
        $this->courante->set($a);

        $this->courante->runAs($b, function () use ($b) {
            $this->assertSame($b->id, $this->courante->id());
        });
        $this->assertSame($a->id, $this->courante->id());

        $this->courante->runWithoutTenancy(function () {
            $this->assertTrue($this->courante->isBypassed());
            $this->assertFalse($this->courante->has());
        });
        $this->assertFalse($this->courante->isBypassed());
        $this->assertSame($a->id, $this->courante->id());
    }

    public function test_un_enregistrement_d_une_autre_entreprise_est_introuvable(): void
    {
        [$a, $b] = Entreprise::factory()->count(2)->create();
        $coursier = Coursiers::factory()->create(['entreprise_id' => $b->id]);

        $this->assertNotNull($this->courante->runAs($b, fn () => Coursiers::find($coursier->id)));
        $this->assertNull($this->courante->runAs($a, fn () => Coursiers::find($coursier->id)));

        $this->expectException(ModelNotFoundException::class);
        $this->courante->runAs($a, fn () => Coursiers::findOrFail($coursier->id));
    }

    public function test_les_helpers_exposent_l_entreprise_courante(): void
    {
        $a = Entreprise::factory()->create();

        $this->assertNull(entreprise());
        $this->courante->runAs($a, function () use ($a) {
            $this->assertTrue($a->is(entreprise()));
            $this->assertSame($a->id, entreprise_id());
        });

        $this->expectException(TenantNonResoluException::class);
        entreprise_id();
    }
}
