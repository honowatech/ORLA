<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Contraintes d'intégrité sur les entreprises et leurs transactions, posées après
 * le remplissage des données. Les références orphelines sont neutralisées avant
 * la création des clés étrangères (base MySQL de production).
 */
return new class extends Migration
{
    public function up(): void
    {
        // Références orphelines → NULL (les colonnes sont nullable).
        DB::table('abonnement_transactions')
            ->whereNotNull('entreprise_id')
            ->whereNotIn('entreprise_id', DB::table('entreprises')->select('id'))
            ->update(['entreprise_id' => null]);

        DB::table('entreprises')
            ->whereNotNull('id_abonnement')
            ->whereNotIn('id_abonnement', DB::table('super_admin_abonnement')->select('id'))
            ->update(['id_abonnement' => null]);

        DB::table('entreprises')
            ->whereNotNull('id_quartier_siege')
            ->whereNotIn('id_quartier_siege', DB::table('quartier')->select('id'))
            ->update(['id_quartier_siege' => null]);

        Schema::table('entreprises', function (Blueprint $table) {
            $table->unique('slug', 'entreprises_slug_unique');
            $table->foreign('id_abonnement', 'entreprises_id_abonnement_foreign')
                ->references('id')->on('super_admin_abonnement')->restrictOnDelete();
            $table->foreign('id_quartier_siege', 'entreprises_id_quartier_siege_foreign')
                ->references('id')->on('quartier')->nullOnDelete();
        });

        Schema::table('abonnement_transactions', function (Blueprint $table) {
            $table->unique('reference', 'abonnement_transactions_reference_unique');
            $table->foreign('entreprise_id', 'abonnement_transactions_entreprise_id_foreign')
                ->references('id')->on('entreprises')->restrictOnDelete();
        });
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        Schema::table('abonnement_transactions', function (Blueprint $table) {
            $table->dropForeign('abonnement_transactions_entreprise_id_foreign');
            $table->dropUnique('abonnement_transactions_reference_unique');
        });

        Schema::table('entreprises', function (Blueprint $table) {
            $table->dropForeign('entreprises_id_quartier_siege_foreign');
            $table->dropForeign('entreprises_id_abonnement_foreign');
            $table->dropUnique('entreprises_slug_unique');
        });
    }
};
