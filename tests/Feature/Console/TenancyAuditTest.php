<?php

namespace Tests\Feature\Console;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class TenancyAuditTest extends TestCase
{
    use RefreshDatabase;

    public function test_l_audit_passe_sur_les_donnees_de_demonstration(): void
    {
        $this->seed();

        $this->artisan('tenancy:audit')->assertSuccessful();
    }

    public function test_l_audit_echoue_sur_une_ligne_orpheline(): void
    {
        $this->seed();
        DB::table('coursiers')->insert([
            'noms' => 'Orphelin', 'prenoms' => 'Sans entreprise', 'telephone' => '600000000',
            'statut' => 1, 'entreprise_id' => null, 'created_at' => now(), 'updated_at' => now(),
        ]);

        $this->artisan('tenancy:audit')->assertFailed();
    }

    public function test_l_audit_echoue_sur_une_reference_croisee(): void
    {
        $this->seed();
        $autre = DB::table('entreprises')->insertGetId([
            'name' => 'Autre', 'slug' => 'autre', 'statut' => 1, 'created_at' => now(), 'updated_at' => now(),
        ]);
        DB::table('coursiers')->update(['entreprise_id' => $autre]);

        $this->artisan('tenancy:audit')->assertFailed();
    }
}
