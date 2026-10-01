<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class SchemaTest extends TestCase
{
    use RefreshDatabase;

    public function test_les_tables_techniques_du_framework_existent(): void
    {
        foreach (['password_reset_tokens', 'sessions', 'cache', 'cache_locks', 'jobs', 'failed_jobs', 'personal_access_tokens'] as $table) {
            $this->assertTrue(Schema::hasTable($table), "Table manquante : $table");
        }
    }

    public function test_les_tables_metier_existent(): void
    {
        foreach (['users', 'type_utilisateur', 'type_client', 'agents', 'coursiers', 'clients', 'ville', 'zone', 'quartier', 'commandes', 'super_admin_client', 'super_admin_abonnement'] as $table) {
            $this->assertTrue(Schema::hasTable($table), "Table manquante : $table");
        }
    }

    public function test_les_nouvelles_colonnes_de_users_existent(): void
    {
        $this->assertTrue(Schema::hasColumn('users', 'email_verified_at'));
        $this->assertTrue(Schema::hasColumn('users', 'doit_changer_mdp'));
    }
}
