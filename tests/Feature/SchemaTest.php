<?php

namespace Tests\Feature;

use App\Console\Commands\TenancyAudit;
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
        foreach (['users', 'type_utilisateur', 'type_client', 'agents', 'coursiers', 'clients', 'ville', 'zone', 'quartier', 'commandes', 'entreprises', 'abonnement_transactions', 'super_admin_abonnement'] as $table) {
            $this->assertTrue(Schema::hasTable($table), "Table manquante : $table");
        }

        $this->assertFalse(Schema::hasTable('super_admin_client'));
        $this->assertFalse(Schema::hasTable('super_admin_transaction'));
    }

    public function test_les_nouvelles_colonnes_de_users_existent(): void
    {
        $this->assertTrue(Schema::hasColumn('users', 'email_verified_at'));
        $this->assertTrue(Schema::hasColumn('users', 'doit_changer_mdp'));
    }

    public function test_chaque_table_metier_porte_la_colonne_entreprise_id(): void
    {
        foreach (TenancyAudit::TABLES as $table) {
            $this->assertTrue(Schema::hasColumn($table, 'entreprise_id'), "Colonne entreprise_id manquante : $table");
        }

        $this->assertFalse(Schema::hasColumn('ville', 'entreprise_id'), 'La table ville est un référentiel partagé.');
    }

    public function test_la_table_entreprises_porte_les_colonnes_attendues(): void
    {
        foreach (['slug', 'email', 'logo_path', 'pays', 'devise', 'prefixe_telephone', 'essai_fin', 'id_quartier_siege', 'tarif_defaut', 'parametres', 'statut', 'date_fin'] as $colonne) {
            $this->assertTrue(Schema::hasColumn('entreprises', $colonne), "Colonne manquante : entreprises.$colonne");
        }

        foreach (['entreprise_id', 'reference', 'payment_id', 'payload'] as $colonne) {
            $this->assertTrue(Schema::hasColumn('abonnement_transactions', $colonne), "Colonne manquante : abonnement_transactions.$colonne");
        }

        $this->assertTrue(Schema::hasColumn('boutiques', 'est_siege'));
    }
}
