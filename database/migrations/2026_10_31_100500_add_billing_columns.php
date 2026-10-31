<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Facturation : limites des offres, référence de paiement unique par
 * transaction et suivi des relances d'expiration.
 */
return new class extends Migration
{
    public function up(): void
    {
        // Vide = illimité
        Schema::table('super_admin_abonnement', function (Blueprint $table) {
            $table->unsignedInteger('max_coursiers')->nullable();
            $table->unsignedInteger('max_vehicules')->nullable();
            $table->unsignedInteger('max_agents')->nullable();
            $table->unsignedInteger('max_commandes_mois')->nullable();
        });

        Schema::table('super_admin_transaction', function (Blueprint $table) {
            $table->string('reference_paiement', 64)->nullable()->unique();
            $table->datetime('confirme_le')->nullable();
        });

        Schema::table('entreprises', function (Blueprint $table) {
            $table->string('derniere_relance', 64)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('super_admin_abonnement', function (Blueprint $table) {
            $table->dropColumn(['max_coursiers', 'max_vehicules', 'max_agents', 'max_commandes_mois']);
        });
        Schema::table('super_admin_transaction', function (Blueprint $table) {
            $table->dropUnique(['reference_paiement']);
            $table->dropColumn(['reference_paiement', 'confirme_le']);
        });
        Schema::table('entreprises', function (Blueprint $table) {
            $table->dropColumn('derniere_relance');
        });
    }
};
