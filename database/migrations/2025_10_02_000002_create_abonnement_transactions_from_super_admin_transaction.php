<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

/**
 * Les transactions d'abonnement sont rattachées à une entreprise et reçoivent une
 * référence unique (utilisée comme payment_ref auprès de Monetbil).
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('super_admin_transaction') && ! Schema::hasTable('abonnement_transactions')) {
            Schema::rename('super_admin_transaction', 'abonnement_transactions');
        }

        if (! Schema::hasTable('abonnement_transactions')) {
            Schema::create('abonnement_transactions', function (Blueprint $table) {
                $table->id();
                $table->timestamps();
                $table->softDeletes();
                $table->unsignedInteger('id_client')->nullable();
                $table->unsignedBigInteger('id_abonnement');
                $table->string('methode', 20);
                $table->decimal('montant', 20, 2)->nullable();
                $table->integer('nbre_abonnement')->nullable();
                $table->datetime('date_debut')->nullable();
                $table->datetime('date_fin')->nullable();
                $table->string('statut', 20);
            });
        }

        Schema::table('abonnement_transactions', function (Blueprint $table) {
            if (! Schema::hasColumn('abonnement_transactions', 'entreprise_id')) {
                $table->unsignedBigInteger('entreprise_id')->nullable()->after('id');
                $table->index('entreprise_id');
            }
            if (! Schema::hasColumn('abonnement_transactions', 'reference')) {
                $table->string('reference', 64)->nullable()->after('entreprise_id');
            }
            if (! Schema::hasColumn('abonnement_transactions', 'payment_id')) {
                $table->string('payment_id')->nullable()->after('reference');
            }
            if (! Schema::hasColumn('abonnement_transactions', 'payload')) {
                $table->json('payload')->nullable();
            }
        });

        // Même type que super_admin_abonnement.id (BIGINT UNSIGNED).
        Schema::table('abonnement_transactions', function (Blueprint $table) {
            $table->unsignedBigInteger('id_abonnement')->change();
        });

        DB::table('abonnement_transactions')->whereNull('entreprise_id')->update(['entreprise_id' => DB::raw('id_client')]);

        DB::table('abonnement_transactions')->whereNull('reference')->pluck('id')->each(function ($id) {
            DB::table('abonnement_transactions')->where('id', $id)->update(['reference' => (string) Str::uuid()]);
        });

        Schema::table('abonnement_transactions', function (Blueprint $table) {
            $table->index(['entreprise_id', 'statut'], 'abonnement_transactions_entreprise_statut_index');
        });
    }

    public function down(): void
    {
        Schema::table('abonnement_transactions', function (Blueprint $table) {
            if (DB::getDriverName() !== 'sqlite') {
                $table->dropIndex('abonnement_transactions_entreprise_statut_index');
            }
            $colonnes = array_filter(
                ['entreprise_id', 'reference', 'payment_id', 'payload'],
                fn (string $colonne) => Schema::hasColumn('abonnement_transactions', $colonne)
            );
            if ($colonnes !== []) {
                $table->dropColumn(array_values($colonnes));
            }
        });

        if (Schema::hasTable('abonnement_transactions') && ! Schema::hasTable('super_admin_transaction')) {
            Schema::rename('abonnement_transactions', 'super_admin_transaction');
        }
    }
};
